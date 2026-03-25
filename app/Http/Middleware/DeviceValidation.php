<?php

namespace App\Http\Middleware;

use App\Models\DeviceSession;
use Closure;
use Illuminate\Http\Request;

class DeviceValidation
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Generate device ID if not exists
        $deviceId = $this->getDeviceId($request);

        // Check if device is registered
        $deviceSession = DeviceSession::where('user_id', $user->id)
            ->where('device_id', $deviceId)
            ->first();

        if (!$deviceSession) {
            // New device - require OTP verification
            session(['new_device_detected' => true]);
            session(['device_verification_required' => true]);
            
            // Store device info for registration
            session([
                'pending_device' => [
                    'device_id' => $deviceId,
                    'device_name' => $this->getDeviceName($request),
                    'device_type' => $this->getDeviceType($request),
                    'browser' => $this->getBrowser($request),
                    'os' => $this->getOS($request),
                    'ip_address' => $request->ip(),
                ]
            ]);

            return redirect()->route('device.verify')
                ->with('warning', 'New device detected. Verification required.');
        }

        // Update last activity
        $deviceSession->update([
            'last_activity_at' => now(),
            'ip_address' => $request->ip(),
        ]);

        // Store device ID in session
        session(['device_id' => $deviceId]);

        return $next($request);
    }

    private function getDeviceId(Request $request)
    {
        $deviceId = session('device_id');

        if (!$deviceId) {
            $fingerprint = $request->userAgent() . $request->ip();
            $deviceId = hash('sha256', $fingerprint);
        }

        return $deviceId;
    }

    private function getDeviceName(Request $request)
    {
        // Extract device name from user agent
        $ua = $request->userAgent();
        
        if (strpos($ua, 'Windows')) {
            return 'Windows Device';
        } elseif (strpos($ua, 'Macintosh')) {
            return 'Mac Device';
        } elseif (strpos($ua, 'Linux')) {
            return 'Linux Device';
        } elseif (strpos($ua, 'iPhone') || strpos($ua, 'iPad')) {
            return 'iOS Device';
        } elseif (strpos($ua, 'Android')) {
            return 'Android Device';
        }
        
        return 'Unknown Device';
    }

    private function getDeviceType(Request $request)
    {
        $ua = $request->userAgent();
        
        if (strpos($ua, 'Mobile')) {
            return 'mobile';
        } elseif (strpos($ua, 'Tablet')) {
            return 'tablet';
        }
        
        return 'desktop';
    }

    private function getBrowser(Request $request)
    {
        $ua = $request->userAgent();
        
        if (strpos($ua, 'Chrome')) {
            return 'Chrome';
        } elseif (strpos($ua, 'Firefox')) {
            return 'Firefox';
        } elseif (strpos($ua, 'Safari')) {
            return 'Safari';
        } elseif (strpos($ua, 'Edge')) {
            return 'Edge';
        } elseif (strpos($ua, 'Opera')) {
            return 'Opera';
        }
        
        return 'Unknown';
    }

    private function getOS(Request $request)
    {
        $ua = $request->userAgent();
        
        if (strpos($ua, 'Windows')) {
            return 'Windows';
        } elseif (strpos($ua, 'Macintosh')) {
            return 'macOS';
        } elseif (strpos($ua, 'Linux')) {
            return 'Linux';
        } elseif (strpos($ua, 'iPhone') || strpos($ua, 'iPad')) {
            return 'iOS';
        } elseif (strpos($ua, 'Android')) {
            return 'Android';
        }
        
        return 'Unknown';
    }
}