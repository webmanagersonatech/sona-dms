{{-- resources/views/departments/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Departments')

@section('content')
    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Department Management</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Departments</li>
                </ol>
            </nav>
        </div>
        @can('create', App\Models\Department::class)
            <a href="{{ route('departments.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i> New Department
            </a>
        @endcan
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($stats['total']) }}</h3>
                    <p>Total Departments</p>
                </div>
                <div class="stat-icon primary">
                    <i class="bi bi-building"></i>
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
                    <i class="bi bi-check-circle"></i>
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
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_users']) }}</h3>
                    <p>Total Users</p>
                </div>
                <div class="stat-icon info">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Departments Table Card -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">All Departments</h5>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <form method="GET" action="{{ route('departments.index') }}" class="row g-3 mb-4">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by name or code..."
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                    <a href="{{ route('departments.index') }}" class="btn btn-light">
                        <i class="bi bi-eraser"></i> Clear
                    </a>
                </div>
            </form>

            <!-- Departments Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Description</th>
                            <th>Users</th>
                            <th>Files</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departments as $department)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-building me-2"
                                            style="font-size: 1.5rem; color: var(--primary);"></i>
                                        <div>
                                            <a href="{{ route('departments.show', $department) }}"
                                                class="text-decoration-none fw-medium">
                                                {{ $department->name }}
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $department->code }}</span>
                                </td>
                                <td>
                                    <span
                                        class="text-muted">{{ Str::limit($department->description, 50) ?? 'No description' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ number_format($department->users_count) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-warning">{{ number_format($department->files_count) }}</span>
                                </td>
                                <td>
                                    @if ($department->status === 'active')
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $department->created_at->format('M d, Y') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('departments.show', $department) }}"
                                            class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @can('update', $department)
                                            <a href="{{ route('departments.edit', $department) }}"
                                                class="btn btn-sm btn-outline-info" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $department)
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="deleteDepartment({{ $department->id }})" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-building display-4 text-muted"></i>
                                    <p class="mt-2">No departments found</p>
                                    @can('create', App\Models\Department::class)
                                        <a href="{{ route('departments.create') }}" class="btn btn-primary">
                                            <i class="bi bi-plus-circle"></i> Create Your First Department
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
                    Showing {{ $departments->firstItem() ?? 0 }} to {{ $departments->lastItem() ?? 0 }} of
                    {{ $departments->total() }} entries
                </div>
                <div>
                    {{ $departments->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Form (Hidden) -->
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
    <script>
        function deleteDepartment(id) {
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
                    const form = document.getElementById('delete-form');
                    form.action = '/departments/' + id;
                    form.submit();
                }
            });
        }
    </script>
@endpush
