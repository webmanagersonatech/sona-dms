@extends('layouts.app')

@section('title', 'Role Management')

@section('content')
<!-- Header Actions -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Role Management</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Roles</li>
            </ol>
        </nav>
    </div>
    @can('create', App\Models\Role::class)
    <a href="{{ route('roles.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i> New Role
    </a>
    @endcan
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-4 col-md-4">
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['total']) }}</h3>
                <p>Total Roles</p>
            </div>
            <div class="stat-icon primary">
                <i class="bi bi-shield"></i>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-md-4">
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['with_users']) }}</h3>
                <p>Roles with Users</p>
            </div>
            <div class="stat-icon success">
                <i class="bi bi-people"></i>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-md-4">
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['total_permissions']) }}</h3>
                <p>Total Permissions</p>
            </div>
            <div class="stat-icon info">
                <i class="bi bi-key"></i>
            </div>
        </div>
    </div>
</div>

<!-- Roles Table Card -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">All Roles</h5>
    </div>
    <div class="card-body">
        <!-- Search Filter -->
        <form method="GET" action="{{ route('roles.index') }}" class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search roles..." value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-8 text-end">
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                    <i class="bi bi-eraser me-1"></i> Clear
                </a>
            </div>
        </form>

        <!-- Roles Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Role</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th>Users</th>
                        <th>Permissions</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-shield me-2 fs-4" style="color: var(--primary);"></i>
                                    <div>
                                        <a href="{{ route('roles.show', $role) }}" class="text-decoration-none fw-medium">
                                            {{ $role->name }}
                                        </a>
                                        @if($role->slug === 'super-admin')
                                            <span class="badge bg-danger ms-2">System</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td><code>{{ $role->slug }}</code></td>
                            <td>{{ Str::limit($role->description, 50) ?? '—' }}</td>
                            <td>
                                <span class="badge bg-info">{{ number_format($role->users_count) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-success">{{ number_format($role->permissions_count) }}</span>
                            </td>
                            <td>
                                <span title="{{ $role->created_at->format('Y-m-d H:i:s') }}">
                                    {{ $role->created_at->format('M d, Y') }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('roles.show', $role) }}" 
                                       class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    @can('update', $role)
                                        @if($role->slug !== 'super-admin')
                                            <a href="{{ route('roles.edit', $role) }}" 
                                               class="btn btn-sm btn-outline-info" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="{{ route('roles.permissions', $role) }}" 
                                               class="btn btn-sm btn-outline-warning" title="Permissions">
                                                <i class="bi bi-key"></i>
                                            </a>
                                        @endif
                                    @endcan
                                    
                                    @can('delete', $role)
                                        @if($role->slug !== 'super-admin' && $role->users_count == 0)
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteRole({{ $role->id }})" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="bi bi-shield display-4 text-muted"></i>
                                <p class="mt-2">No roles found</p>
                                @can('create', App\Models\Role::class)
                                <a href="{{ route('roles.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Create Your First Role
                                </a>
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($roles->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Showing {{ $roles->firstItem() }} to {{ $roles->lastItem() }} of {{ $roles->total() }} entries
                </div>
                <div>
                    {{ $roles->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Delete Form -->
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    function deleteRole(roleId) {
        Swal.fire({
            title: 'Delete Role',
            text: 'Are you sure you want to delete this role? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('delete-form');
                form.action = '/roles/' + roleId;
                form.submit();
            }
        });
    }
</script>
@endpush