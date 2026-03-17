<?php

namespace App\Controllers\Api;

use App\Models\LocationSettingsModel;
use CodeIgniter\RESTful\ResourceController;
use App\Services\AuthService;
use CodeIgniter\Config\Services;

class LocationSettingsController extends ResourceController
{
    protected $cityModel;
    protected $countryModel;
    protected $stateModel;
    protected $locationSettingsModel;
    protected $authService;

    public function __construct()
    {
        $this->cityModel = new \App\Models\CityModel();
        $this->countryModel = new \App\Models\CountryModel();
        $this->stateModel = new \App\Models\StateModel();
        $this->locationSettingsModel = new LocationSettingsModel();
        $this->authService = Services::auth($this->request);
    }

    /**
     * Get current location settings
     */
    public function getSettings()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        if (!in_array($user->role, ['admin', 'hr'])) {
            return $this->respond(['status' => 'error', 'message' => 'Access denied'], 403);
        }

        $settings = $this->locationSettingsModel->getSettings();
        
        if (!$settings) {
            return $this->respond([
                'status' => 'success',
                'data' => [
                    'latitude' => 0,
                    'longitude' => 0,
                    'radius' => 0
                ]
            ]);
        }

        return $this->respond([
            'status' => 'success',
            'data' => [
                'id' => $settings['id'],
                'latitude' => (float)$settings['latitude'],
                'longitude' => (float)$settings['longitude'],
                'radius' => (float)$settings['radius']
            ]
        ]);
    }

    /**
     * Update location settings (admin only)
     */
    public function updateSettings()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        if ($user->role !== 'admin') {
            return $this->respond(['status' => 'error', 'message' => 'Only admin can update location settings'], 403);
        }

        $json = $this->request->getJSON(true);
        $latitude = $json['latitude'] ?? null;
        $longitude = $json['longitude'] ?? null;
        $radius = $json['radius'] ?? null;

        // Validation
        if ($latitude === null || $longitude === null || $radius === null) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Latitude, longitude, and radius are required'
            ], 400);
        }

        if (!is_numeric($latitude) || !is_numeric($longitude) || !is_numeric($radius)) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Latitude, longitude, and radius must be numeric values'
            ], 400);
        }

        if ($latitude < -90 || $latitude > 90) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Latitude must be between -90 and 90'
            ], 400);
        }

        if ($longitude < -180 || $longitude > 180) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Longitude must be between -180 and 180'
            ], 400);
        }

        if ($radius < 0) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Radius must be 0 or greater'
            ], 400);
        }

        if ($radius > 10) {
            return $this->respond([
                'status' => 'error',
                'message' => 'Maximum allowed radius is 10 meters'
            ], 400);
        }

        // Get existing settings or create new
        $existing = $this->locationSettingsModel->getSettings();
        
        // Ensure values are properly cast to float for database storage
        $data = [
            'latitude' => (float)$latitude,
            'longitude' => (float)$longitude,
            'radius' => (float)$radius
        ];

        if ($existing) {
            $updated = $this->locationSettingsModel->update($existing['id'], $data);
            if (!$updated) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Failed to update location settings',
                    'errors' => $this->locationSettingsModel->errors()
                ], 500);
            }
        } else {
            $insertId = $this->locationSettingsModel->insert($data);
            if (!$insertId) {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'Failed to save location settings',
                    'errors' => $this->locationSettingsModel->errors()
                ], 500);
            }
        }

        // Verify the saved data
        $savedSettings = $this->locationSettingsModel->getSettings();

        return $this->respond([
            'status' => 'success',
            'message' => 'Location settings updated successfully',
            'data' => [
                'latitude' => (float)$savedSettings['latitude'],
                'longitude' => (float)$savedSettings['longitude'],
                'radius' => (float)$savedSettings['radius']
            ]
        ]);
    }

    /**
     * Get location settings for check-in verification (public for employees)
     */
    public function getSettingsForCheckIn()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $settings = $this->locationSettingsModel->getSettings();
        
        if (!$settings) {
            return $this->respond([
                'status' => 'success',
                'location_enabled' => false,
                'data' => null
            ]);
        }

        $locationEnabled = ($settings['latitude'] != 0 || $settings['longitude'] != 0);

        return $this->respond([
            'status' => 'success',
            'location_enabled' => $locationEnabled,
            'data' => $locationEnabled ? [
                'latitude' => (float)$settings['latitude'],
                'longitude' => (float)$settings['longitude'],
                'radius' => (float)$settings['radius']
            ] : null
        ]);
    }

    public function getLocationByIP()
    {
        // Get user's IP address
        $ipAddress = $this->request->getIPAddress();
        
        // For local testing, use a public IP (you can remove this in production)
        if ($ipAddress === '::1' || $ipAddress === '127.0.0.1') {
            $ipAddress = $this->request->getServer('HTTP_X_FORWARDED_FOR') ?? 
                         $this->request->getServer('REMOTE_ADDR') ?? 
                         '8.8.8.8'; // Fallback IP for testing
        }

        try {
            // Call IP geolocation API
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://ip-api.com/json/{$ipAddress}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200 || !$response) {
                throw new \Exception('Failed to fetch location data');
            }

            $locationData = json_decode($response, true);

            if (!$locationData || $locationData['status'] !== 'success') {
                throw new \Exception('Invalid location data');
            }

            // Extract location details
            $countryName = $locationData['country'] ?? null;
            $stateName = $locationData['regionName'] ?? null;
            $cityName = $locationData['city'] ?? null;

            // Find matching records in database
            $country = $this->countryModel
                ->like('country_name', $countryName, 'both')
                ->first();

            $state = null;
            if ($country) {
                $state = $this->stateModel
                    ->like('state_name', $stateName, 'both')
                    ->first();
            }

            $city = null;
            if ($country) {
                $city = $this->cityModel
                    ->like('city_name', $cityName, 'both')
                    ->where('country_id', $country['id'])
                    ->first();
            }

            return $this->respond([
                'status' => true,
                'message' => 'Location detected successfully',
                'data' => [
                    'ip_address' => $ipAddress,
                    'detected_location' => [
                        'country' => $countryName,
                        'state' => $stateName,
                        'city' => $cityName
                    ],
                    'matched_ids' => [
                        'country_id' => $country['id'] ?? null,
                        'state_id' => $state['id'] ?? null,
                        'city_id' => $city['id'] ?? null
                    ],
                    'current_date' => date('Y-m-d')
                ]
            ]);

        } catch (\Exception $e) {
            return $this->respond([
                'status' => false,
                'message' => 'Could not detect location: ' . $e->getMessage(),
                'data' => [
                    'country_id' => null,
                    'state_id' => null,
                    'city_id' => null,
                    'current_date' => date('Y-m-d')
                ]
            ]);
        }
    }
}

