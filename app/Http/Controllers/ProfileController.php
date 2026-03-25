<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user()->load(['role', 'department']);
        
        $recentActivities = ActivityLog::where('user_id', $user->id)
            ->latest()
            ->take(20)
            ->get();

        $stats = [
            'total_files' => $user->files()->count(),
            'total_transfers' => $user->transfers()->count(),
            'total_downloads' => $user->files()->sum('download_count'),
            'storage_used' => $user->files()->sum('file_size'),
        ];

        $sessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'desc')
            ->get();

        return view('profile.show', compact('user', 'recentActivities', 'stats', 'sessions'));
=======
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
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
    }

    public function update(Request $request)
    {
        $user = Auth::user();
<<<<<<< HEAD

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        $user->update($data);

        $this->logActivity(
            $user,
            $request,
            'update_profile',
            'profile',
            'Updated profile information'
        );

        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    public function password(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        $this->logActivity(
            $user,
            $request,
            'change_password',
            'profile',
            'Changed account password'
        );

        return redirect()->route('profile.show')
            ->with('success', 'Password changed successfully.');
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return response()->json([
            'success' => true,
            'avatar_url' => Storage::url($path)
        ]);
    }

    public function removeAvatar(Request $request)
    {
        $user = Auth::user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }

        return redirect()->route('profile.show')
            ->with('success', 'Avatar removed successfully.');
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
=======
        
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
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
    }
}