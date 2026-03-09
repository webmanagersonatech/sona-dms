<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    public static function log($action, $description, $userId = null, $fileId = null, $transferId = null, $metadata = [])
    {
        try {
            $location = self::getLocation();
            
            ActivityLog::create([
                'user_id' => $userId,
                'file_id' => $fileId,
                'transfer_id' => $transferId,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'device_id' => session('device_id'),
                'location' => $location['location'],
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
                'action' => $action,
                'description' => $description,
                'metadata' => $metadata,
                'performed_at' => now(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to log activity', [
                'error' => $e->getMessage(),
                'action' => $action,
            ]);
        }
    }

    private static function getLocation()
    {
        $ip = Request::ip();
        
        // For demo, return mock location
        // In production, use IP geolocation service
        if ($ip === '127.0.0.1') {
            return [
                'location' => 'Localhost',
                'latitude' => null,
                'longitude' => null,
            ];
        }

        // Example: Use ipinfo.io (you need API key)
        try {
            $response = @file_get_contents("http://ipinfo.io/{$ip}/json");
            if ($response) {
                $data = json_decode($response, true);
                $loc = explode(',', $data['loc'] ?? '0,0');
                
                return [
                    'location' => $data['city'] . ', ' . $data['country'],
                    'latitude' => $loc[0] ?? null,
                    'longitude' => $loc[1] ?? null,
                ];
            }
        } catch (\Exception $e) {
            // Silent fail
        }

        return [
            'location' => 'Unknown',
            'latitude' => null,
            'longitude' => null,
        ];
    }
}