<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments.
     */
    public function index(Request $request)
    {
        // Check if user can view any department - using string instead of class
        if (!Auth::user()->can('viewAny', Department::class)) {
            abort(403, 'Unauthorized action.');
        }

        $query = Department::withCount(['users', 'files']);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $departments = $query->orderBy('name')->paginate(15);

        $stats = [
            'total' => Department::count(),
            'active' => Department::where('status', 'active')->count(),
            'inactive' => Department::where('status', 'inactive')->count(),
            'total_users' => User::count(),
        ];

        return view('departments.index', compact('departments', 'stats'));
    }

    /**
     * Show the form for creating a new department.
     */
    public function create()
    {
        if (!Auth::user()->can('create', Department::class)) {
            abort(403, 'Unauthorized action.');
        }

        return view('departments.create');
    }

    /**
     * Store a newly created department.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->can('create', Department::class)) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:departments',
            'code' => 'required|string|max:50|unique:departments',
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ]);

        DB::beginTransaction();
        try {
            $department = Department::create([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'status' => $request->status,
            ]);

            $this->logActivity(
                Auth::user(),
                $request,
                'create',
                'department',
                'Created department: ' . $department->name,
                null,
                null,
                null,
                $department->toArray()
            );

            DB::commit();

            return redirect()->route('departments.show', $department)
                ->with('success', 'Department created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create department: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified department.
     */
    public function show(Department $department)
    {
        if (!Auth::user()->can('view', $department)) {
            abort(403, 'Unauthorized action.');
        }

        $department->loadCount(['users', 'files']);
        
        $recentUsers = $department->users()
            ->with('role')
            ->latest()
            ->take(10)
            ->get();

        $recentFiles = $department->files()
            ->with('owner')
            ->latest()
            ->take(10)
            ->get();

        $recentActivities = ActivityLog::whereHas('user', function($q) use ($department) {
                $q->where('department_id', $department->id);
            })
            ->with('user')
            ->latest()
            ->take(20)
            ->get();

        $availableUsers = User::where(function($q) use ($department) {
                $q->whereNull('department_id')
                  ->orWhere('department_id', '!=', $department->id);
            })
            ->where('status', 'active')
            ->whereDoesntHave('role', function($q) {
                $q->where('slug', 'super-admin');
            })
            ->get();

        return view('departments.show', compact('department', 'recentUsers', 'recentFiles', 'recentActivities', 'availableUsers'));
    }

    /**
     * Show the form for editing the specified department.
     */
    public function edit(Department $department)
    {
        if (!Auth::user()->can('update', $department)) {
            abort(403, 'Unauthorized action.');
        }

        return view('departments.edit', compact('department'));
    }

    /**
     * Update the specified department.
     */
    public function update(Request $request, Department $department)
    {
        if (!Auth::user()->can('update', $department)) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'code' => 'required|string|max:50|unique:departments,code,' . $department->id,
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ]);

        DB::beginTransaction();
        try {
            $oldData = $department->toArray();
            
            $department->update($request->all());

            $this->logActivity(
                Auth::user(),
                $request,
                'update',
                'department',
                'Updated department: ' . $department->name,
                $oldData,
                null,
                null,
                $department->toArray()
            );

            DB::commit();

            return redirect()->route('departments.show', $department)
                ->with('success', 'Department updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update department: ' . $e->getMessage()]);
        }
    }

    /**
     * Assign a user as department admin.
     */
    public function assignAdmin(Request $request, Department $department)
    {
        if (!Auth::user()->can('assignAdmin', $department)) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);
        
        // Check if user is already department admin
        if ($user->isDepartmentAdmin() && $user->department_id) {
            return back()->withErrors(['error' => 'User is already a department admin.']);
        }

        DB::beginTransaction();
        try {
            $oldData = $user->toArray();
            
            $user->update([
                'role_id' => Role::where('slug', 'department-admin')->first()->id,
                'department_id' => $department->id,
            ]);

            $this->logActivity(
                Auth::user(),
                $request,
                'assign_admin',
                'department',
                'Assigned ' . $user->name . ' as admin of ' . $department->name,
                $oldData,
                null,
                null,
                $user->toArray()
            );

            DB::commit();

            return redirect()->route('departments.show', $department)
                ->with('success', 'Department admin assigned successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to assign admin: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove a user as department admin.
     */
    public function removeAdmin(Request $request, Department $department, User $user)
    {
        if (!Auth::user()->can('assignAdmin', $department)) {
            abort(403, 'Unauthorized action.');
        }

        if ($user->department_id !== $department->id || !$user->isDepartmentAdmin()) {
            return back()->withErrors(['error' => 'User is not an admin of this department.']);
        }

        DB::beginTransaction();
        try {
            $oldData = $user->toArray();
            
            $user->update([
                'role_id' => Role::where('slug', 'user')->first()->id,
                'department_id' => $department->id, // Keep department but change role
            ]);

            $this->logActivity(
                Auth::user(),
                $request,
                'remove_admin',
                'department',
                'Removed ' . $user->name . ' as admin of ' . $department->name,
                $oldData,
                null,
                null,
                $user->toArray()
            );

            DB::commit();

            return redirect()->route('departments.show', $department)
                ->with('success', 'Department admin removed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to remove admin: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified department.
     */
    public function destroy(Request $request, Department $department)
    {
        if (!Auth::user()->can('delete', $department)) {
            abort(403, 'Unauthorized action.');
        }

        if ($department->users()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete department with assigned users.']);
        }

        DB::beginTransaction();
        try {
            $oldData = $department->toArray();
            $department->delete();

            $this->logActivity(
                Auth::user(),
                $request,
                'delete',
                'department',
                'Deleted department: ' . $department->name,
                $oldData
            );

            DB::commit();

            return redirect()->route('departments.index')
                ->with('success', 'Department deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete department: ' . $e->getMessage()]);
        }
    }

    /**
     * Log activity for department actions.
     */
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