<?php

namespace App\Services;

class LocationService
{
    public function isWithinRadius($department, $userLat, $userLng, $radiusInMeters = null)
    {
        $radius = $radiusInMeters ?? $department->radius;
        
        $earthRadius = 6371000; // Earth radius in meters
        
        $latDiff = deg2rad($userLat - $department->location_lat);
        $lngDiff = deg2rad($userLng - $department->location_lng);
        
        $a = sin($latDiff / 2) * sin($latDiff / 2) +
             cos(deg2rad($department->location_lat)) * cos(deg2rad($userLat)) *
             sin($lngDiff / 2) * sin($lngDiff / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;
        
        return $distance <= $radius;
    }

    public function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000;
        
        $latDiff = deg2rad($lat2 - $lat1);
        $lngDiff = deg2rad($lng2 - $lng1);
        
        $a = sin($latDiff / 2) * sin($latDiff / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lngDiff / 2) * sin($lngDiff / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }
}