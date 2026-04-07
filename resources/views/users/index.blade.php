{{-- resources/views/users/index.blade.php --}}
@extends('layouts.app')

@section('title', 'User Management')

@section('content')
    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">User Management</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Users</li>
                </ol>
            </nav>
        </div>
        @can('create', App\Models\User::class)
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus me-2"></i> Add New User
            </a>
        @endcan
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($stats['total']) }}</h3>
                    <p>Total Users</p>
                </div>
                <div class="stat-icon primary">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($stats['active']) }}</h3>
                    <p>Active</p>
                </div>
                <div class="stat-icon success">
                    <i class="bi bi-person-check"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($stats['inactive']) }}</h3>
                    <p>Inactive</p>
                </div>
                <div class="stat-icon warning">
                    <i class="bi bi-person-exclamation"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($stats['suspended']) }}</h3>
                    <p>Suspended</p>
                </div>
                <div class="stat-icon danger">
                    <i class="bi bi-person-slash"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">All Users</h5>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <form method="GET" action="{{ route('users.index') }}" class="row g-3 mb-4">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search by name or email..."
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="role_id" class="form-select">
                        <option value="">All Roles</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="department_id" class="form-select">
                        <option value="">All Departments</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}"
                                {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended
                        </option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-light">
                        <i class="bi bi-eraser"></i> Clear
                    </a>
                </div>
            </form>

            <!-- Users Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Department</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if ($user->avatar)
                                            <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}"
                                                class="rounded-circle me-2" width="40" height="40"
                                                style="object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2"
                                                style="width: 40px; height: 40px;">
                                                <span
                                                    class="text-white fw-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <a href="{{ route('users.show', $user) }}"
                                                class="text-decoration-none fw-medium">
                                                {{ $user->name }}
                                            </a>
                                            <br>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span>{{ $user->role->name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @if ($user->department)
                                        <span class="badge bg-light text-dark">{{ $user->department->name }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($user->phone)
                                        <i class="bi bi-telephone me-1"></i>{{ $user->phone }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($user->status === 'active')
                                        <span class="badge badge-success">Active</span>
                                    @elseif($user->status === 'inactive')
                                        <span class="badge badge-warning">Inactive</span>
                                    @else
                                        <span class="badge badge-danger">Suspended</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($user->last_login_at)
                                        <small>{{ $user->last_login_at->diffForHumans() }}</small>
                                        <br>
                                        <small class="text-muted">{{ $user->last_login_ip ?? 'Unknown IP' }}</small>
                                    @else
                                        <span class="text-muted">Never</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-primary"
                                            title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @can('update', $user)
                                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-info"
                                                title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $user)
                                            @if ($user->status !== 'suspended')
                                                <button type="button" class="btn btn-sm btn-outline-warning"
                                                    onclick="suspendUser({{ $user->id }})" title="Suspend">
                                                    <i class="bi bi-person-slash"></i>
                                                </button>
                                            @else
                                                 <button type="button" class="btn btn-sm btn-outline-success"
                                                    onclick="activateUser({{ $user->id }})" title="Activate">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="deleteUser({{ $user->id }})" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endcan
                                        @can('update', $user)
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                onclick="resetPassword({{ $user->id }})" title="Reset Password">
                                                <i class="bi bi-key"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="bi bi-people display-4 text-muted"></i>
                                    <p class="mt-2">No users found</p>
                                    @can('create', App\Models\User::class)
                                        <a href="{{ route('users.create') }}" class="btn btn-primary">
                                            <i class="bi bi-person-plus"></i> Add Your First User
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }}
                    entries
                </div>
                <div>
                    {{ $users->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Forms -->
    <form id="activate-form" method="POST" style="display: none;">
        @csrf
    </form>

    <form id="suspend-form" method="POST" style="display: none;">
        @csrf
    </form>

    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <form id="reset-password-form" method="POST" style="display: none;">
        @csrf
    </form>
@endsection

@push('scripts')
    <script>
        const API_USER_BASE = "{{ url('users') }}";

        function activateUser(id) {
            Swal.fire({
                title: 'Activate User',
                text: 'Are you sure you want to activate this user?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, activate',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('activate-form');
                    form.action = `${API_USER_BASE}/${id}/activate`;
                    form.submit();
                }
            });
        }

        function suspendUser(id) {
            Swal.fire({
                title: 'Suspend User',
                text: 'Are you sure you want to suspend this user? They will not be able to login.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, suspend',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('suspend-form');
                    form.action = `${API_USER_BASE}/${id}/suspend`;
                    form.submit();
                }
            });
        }

        function deleteUser(id) {
            Swal.fire({
                title: 'Delete User',
                text: 'Are you sure you want to PERMANENTLY delete this user? This action cannot be undone.',
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#f72585',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('delete-form');
                    form.action = `${API_USER_BASE}/${id}`;
                    form.submit();
                }
            });
        }

        function resetPassword(id) {
            Swal.fire({
                title: 'Reset Password',
                text: 'Are you sure you want to reset this user\'s password? A new password will be sent to their email.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6c757d',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reset',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('reset-password-form');
                    form.action = `${API_USER_BASE}/${id}/reset-password`;
                    form.submit();
                }
            });
        }
    </script>
@endpush
