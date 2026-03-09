<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $devices = $user->deviceSessions()->orderBy('last_activity_at', 'desc')->get();
        
        return view('profile.index', compact('user', 'devices'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'current_password' => 'required_with:new_password',
            'new_password' => 'nullable|string|min:8|confirmed|different:current_password',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update basic info
        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        // Update password if provided
        if ($request->filled('current_password') && $request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->with('error', 'Current password is incorrect.');
            }

            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            ActivityLogger::log('profile_update', 'Changed password', $user->id);
        }

        ActivityLogger::log('profile_update', 'Updated profile information', $user->id);

        return redirect()->route('profile.index')->with('success', 'Profile updated successfully.');
    }

    public function updateNotificationPreferences(Request $request)
    {
        $user = Auth::user();
        
        $user->update([
            'notification_preferences' => $request->only([
                'email_alerts',
                'transfer_notifications',
                'file_access_alerts',
                'security_alerts',
            ]),
        ]);

        ActivityLogger::log('profile_update', 'Updated notification preferences', $user->id);

        return redirect()->back()->with('success', 'Notification preferences updated.');
    }

    public function activityLogs()
    {
        $user = Auth::user();
        $logs = $user->activityLogs()
            ->with(['file', 'transfer'])
            ->orderBy('performed_at', 'desc')
            ->paginate(20);

        return view('profile.activity-logs', compact('logs'));
    }
}