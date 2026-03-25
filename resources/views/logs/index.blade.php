{{-- resources/views/logs/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')
<!-- Header Actions -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Activity Logs</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Activity Logs</li>
            </ol>
        </nav>
    </div>
    <div class="btn-group">
        <a href="{{ route('logs.stats') }}" class="btn btn-info">
            <i class="bi bi-graph-up me-2"></i> Statistics
        </a>
        
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['total'] ?? $logs->total()) }}</h3>
                <p>Total Logs</p>
            </div>
            <div class="stat-icon primary">
                <i class="bi bi-clock-history"></i>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['today'] ?? 0) }}</h3>
                <p>Today</p>
            </div>
            <div class="stat-icon success">
                <i class="bi bi-calendar-day"></i>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['week'] ?? 0) }}</h3>
                <p>This Week</p>
            </div>
            <div class="stat-icon warning">
                <i class="bi bi-calendar-week"></i>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['month'] ?? 0) }}</h3>
                <p>This Month</p>
            </div>
            <div class="stat-icon info">
                <i class="bi bi-calendar-month"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filter Card -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="bi bi-funnel me-2"></i> Filter Logs
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('logs.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <div class="input-group">
                    <span class="input-group-text bg-light">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control" placeholder="Search by description or IP..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">Action</label>
                <select name="action" class="form-select">
                    <option value="">All Actions</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $action)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Module</label>
                <select name="module" class="form-select">
                    <option value="">All Modules</option>
                    @foreach($modules as $module)
                        <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>
                            {{ ucfirst($module) }}
                        </option>
                    @endforeach
                </select>
            </div>
            @if(Auth::user()->isSuperAdmin())
                <div class="col-md-2">
                    <label class="form-label">User</label>
                    <select name="user_id" class="form-select">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-md-2">
                <label class="form-label">Date From</label>
                <div class="input-group">
                    <span class="input-group-text bg-light">
                        <i class="bi bi-calendar"></i>
                    </span>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">Date To</label>
                <div class="input-group">
                    <span class="input-group-text bg-light">
                        <i class="bi bi-calendar"></i>
                    </span>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
            </div>
            <div class="col-12 d-flex justify-content-between mt-3">
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-filter me-2"></i> Apply Filters
                    </button>
                    <a href="{{ route('logs.index') }}" class="btn btn-light">
                        <i class="bi bi-eraser me-2"></i> Clear Filters
                    </a>
                </div>
                @if(request()->has('search') || request()->has('action') || request()->has('module') || request()->has('user_id') || request()->has('date_from') || request()->has('date_to'))
                    <span class="text-muted">
                        <i class="bi bi-info-circle me-1"></i> Filters are applied
                    </span>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Activity Timeline Card -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="bi bi-list-ul me-2"></i> Activity Timeline
        </h5>
        <div>
            <select class="form-select form-select-sm" id="logLimit" style="width: auto;">
                <option value="50" {{ request('limit', 50) == 50 ? 'selected' : '' }}>50 per page</option>
                <option value="100" {{ request('limit') == 100 ? 'selected' : '' }}>100 per page</option>
                <option value="200" {{ request('limit') == 200 ? 'selected' : '' }}>200 per page</option>
            </select>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Date/Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Description</th>
                        <th>IP Address</th>
                        <th>Device</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>
                                <span class="fw-medium">{{ $log->created_at->format('M d, Y') }}</span>
                                <br>
                                <small class="text-muted">{{ $log->created_at->format('h:i:s A') }}</small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($log->user && $log->user->avatar)
                                        <img src="{{ Storage::url($log->user->avatar) }}" 
                                             alt="{{ $log->user->name }}" 
                                             class="rounded-circle me-2" width="32" height="32" style="object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2" 
                                             style="width: 32px; height: 32px;">
                                            <span class="text-white small">
                                                {{ $log->user ? strtoupper(substr($log->user->name, 0, 1)) : 'S' }}
                                            </span>
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('users.show', $log->user) }}" class="text-decoration-none fw-medium">
                                            {{ $log->user->name ?? 'System' }}
                                        </a>
                                        @if(!$log->user)
                                            <br>
                                            <small class="text-muted">System Action</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $actionColors = [
                                        'login' => 'success',
                                        'logout' => 'secondary',
                                        'upload' => 'primary',
                                        'download' => 'success',
                                        'share' => 'info',
                                        'create' => 'success',
                                        'update' => 'info',
                                        'delete' => 'danger',
                                        'archive' => 'warning',
                                        'restore' => 'success',
                                        'view' => 'info'
                                    ];
                                    $actionIcons = [
                                        'login' => 'box-arrow-in-right',
                                        'logout' => 'box-arrow-right',
                                        'upload' => 'upload',
                                        'download' => 'download',
                                        'share' => 'share',
                                        'create' => 'plus-circle',
                                        'update' => 'pencil',
                                        'delete' => 'trash',
                                        'archive' => 'archive',
                                        'restore' => 'arrow-counterclockwise',
                                        'view' => 'eye'
                                    ];
                                    $color = $actionColors[$log->action] ?? 'info';
                                    $icon = $actionIcons[$log->action] ?? 'activity';
                                @endphp
                                <span class="badge bg-{{ $color }} py-2 px-3">
                                    <i class="bi bi-{{ $icon }} me-1"></i>
                                    {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ ucfirst($log->module) }}</span>
                            </td>
                            <td>
                                <span class="text-muted">{{ Str::limit($log->description, 50) }}</span>
                                @if(strlen($log->description) > 50)
                                    <br>
                                    <small class="text-info" data-bs-toggle="tooltip" title="{{ $log->description }}">
                                        <i class="bi bi-info-circle"></i> more
                                    </small>
                                @endif
                            </td>
                            <td>
                                @if($log->ip_address)
                                    <code>{{ $log->ip_address }}</code>
                                    @if($log->location)
                                        <br>
                                        <small class="text-muted">{{ $log->location }}</small>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($log->device_type)
                                    <div>
                                        <i class="bi bi-{{ $log->device_type === 'mobile' ? 'phone' : ($log->device_type === 'tablet' ? 'tablet' : 'laptop') }} me-1"></i>
                                        <span>{{ ucfirst($log->device_type) }}</span>
                                    </div>
                                    @if($log->browser)
                                        <small class="text-muted">{{ $log->browser }} on {{ $log->platform }}</small>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('logs.show', $log) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($log->old_data || $log->new_data)
                                        <button type="button" class="btn btn-sm btn-outline-info" title="View Changes"
                                                onclick="showChanges({{ $log->id }})">
                                            <i class="bi bi-arrow-left-right"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-clock-history display-1 text-muted"></i>
                                <h5 class="mt-3">No Activity Logs Found</h5>
                                <p class="text-muted mb-3">No logs match your current filters.</p>
                                <a href="{{ route('logs.index') }}" class="btn btn-primary">
                                    <i class="bi bi-arrow-counterclockwise me-2"></i> Reset Filters
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($logs->hasPages())
            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                <div class="text-muted">
                    Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} entries
                </div>
                <div>
                    {{ $logs->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .stat-card {
        cursor: default;
    }
    .badge {
        font-weight: 500;
    }
    [data-theme="dark"] .bg-light {
        background-color: var(--dark) !important;
        color: var(--light) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Handle limit change
        $('#logLimit').on('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('limit', this.value);
            window.location.href = url.toString();
        });

        // Initialize DataTables for better sorting (optional)
        if (!$.fn.DataTable.isDataTable('.table')) {
            $('.table').DataTable({
                paging: false,
                searching: false,
                info: false,
                ordering: true,
                responsive: true,
                columnDefs: [
                    { orderable: false, targets: [4, 7] } // Disable sorting on description and actions
                ]
            });
        }
    });

    // Function to show changes (for the changes modal)
    function showChanges(logId) {
        // You can implement a modal to show old_data and new_data
        // This would require an AJAX call to fetch the log details
        window.location.href = '/logs/' + logId;
    }
</script>
@endpush