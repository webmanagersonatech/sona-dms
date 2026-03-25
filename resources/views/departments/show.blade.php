{{-- resources/views/departments/show.blade.php --}}
@extends('layouts.app')

@section('title', $department->name)

@section('content')
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">{{ $department->name }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">Departments</a></li>
                    <li class="breadcrumb-item active">{{ $department->code }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            @can('update', $department)
                <a href="{{ route('departments.edit', $department) }}" class="btn btn-info">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
            @endcan
            @can('delete', $department)
                <button type="button" class="btn btn-danger" onclick="deleteDepartment()">
                    <i class="bi bi-trash me-2"></i>Delete
                </button>
            @endcan
        </div>
    </div>

    <!-- Department Info Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($department->users_count) }}</h3>
                    <p>Total Users</p>
                </div>
                <div class="stat-icon primary">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($department->files_count) }}</h3>
                    <p>Total Files</p>
                </div>
                <div class="stat-icon success">
                    <i class="bi bi-files"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ $department->created_at->format('M d, Y') }}</h3>
                    <p>Created Date</p>
                </div>
                <div class="stat-icon warning">
                    <i class="bi bi-calendar"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>
                        @if ($department->status === 'active')
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif
                    </h3>
                    <p>Status</p>
                </div>
                <div class="stat-icon info">
                    <i class="bi bi-toggle-on"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-4">
            <!-- Department Details Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Department Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 120px;">Name:</th>
                            <td>{{ $department->name }}</td>
                        </tr>
                        <tr>
                            <th>Code:</th>
                            <td><span class="badge bg-light text-dark">{{ $department->code }}</span></td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if ($department->status === 'active')
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td>{{ $department->created_at->format('F d, Y h:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated:</th>
                            <td>{{ $department->updated_at->diffForHumans() }}</td>
                        </tr>
                    </table>

                    @if ($department->description)
                        <div class="mt-3">
                            <h6 class="fw-bold">Description</h6>
                            <p class="text-muted">{{ $department->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Department Admin Section -->
            @can('assignAdmin', $department)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Department Admin</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $deptAdmin = $department->departmentAdmin;
                        @endphp

                        @if ($deptAdmin)
                            <div class="d-flex align-items-center mb-3">
                                @if ($deptAdmin->avatar)
                                    <img src="{{ Storage::url($deptAdmin->avatar) }}" alt="{{ $deptAdmin->name }}"
                                        class="rounded-circle me-3" width="50" height="50">
                                @else
                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3"
                                        style="width: 50px; height: 50px;">
                                        <span class="text-white fs-5">{{ strtoupper(substr($deptAdmin->name, 0, 1)) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <h6 class="mb-1">{{ $deptAdmin->name }}</h6>
                                    <small class="text-muted">{{ $deptAdmin->email }}</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger ms-auto"
                                    onclick="removeAdmin({{ $deptAdmin->id }})">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        @else
                            <p class="text-muted text-center mb-3">No admin assigned to this department</p>
                        @endif

                        @if ($availableUsers->isNotEmpty())
                            <form action="{{ route('departments.assign-admin', $department) }}" method="POST">
                                @csrf
                                <div class="input-group">
                                    <select name="user_id" class="form-select" required>
                                        <option value="">Select user to assign as admin</option>
                                        @foreach ($availableUsers as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-person-plus"></i>
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            @endcan
        </div>

        <!-- Right Column -->
        <div class="col-lg-8">
            <!-- Recent Users -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Users</h5>
                    <a href="{{ route('users.index', ['department_id' => $department->id]) }}"
                        class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body p-0">
                    @if ($recentUsers->isNotEmpty())
                        <div class="list-group list-group-flush">
                            @foreach ($recentUsers as $user)
                                <div class="list-group-item">
                                    <div class="d-flex align-items-center">
                                        @if ($user->avatar)
                                            <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}"
                                                class="rounded-circle me-3" width="40" height="40">
                                        @else
                                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3"
                                                style="width: 40px; height: 40px;">
                                                <span
                                                    class="text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                            </div>
                                        @endif
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $user->name }}</h6>
                                            <small class="text-muted">{{ $user->email }} •
                                                {{ $user->role->name }}</small>
                                        </div>
                                        <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-people text-muted" style="font-size: 2rem;"></i>
                            <p class="mb-0">No users in this department</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Files -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Files</h5>
                    <a href="{{ route('files.index', ['department_id' => $department->id]) }}"
                        class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body p-0">
                    @if ($recentFiles->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>File</th>
                                        <th>Owner</th>
                                        <th>Size</th>
                                        <th>Uploaded</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentFiles as $file)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi {{ $file->icon }} me-2"
                                                        style="color: var(--primary);"></i>
                                                    <a href="{{ route('files.show', $file) }}"
                                                        class="text-decoration-none">
                                                        {{ Str::limit($file->name, 30) }}
                                                    </a>
                                                </div>
                                            </td>
                                            <td>{{ $file->owner->name }}</td>
                                            <td>{{ $file->size_for_humans }}</td>
                                            <td>{{ $file->created_at->diffForHumans() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-files text-muted" style="font-size: 2rem;"></i>
                            <p class="mb-0">No files in this department</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Activities</h5>
                </div>
                <div class="card-body p-0">
                    @if ($recentActivities->isNotEmpty())
                        <div class="list-group list-group-flush">
                            @foreach ($recentActivities as $activity)
                                <div class="list-group-item">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="stat-icon primary" style="width: 35px; height: 35px;">
                                                <i class="bi bi-activity"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <strong>{{ $activity->user->name }}</strong>
                                                    <span class="text-muted">•</span>
                                                    <span>{{ $activity->description }}</span>
                                                </div>
                                                <small
                                                    class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-clock-history text-muted" style="font-size: 2rem;"></i>
                            <p class="mb-0">No recent activities</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Form -->
    <form id="delete-form" method="POST" action="{{ route('departments.destroy', $department) }}"
        style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <!-- Remove Admin Form -->
    <form id="remove-admin-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
    <script>
        function deleteDepartment() {
            Swal.fire({
                title: 'Delete Department',
                text: 'Are you sure you want to delete this department? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f72585',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form').submit();
                }
            });
        }

        function removeAdmin(userId) {
            Swal.fire({
                title: 'Remove Admin',
                text: 'Are you sure you want to remove this user as department admin?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f72585',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, remove',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('remove-admin-form');
                    form.action = '{{ route('departments.remove-admin', [$department->id, 'USER_ID']) }}'.replace(
                        'USER_ID', userId);
                    form.submit();
                }
            });
        }
    </script>
@endpush
