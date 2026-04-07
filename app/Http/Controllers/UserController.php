<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Jenssegers\Agent\Agent;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::with(['role', 'department']);

        if (!Auth::user()->isSuperAdmin()) {
            $query->where('department_id', Auth::user()->department_id);
        }

        // Apply filters
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('department_id') && Auth::user()->isSuperAdmin()) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->orderBy('name')->paginate(15);

        // Get filter data
        $roles = Role::all();
        $departments = Department::where('status', 'active')->get();

        // Get statistics
        $stats = [
            'total' => (clone $query)->count(),
            'active' => (clone $query)->where('status', 'active')->count(),
            'inactive' => (clone $query)->where('status', 'inactive')->count(),
            'suspended' => (clone $query)->where('status', 'suspended')->count(),
        ];

        return view('users.index', compact('users', 'roles', 'departments', 'stats'));
    }

    public function create()
    {
        $this->authorize('create', User::class);

        $roles = Role::all();
        $departments = Department::where('status', 'active')->get();

        return view('users.create', compact('roles', 'departments'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'department_id' => $request->department_id,
                'phone' => $request->phone,
                'status' => $request->status,
            ]);

            $this->logActivity(
                Auth::user(),
                $request,
                'create_user',
                'user',
                'Created user: ' . $user->name,
                null,
                null,
                null,
                $user->toArray()
            );

            DB::commit();

            return redirect()->route('users.show', $user)
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create user: ' . $e->getMessage()]);
        }
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);

        $user->load(['role', 'department', 'files' => function($q) {
            $q->latest()->limit(10);
        }]);

        $recentActivities = ActivityLog::where('user_id', $user->id)
            ->latest()
            ->take(20)
            ->get();

        $stats = [
            'total_files' => $user->files()->count(),
            'total_transfers_sent' => $user->transfers()->count(),
            'total_transfers_received' => $user->receivedTransfers()->count(),
            'total_downloads' => $user->files()->sum('download_count'),
            'storage_used' => $user->files()->sum('file_size'),
        ];

        return view('users.show', compact('user', 'recentActivities', 'stats'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        $roles = Role::all();
        $departments = Department::where('status', 'active')->get();

        return view('users.edit', compact('user', 'roles', 'departments'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        DB::beginTransaction();
        try {
            $oldData = $user->toArray();
            
            $data = $request->except(['password', 'avatar']);

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
                Auth::user(),
                $request,
                'update_user',
                'user',
                'Updated user: ' . $user->name,
                $oldData,
                null,
                null,
                $user->toArray()
            );

            DB::commit();

            return redirect()->route('users.show', $user)
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update user: ' . $e->getMessage()]);
        }
    }

    public function destroy(Request $request, User $user)
    {
        $this->authorize('delete', $user);

        if ($user->isSuperAdmin()) {
            return back()->withErrors(['error' => 'Super Admin cannot be deleted.']);
        }

        DB::beginTransaction();
        try {
            $oldData = $user->toArray();
            
            // REALLY delete the user
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->delete();

            $this->logActivity(
                Auth::user(),
                $request,
                'delete_user',
                'user',
                'Deleted user: ' . $user->name,
                $oldData
            );

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete user. This might be due to dependencies like files or transfers. Please reassign them first.']);
        }
    }

    public function removeAvatar(Request $request, User $user)
    {
        $this->authorize('update', $user);

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);

            $this->logActivity(
                Auth::user(),
                $request,
                'remove_avatar',
                'user',
                'Removed avatar for user: ' . $user->name,
                null,
                null,
                null,
                $user->toArray()
            );
        }

        return back()->with('success', 'User avatar removed successfully.');
    }

    public function suspend(Request $request, User $user)
    {
        $this->authorize('update', $user);

        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'You cannot suspend yourself.']);
        }

        if ($user->isSuperAdmin()) {
            return back()->withErrors(['error' => 'Super Admins cannot be suspended.']);
        }

        $user->update(['status' => 'suspended']);

        $this->logActivity(
            Auth::user(),
            $request,
            'suspend_user',
            'user',
            'Suspended user: ' . $user->name,
            null,
            null,
            null,
            $user->toArray()
        );

        return back()->with('success', 'User suspended successfully.');
    }

    public function activate(Request $request, User $user)
    {
        $this->authorize('update', $user);

        if ($user->status === 'active') {
            return back()->withErrors(['error' => 'User is already active.']);
        }

        $oldData = $user->toArray();
        $user->update(['status' => 'active']);

        $this->logActivity(
            Auth::user(),
            $request,
            'activate_user',
            'user',
            'Activated user: ' . $user->name,
            $oldData,
            null,
            null,
            $user->toArray()
        );

        return redirect()->route('users.show', $user)
            ->with('success', 'User activated successfully.');
    }

    public function resetPassword(Request $request, User $user, \App\Services\BrevoService $brevoService)
    {
        $this->authorize('update', $user);

        $newPassword = Str::random(10);

        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        // Send email with new password
        $brevoService->sendPasswordChangedEmail($user->email, $user->name, $newPassword);

        $this->logActivity(
            Auth::user(),
            $request,
            'reset_password',
            'user',
            'Reset password for: ' . $user->name
        );

        return redirect()->route('users.show', $user)
            ->with('success', 'Password reset successfully. New password has been sent to user email.');
    }

    private function logActivity($user, $request, $action, $module, $description, $oldData = null, $fileId = null, $transferId = null, $newData = null)
    {
        $agent = new Agent();
        
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'module' => $module,
            'file_id' => $fileId,
            'transfer_id' => $transferId,
            'description' => $description,
            'old_data' => $oldData ? json_encode($oldData) : null,
            'new_data' => $newData ? json_encode($newData) : null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_type' => $agent->isMobile() ? 'mobile' : ($agent->isTablet() ? 'tablet' : 'desktop'),
            'browser' => $agent->browser(),
            'platform' => $agent->platform(),
        ]);
    }
}