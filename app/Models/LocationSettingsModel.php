<?php

namespace App\Models;

use CodeIgniter\Model;

class LocationSettingsModel extends Model
{
    protected $table = 'location_settings';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'latitude',
        'longitude',
        'radius',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get the current location settings
     * @return array|null
     */
    public function getSettings()
    {
        return $this->first();
    }
}

