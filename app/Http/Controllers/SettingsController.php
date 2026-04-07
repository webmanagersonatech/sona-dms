<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function security()
    {
        $user = Auth::user();
        
        // Use the new helper on the user model
        $settings = $user->getSettings();
        
        $sessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'desc')
            ->get();

        return view('settings.security', compact('user', 'settings', 'sessions'));
    }

    public function updateSecurity(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'two_factor_enabled' => 'nullable|boolean',
            'session_timeout' => 'nullable|integer|min:5|max:480',
            'email_on_login' => 'nullable|boolean',
            'email_on_device' => 'nullable|boolean',
            'require_otp_download' => 'nullable|boolean',
            'notify_file_access' => 'nullable|boolean',
        ]);

        // Get current settings from model
        $settings = $user->getSettings();
        
        // Update settings
        $settings['two_factor_enabled'] = $request->boolean('two_factor_enabled', false);
        $settings['session_timeout'] = (int) $request->input('session_timeout', 30);
        $settings['email_on_login'] = $request->boolean('email_on_login', true);
        $settings['email_on_device'] = $request->boolean('email_on_device', true);
        $settings['require_otp_download'] = $request->boolean('require_otp_download', true);
        $settings['notify_file_access'] = $request->boolean('notify_file_access', true);

        // Save as JSON
        $user->settings = $settings;
        $user->save();

        $this->logActivity(
            $user,
            $request,
            'update_security',
            'settings',
            'Updated security settings'
        );

        return redirect()->route('settings.security')
            ->with('success', 'Security settings updated successfully.');
    }

    public function revokeSession(Request $request, $sessionId)
    {
        $user = Auth::user();
        
        DB::table('sessions')
            ->where('id', $sessionId)
            ->where('user_id', $user->id)
            ->delete();

        $this->logActivity(
            $user,
            $request,
            'revoke_session',
            'settings',
            'Revoked a device session'
        );

        return redirect()->route('settings.security')
            ->with('success', 'Session revoked successfully.');
    }

    public function notifications()
    {
        $user = Auth::user();
        $settings = $user->getSettings();

        return view('settings.notifications', compact('user', 'settings'));
    }

    public function updateNotifications(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'email_notifications' => 'nullable|boolean',
            'push_notifications' => 'nullable|boolean',
            'file_shared_notification' => 'nullable|boolean',
            'transfer_created_notification' => 'nullable|boolean',
            'transfer_delivered_notification' => 'nullable|boolean',
            'file_accessed_notification' => 'nullable|boolean',
        ]);

        $settings = $user->getSettings();
        
        $settings['email_notifications'] = $request->boolean('email_notifications', true);
        $settings['push_notifications'] = $request->boolean('push_notifications', false);
        $settings['file_shared_notification'] = $request->boolean('file_shared_notification', true);
        $settings['transfer_created_notification'] = $request->boolean('transfer_created_notification', true);
        $settings['transfer_delivered_notification'] = $request->boolean('transfer_delivered_notification', true);
        $settings['file_accessed_notification'] = $request->boolean('file_accessed_notification', true);

        $user->settings = $settings;
        $user->save();

        return redirect()->route('settings.notifications')
            ->with('success', 'Notification settings updated successfully.');
    }

    public function appearance()
    {
        $user = Auth::user();
        $settings = $user->getSettings();

        return view('settings.appearance', compact('user', 'settings'));
    }

    public function updateAppearance(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'theme' => 'required|in:light,dark,auto',
            'sidebar_collapsed' => 'nullable|boolean',
            'dense_mode' => 'nullable|boolean',
        ]);

        $settings = $user->getSettings();
        
        $settings['theme'] = $request->input('theme', 'light');
        $settings['sidebar_collapsed'] = $request->boolean('sidebar_collapsed', false);
        $settings['dense_mode'] = $request->boolean('dense_mode', false);

        $user->settings = $settings;
        $user->save();

        return redirect()->route('settings.appearance')
            ->with('success', 'Appearance settings updated successfully.');
    }


    /**
     * Log user activity
     */
    private function logActivity($user, $request, $action, $module, $description)
    {
        try {
            $agent = new Agent();
            $agent->setUserAgent($request->userAgent());
            
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => $action,
                'module' => $module,
                'description' => $description,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'device_type' => $agent->isMobile() ? 'mobile' : ($agent->isTablet() ? 'tablet' : 'desktop'),
                'browser' => $agent->browser(),
                'platform' => $agent->platform(),
                'location' => null,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to log activity: ' . $e->getMessage());
        }
    }
}