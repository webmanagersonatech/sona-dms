{{-- resources/views/reports/users.blade.php --}}
@extends('layouts.app')

@section('title', 'Users Report')

@section('content')
    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Users Report</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('reports.dashboard') }}">Reports</a></li>
                    <li class="breadcrumb-item active">Users Report</li>
                </ol>
            </nav>
        </div>
        <!-- <div class="btn-group">
            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-download me-2"></i> Export Report
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item"
                        href="{{ route('reports.export', ['report_type' => 'users', 'type' => 'excel']) }}">
                        <i class="bi bi-file-earmark-excel text-success me-2"></i> Excel
                    </a>
                </li>
                <li>
                    <a class="dropdown-item"
                        href="{{ route('reports.export', ['report_type' => 'users', 'type' => 'pdf']) }}">
                        <i class="bi bi-file-earmark-pdf text-danger me-2"></i> PDF
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#" onclick="window.print()">
                        <i class="bi bi-printer text-primary me-2"></i> Print
                    </a>
                </li>
            </ul>
        </div> -->
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-funnel me-2"></i> Filter Users Report
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.users') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Role</label>
                    <select name="role_id" class="form-select">
                        <option value="">All Roles</option>
                        @foreach ($roles ?? [] as $role)
                            <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Department</label>
                    <select name="department_id" class="form-select">
                        <option value="">All Departments</option>
                        @foreach ($departments ?? [] as $department)
                            <option value="{{ $department->id }}"
                                {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended
                        </option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-filter me-2"></i> Apply Filters
                    </button>
                </div>
                @if (request()->has('role_id') || request()->has('department_id') || request()->has('status'))
                    <div class="col-12 text-end">
                        <a href="{{ route('reports.users') }}" class="btn btn-sm btn-light">
                            <i class="bi bi-eraser me-1"></i> Clear Filters
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($stats['total'] ?? 0) }}</h3>
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
                    <h3>{{ number_format($stats['active'] ?? 0) }}</h3>
                    <p>Active Users</p>
                </div>
                <div class="stat-icon success">
                    <i class="bi bi-person-check"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($stats['inactive'] ?? 0) }}</h3>
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
                    <h3>{{ number_format($stats['new'] ?? 0) }}</h3>
                    <p>New (30 days)</p>
                </div>
                <div class="stat-icon info">
                    <i class="bi bi-person-plus"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-table me-2"></i> Users Details
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable" id="usersTable">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Files</th>
                            <th>Transfers</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if ($user->avatar)
                                            <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}"
                                                class="rounded-circle me-2" width="32" height="32"
                                                style="object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2"
                                                style="width: 32px; height: 32px;">
                                                <span
                                                    class="text-white small">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
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
                                    @if ($user->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($user->status === 'inactive')
                                        <span class="badge bg-warning">Inactive</span>
                                    @else
                                        <span class="badge bg-danger">Suspended</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($user->last_login_at)
                                        <span title="{{ $user->last_login_at->format('Y-m-d H:i:s') }}">
                                            {{ $user->last_login_at->diffForHumans() }}
                                        </span>
                                    @else
                                        <span class="text-muted">Never</span>
                                    @endif
                                </td>
                                <td>
                                    <span>{{ number_format($user->files_count ?? $user->files()->count()) }}</span>
                                </td>
                                <td>
                                    <span>{{ number_format($user->transfers_count ?? $user->transfers()->count()) }}</span>
                                </td>
                                <td>
                                    <span title="{{ $user->created_at->format('Y-m-d H:i:s') }}">
                                        {{ $user->created_at->format('M d, Y') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-people display-4 text-muted"></i>
                                    <p class="mt-2">No users found matching the criteria</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($users->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} entries
                    </div>
                    <div>
                        {{ $users->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            if (!$.fn.DataTable.isDataTable('#usersTable')) {
                $('#usersTable').DataTable({
                    responsive: true,
                    paging: false,
                    searching: true,
                    info: false,
                    ordering: true,
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search in table..."
                    }
                });
            }
        });
    </script>
@endpush
