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
    public function security()
    {
        $user = Auth::user();
        
        $settings = $user->settings ?? [];
        
        $sessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'desc')
            ->get();

        return view('settings.security', compact('user', 'settings', 'sessions'));
    }

    public function updateSecurity(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'two_factor_enabled' => 'boolean',
            'session_timeout' => 'nullable|integer|min:5|max:480',
            'email_on_login' => 'boolean',
            'email_on_device' => 'boolean',
            'require_otp_download' => 'boolean',
            'notify_file_access' => 'boolean',
        ]);

        $settings = $user->settings ?? [];
        
        $settings['two_factor_enabled'] = $request->boolean('two_factor_enabled');
        $settings['session_timeout'] = $request->session_timeout ?? 30;
        $settings['email_on_login'] = $request->boolean('email_on_login');
        $settings['email_on_device'] = $request->boolean('email_on_device');
        $settings['require_otp_download'] = $request->boolean('require_otp_download');
        $settings['notify_file_access'] = $request->boolean('notify_file_access');

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
        $settings = $user->settings ?? [];

        return view('settings.notifications', compact('user', 'settings'));
    }

    public function updateNotifications(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'file_shared_notification' => 'boolean',
            'transfer_created_notification' => 'boolean',
            'transfer_delivered_notification' => 'boolean',
            'file_accessed_notification' => 'boolean',
        ]);

        $settings = $user->settings ?? [];
        
        $settings['email_notifications'] = $request->boolean('email_notifications');
        $settings['push_notifications'] = $request->boolean('push_notifications');
        $settings['file_shared_notification'] = $request->boolean('file_shared_notification');
        $settings['transfer_created_notification'] = $request->boolean('transfer_created_notification');
        $settings['transfer_delivered_notification'] = $request->boolean('transfer_delivered_notification');
        $settings['file_accessed_notification'] = $request->boolean('file_accessed_notification');

        $user->settings = $settings;
        $user->save();

        return redirect()->route('settings.notifications')
            ->with('success', 'Notification settings updated successfully.');
    }

    public function appearance()
    {
        $user = Auth::user();
        $settings = $user->settings ?? [];

        return view('settings.appearance', compact('user', 'settings'));
    }

    public function updateAppearance(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'theme' => 'required|in:light,dark,auto',
            'sidebar_collapsed' => 'boolean',
            'dense_mode' => 'boolean',
        ]);

        $settings = $user->settings ?? [];
        
        $settings['theme'] = $request->theme;
        $settings['sidebar_collapsed'] = $request->boolean('sidebar_collapsed');
        $settings['dense_mode'] = $request->boolean('dense_mode');

        $user->settings = $settings;
        $user->save();

        return redirect()->route('settings.appearance')
            ->with('success', 'Appearance settings updated successfully.');
    }

    private function logActivity($user, $request, $action, $module, $description)
    {
        $agent = new Agent();
        
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
        ]);
    }
}