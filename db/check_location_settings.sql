-- Query to check current location settings
-- Run this to verify your saved location coordinates

SELECT 
    id,
    latitude,
    longitude,
    radius,
    created_at,
    updated_at
FROM location_settings;

-- If you need to manually update the location:
-- UPDATE location_settings 
-- SET latitude = 21.1929, longitude = 72.7984, radius = 5.00 
-- WHERE id = 1;

