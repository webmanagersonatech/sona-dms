@extends('layouts.app')

@section('title', $role->name)

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">{{ $role->name }}</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
                <li class="breadcrumb-item active">{{ $role->name }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        @can('update', $role)
            @if($role->slug !== 'super-admin')
                <a href="{{ route('roles.edit', $role) }}" class="btn btn-info">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
                <a href="{{ route('roles.permissions', $role) }}" class="btn btn-warning">
                    <i class="bi bi-key me-2"></i>Permissions
                </a>
            @endif
        @endcan
    </div>
</div>

<!-- Role Info Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($usersCount) }}</h3>
                <p>Users with this role</p>
            </div>
            <div class="stat-icon primary">
                <i class="bi bi-people"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($role->permissions->count()) }}</h3>
                <p>Permissions</p>
            </div>
            <div class="stat-icon success">
                <i class="bi bi-key"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ $role->created_at->format('M d, Y') }}</h3>
                <p>Created Date</p>
            </div>
            <div class="stat-icon info">
                <i class="bi bi-calendar"></i>
            </div>
        </div>
    </div>
</div>

<!-- Role Details -->
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Role Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th style="width: 100px;">Name:</th>
                        <td>{{ $role->name }}</td>
                    </tr>
                    <tr>
                        <th>Slug:</th>
                        <td><code>{{ $role->slug }}</code></td>
                    </tr>
                    <tr>
                        <th>Created:</th>
                        <td>{{ $role->created_at->format('F d, Y h:i A') }}</td>
                    </tr>
                    <tr>
                        <th>Last Updated:</th>
                        <td>{{ $role->updated_at->diffForHumans() }}</td>
                    </tr>
                </table>

                @if($role->description)
                    <div class="mt-3">
                        <h6 class="fw-bold">Description</h6>
                        <p class="text-muted">{{ $role->description }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Permissions by Module -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Role Permissions</h5>
                @can('managePermissions', $role)
                    @if($role->slug !== 'super-admin')
                        <a href="{{ route('roles.permissions', $role) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i> Manage Permissions
                        </a>
                    @endif
                @endcan
            </div>
            <div class="card-body">
                @if($permissionsByModule->isEmpty())
                    <p class="text-muted text-center py-3">No permissions assigned to this role</p>
                @else
                    @foreach($permissionsByModule as $module => $permissions)
                        <div class="mb-4">
                            <h6 class="fw-bold text-primary">{{ ucfirst($module) }}</h6>
                            <div class="row">
                                @foreach($permissions as $permission)
                                    <div class="col-md-4 mb-2">
                                        <span class="badge bg-info py-2 px-3">
                                            <i class="bi bi-check-circle me-1"></i>
                                            {{ $permission->name }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @if(!$loop->last)
                            <hr>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Users with this role -->
        @if($role->users->isNotEmpty())
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Users with this role</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($role->users as $user)
                                    <tr>
                                        <td>
                                            <a href="{{ route('users.show', $user) }}" class="text-decoration-none">
                                                {{ $user->name }}
                                            </a>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->department->name ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection