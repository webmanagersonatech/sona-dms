{{-- resources/views/reports/transfers.blade.php --}}
@extends('layouts.app')

@section('title', 'Movement Tracking Report')

@section('content')
<!-- Header Actions -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Movement Tracking Report</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('reports.dashboard') }}">Reports</a></li>
                <li class="breadcrumb-item active">Movement Tracking Report</li>
            </ol>
        </nav>
    </div>
    <!-- <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
            <i class="bi bi-download me-2"></i> Export Report
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <a class="dropdown-item" href="{{ route('reports.export', ['report_type' => 'transfers', 'type' => 'excel']) }}">
                    <i class="bi bi-file-earmark-excel text-success me-2"></i> Excel
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('reports.export', ['report_type' => 'transfers', 'type' => 'pdf']) }}">
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
            <i class="bi bi-funnel me-2"></i> Filter Movement Tracking Report
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('reports.transfers') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Date From</label>
                <div class="input-group">
                    <span class="input-group-text bg-light">
                        <i class="bi bi-calendar"></i>
                    </span>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label">Date To</label>
                <div class="input-group">
                    <span class="input-group-text bg-light">
                        <i class="bi bi-calendar"></i>
                    </span>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Sender</label>
                <select name="sender_id" class="form-select">
                    <option value="">All Senders</option>
                    @foreach($users ?? [] as $user)
                        <option value="{{ $user->id }}" {{ request('sender_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-filter me-2"></i> Apply Filters
                </button>
            </div>
            @if(request()->has('date_from') || request()->has('date_to') || request()->has('status') || request()->has('sender_id'))
                <div class="col-12 text-end">
                    <a href="{{ route('reports.transfers') }}" class="btn btn-sm btn-light">
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
                <p>Total Movements</p>
            </div>
            <div class="stat-icon primary">
                <i class="bi bi-arrow-left-right"></i>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['pending'] ?? 0) }}</h3>
                <p>Pending</p>
            </div>
            <div class="stat-icon warning">
                <i class="bi bi-hourglass-split"></i>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['in_transit'] ?? 0) }}</h3>
                <p>In Transit</p>
            </div>
            <div class="stat-icon info">
                <i class="bi bi-truck"></i>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['delivered'] ?? 0) }}</h3>
                <p>Delivered</p>
            </div>
            <div class="stat-icon success">
                <i class="bi bi-check-circle"></i>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['cancelled'] ?? 0) }}</h3>
                <p>Cancelled</p>
            </div>
            <div class="stat-icon danger">
                <i class="bi bi-x-circle"></i>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['overdue'] ?? 0) }}</h3>
                <p>Overdue</p>
            </div>
            <div class="stat-icon danger">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
        </div>
    </div>
</div>

<!-- Additional Stats Row -->
<div class="row g-4 mb-4">
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-pie-chart me-2"></i> Transfer Status Distribution
                </h5>
            </div>
            <div class="card-body">
                <canvas id="statusChart" style="height: 250px;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-graph-up me-2"></i> Monthly Trend
                </h5>
            </div>
            <div class="card-body">
                <canvas id="trendChart" style="height: 250px;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i> Delivery Performance
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <h2 class="mb-0">{{ number_format($stats['avg_delivery_time'] ?? 0, 1) }}</h2>
                    <p class="text-muted">Average Delivery Time (Hours)</p>
                </div>
                <div class="progress mb-3" style="height: 10px;">
                    @php
                        $onTimeRate = $stats['delivered'] > 0 ? 
                            (($stats['delivered'] - $stats['overdue']) / $stats['delivered'] * 100) : 0;
                    @endphp
                    <div class="progress-bar bg-success" style="width: {{ $onTimeRate }}%"></div>
                </div>
                <div class="d-flex justify-content-between">
                    <span><i class="bi bi-check-circle text-success me-1"></i> On Time: {{ number_format($onTimeRate, 1) }}%</span>
                    <span><i class="bi bi-exclamation-triangle text-danger me-1"></i> Overdue: {{ number_format(100 - $onTimeRate, 1) }}%</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transfers Table Card -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="bi bi-table me-2"></i> Transfers Details
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle datatable" id="transfersTable">
                <thead>
                    <tr>
                        <th>Transfer ID</th>
                        <th>Sender</th>
                        <th>Receiver</th>
                        <th>Purpose</th>
                        <th>Expected Delivery</th>
                        <th>Actual Delivery</th>
                        <th>Status</th>
                        <th>Cost</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $transfer)
                        <tr>
                            <td>
                                <a href="{{ route('transfers.show', $transfer) }}" class="text-decoration-none fw-medium">
                                    <code>{{ $transfer->transfer_id }}</code>
                                </a>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($transfer->sender && $transfer->sender->avatar)
                                        <img src="{{ Storage::url($transfer->sender->avatar) }}" 
                                             alt="{{ $transfer->sender->name }}" 
                                             class="rounded-circle me-2" width="28" height="28" style="object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-info d-flex align-items-center justify-content-center me-2" 
                                             style="width: 28px; height: 28px;">
                                            <span class="text-white small">{{ $transfer->sender ? strtoupper(substr($transfer->sender->name, 0, 1)) : '?' }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <span class="fw-medium">{{ $transfer->sender->name ?? 'Unknown' }}</span>
                                        <br>
                                        <small class="text-muted">{{ $transfer->sender->email ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($transfer->receiver)
                                    <div class="d-flex align-items-center">
                                        @if($transfer->receiver->avatar)
                                            <img src="{{ Storage::url($transfer->receiver->avatar) }}" 
                                                 alt="{{ $transfer->receiver->name }}" 
                                                 class="rounded-circle me-2" width="28" height="28" style="object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-success d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 28px; height: 28px;">
                                                <span class="text-white small">{{ strtoupper(substr($transfer->receiver->name, 0, 1)) }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <span class="fw-medium">{{ $transfer->receiver->name }}</span>
                                            <br>
                                            <small class="text-muted">{{ $transfer->receiver->email }}</small>
                                        </div>
                                    </div>
                                @else
                                    <div>
                                        <span class="fw-medium">{{ $transfer->receiver_name }}</span>
                                        <br>
                                        <small class="text-muted">{{ $transfer->receiver_email }}</small>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span title="{{ $transfer->purpose }}">
                                    {{ Str::limit($transfer->purpose, 30) }}
                                </span>
                                @if($transfer->description)
                                    <br>
                                    <small class="text-muted" data-bs-toggle="tooltip" title="{{ $transfer->description }}">
                                        <i class="bi bi-info-circle"></i> details
                                    </small>
                                @endif
                            </td>
                            <td>
                                @if($transfer->expected_delivery_time)
                                    <span class="fw-medium">{{ $transfer->expected_delivery_time->format('M d, Y') }}</span>
                                    <br>
                                    <small class="text-muted">{{ $transfer->expected_delivery_time->format('h:i A') }}</small>
                                    @if($transfer->isOverdue())
                                        <br>
                                        <span class="badge bg-danger mt-1">Overdue</span>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($transfer->actual_delivery_time)
                                    <span class="fw-medium">{{ $transfer->actual_delivery_time->format('M d, Y') }}</span>
                                    <br>
                                    <small class="text-muted">{{ $transfer->actual_delivery_time->format('h:i A') }}</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'in_transit' => 'info',
                                        'delivered' => 'success',
                                        'cancelled' => 'danger',
                                        'failed' => 'danger'
                                    ];
                                    $statusIcons = [
                                        'pending' => 'hourglass-split',
                                        'in_transit' => 'truck',
                                        'delivered' => 'check-circle',
                                        'cancelled' => 'x-circle',
                                        'failed' => 'exclamation-circle'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$transfer->status] ?? 'secondary' }} py-2 px-3">
                                    <i class="bi bi-{{ $statusIcons[$transfer->status] ?? 'question' }} me-1"></i>
                                    {{ ucfirst(str_replace('_', ' ', $transfer->status)) }}
                                </span>
                            </td>
                            <td>
                                @if($transfer->cost)
                                    <span class="fw-medium">{{ number_format($transfer->cost, 2) }} {{ $transfer->currency ?? 'USD' }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('transfers.show', $transfer) }}" 
                                       class="btn btn-sm btn-outline-primary" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('transfers.track', $transfer->transfer_id) }}" 
                                       class="btn btn-sm btn-outline-info" title="Track Transfer">
                                        <i class="bi bi-geo-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="bi bi-truck display-4 text-muted"></i>
                                <p class="mt-2">No transfers found matching the criteria</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($transfers->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Showing {{ $transfers->firstItem() }} to {{ $transfers->lastItem() }} of {{ $transfers->total() }} entries
                </div>
                <div>
                    {{ $transfers->appends(request()->query())->links() }}
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
    .nav-tabs .nav-link {
        color: var(--gray);
        border: none;
        padding: 10px 20px;
        font-weight: 500;
    }
    .nav-tabs .nav-link:hover {
        color: var(--primary);
        border: none;
    }
    .nav-tabs .nav-link.active {
        color: var(--primary);
        background: transparent;
        border-bottom: 3px solid var(--primary);
    }
    .progress {
        background-color: var(--gray-light);
        border-radius: 10px;
        overflow: hidden;
    }
    [data-theme="dark"] .progress {
        background-color: var(--dark);
    }
</style>
@endpush

@push('scripts')
<script>
    // Status Distribution Chart
    const statusData = @json($stats['by_status'] ?? []);
    const statusLabels = statusData.map(item => {
        return item.status?.charAt(0).toUpperCase() + item.status?.slice(1).replace('_', ' ') || 'Unknown';
    });
    const statusValues = statusData.map(item => item.count);

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusValues,
                backgroundColor: [
                    '#ffc107', // pending - warning
                    '#17a2b8', // in_transit - info
                    '#28a745', // delivered - success
                    '#dc3545', // cancelled - danger
                    '#6c757d'  // failed - secondary
                ],
                borderWidth: 0,
                hoverOffset: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: { size: 12 }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} transfers (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Monthly Trend Chart
    const trendData = @json($stats['by_month'] ?? []);
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: trendData.map(item => {
                const date = new Date(item.year, item.month - 1, 1);
                return date.toLocaleString('default', { month: 'short', year: 'numeric' });
            }),
            datasets: [{
                label: 'Transfers',
                data: trendData.map(item => item.count),
                borderColor: '#4361ee',
                backgroundColor: 'rgba(67, 97, 238, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    stepSize: 1,
                    grid: { display: true, color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // Initialize tooltips
    $(document).ready(function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Initialize DataTables
        if (!$.fn.DataTable.isDataTable('#transfersTable')) {
            $('#transfersTable').DataTable({
                responsive: true,
                paging: false,
                searching: true,
                info: false,
                ordering: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search in table...",
                    lengthMenu: "Show _MENU_ entries"
                },
                columnDefs: [
                    { orderable: false, targets: [8] } // Actions column
                ]
            });
        }
    });
</script>
@endpush