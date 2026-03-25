@extends('layouts.app')

@section('title', 'Activity Statistics')

@section('content')
    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Activity Statistics</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('logs.index') }}">Activity Logs</a></li>
                    <li class="breadcrumb-item active">Statistics</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('logs.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-list-ul"></i> View Logs
            </a>
            @can('export', App\Models\ActivityLog::class)
                <a href="{{ route('logs.export') }}" class="btn btn-success">
                    <i class="bi bi-download"></i> Export
                </a>
            @endcan
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-md-4">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_today']) }}</h3>
                    <p>Today's Activities</p>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-calendar-day"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-4">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_week']) }}</h3>
                    <p>This Week</p>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-calendar-week"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-4">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_month']) }}</h3>
                    <p>This Month</p>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-calendar-month"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Activity by Hour -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Today's Activity by Hour
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="hourlyChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Users -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-people me-2"></i>
                        Top Active Users (30 Days)
                    </h5>
                </div>
                <div class="card-body">
                    @if ($stats['top_users']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Activities</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalTopUserActivities = $stats['top_users']->sum('count');
                                    @endphp
                                    @foreach ($stats['top_users'] as $userStat)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if ($userStat->user && $userStat->user->avatar)
                                                        <img src="{{ Storage::url($userStat->user->avatar) }}"
                                                            class="rounded-circle me-2" width="35" height="35"
                                                            style="object-fit: cover;">
                                                    @else
                                                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2"
                                                            style="width: 35px; height: 35px;">
                                                            <span class="text-white small fw-bold">
                                                                {{ $userStat->user ? strtoupper(substr($userStat->user->name, 0, 1)) : '?' }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-medium">{{ $userStat->user->name ?? 'Deleted User' }}
                                                        </div>
                                                        @if ($userStat->user && $userStat->user->department)
                                                            <small
                                                                class="text-muted">{{ $userStat->user->department->name }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-info rounded-pill">{{ number_format($userStat->count) }}</span>
                                            </td>
                                            <td style="width: 40%;">
                                                @php
                                                    $percentage =
                                                        $totalTopUserActivities > 0
                                                            ? round(
                                                                ($userStat->count / $totalTopUserActivities) * 100,
                                                                1,
                                                            )
                                                            : 0;
                                                @endphp
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar bg-success" role="progressbar"
                                                        style="width: {{ $percentage }}%;"
                                                        aria-valuenow="{{ $percentage }}" aria-valuemin="0"
                                                        aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ $percentage }}%</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-people display-4 text-muted"></i>
                            <p class="mt-2 text-muted">No activity data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action and Module Stats -->
    <div class="row mb-4">
        <!-- By Action -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightning me-2"></i>
                        Activities by Action (30 Days)
                    </h5>
                </div>
                <div class="card-body">
                    @if ($stats['by_action']->count() > 0)
                        <canvas id="actionChart" height="250"></canvas>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-pie-chart display-4 text-muted"></i>
                            <p class="mt-2 text-muted">No action data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- By Module -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-grid me-2"></i>
                        Activities by Module (30 Days)
                    </h5>
                </div>
                <div class="card-body">
                    @if ($stats['by_module']->count() > 0)
                        <canvas id="moduleChart" height="250"></canvas>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-bar-chart display-4 text-muted"></i>
                            <p class="mt-2 text-muted">No module data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Stats Tables -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-table me-2"></i>
                        Actions Breakdown
                    </h5>
                </div>
                <div class="card-body">
                    @if ($stats['by_action']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>Count</th>
                                        <th>Distribution</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalActions = $stats['by_action']->sum('count');
                                    @endphp
                                    @foreach ($stats['by_action'] as $action)
                                        <tr>
                                            <td>
                                                <span class="badge bg-light text-dark p-2">
                                                    <i
                                                        class="bi 
                                                        @switch($action->action)
                                                            @case('create') bi-plus-circle text-success @break
                                                            @case('update') bi-pencil text-info @break
                                                            @case('delete') bi-trash text-danger @break
                                                            @case('view') bi-eye text-primary @break
                                                            @case('download') bi-download text-success @break
                                                            @default bi-record
                                                        @endswitch
                                                        me-1">
                                                    </i>
                                                    {{ ucfirst($action->action) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ number_format($action->count) }}</span>
                                            </td>
                                            <td width="40%">
                                                @php
                                                    $percentage =
                                                        $totalActions > 0 ? ($action->count / $totalActions) * 100 : 0;
                                                @endphp
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1" style="height: 8px;">
                                                        <div class="progress-bar bg-info" role="progressbar"
                                                            style="width: {{ $percentage }}%"></div>
                                                    </div>
                                                    <span
                                                        class="ms-2 small text-muted">{{ number_format($percentage, 1) }}%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-3">No action data available</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-table me-2"></i>
                        Modules Breakdown
                    </h5>
                </div>
                <div class="card-body">
                    @if ($stats['by_module']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Module</th>
                                        <th>Count</th>
                                        <th>Distribution</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalModules = $stats['by_module']->sum('count');
                                    @endphp
                                    @foreach ($stats['by_module'] as $module)
                                        <tr>
                                            <td>
                                                <span class="badge bg-light text-dark p-2">
                                                    <i
                                                        class="bi 
                                                        @switch($module->module)
                                                            @case('user') bi-people @break
                                                            @case('file') bi-files @break
                                                            @case('department') bi-building @break
                                                            @case('role') bi-shield @break
                                                            @case('permission') bi-lock @break
                                                            @default bi-folder
                                                        @endswitch
                                                        me-1">
                                                    </i>
                                                    {{ ucfirst($module->module) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ number_format($module->count) }}</span>
                                            </td>
                                            <td width="40%">
                                                @php
                                                    $percentage =
                                                        $totalModules > 0 ? ($module->count / $totalModules) * 100 : 0;
                                                @endphp
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1" style="height: 8px;">
                                                        <div class="progress-bar bg-success" role="progressbar"
                                                            style="width: {{ $percentage }}%"></div>
                                                    </div>
                                                    <span
                                                        class="ms-2 small text-muted">{{ number_format($percentage, 1) }}%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-3">No module data available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .stat-card {
            border-radius: 10px;
            padding: 1.5rem;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .stat-card .stat-info {
            position: relative;
            z-index: 1;
        }

        .stat-card .stat-info h3 {
            font-size: 2rem;
            margin-bottom: 0.25rem;
            font-weight: 600;
        }

        .stat-card .stat-info p {
            margin-bottom: 0;
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .stat-card .stat-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 3rem;
            opacity: 0.2;
        }

        .progress {
            border-radius: 10px;
            background-color: #e9ecef;
        }

        .card {
            border: none;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.25rem;
        }

        .table th {
            border-top: none;
            font-weight: 500;
            color: #6c757d;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Hourly Chart
            @if ($stats['by_hour']->count() > 0)
                const hourlyData = @json($stats['by_hour']);
                const hours = Array.from({
                    length: 24
                }, (_, i) => {
                    const hour = i.toString().padStart(2, '0');
                    return `${hour}:00`;
                });
                const hourlyCounts = Array(24).fill(0);

                hourlyData.forEach(item => {
                    hourlyCounts[item.hour] = item.count;
                });

                new Chart(document.getElementById('hourlyChart'), {
                    type: 'line',
                    data: {
                        labels: hours,
                        datasets: [{
                            label: 'Activities',
                            data: hourlyCounts,
                            borderColor: 'rgb(102, 126, 234)',
                            backgroundColor: 'rgba(102, 126, 234, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: 'rgb(102, 126, 234)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    precision: 0
                                },
                                grid: {
                                    drawBorder: false
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            @endif

            // Action Chart
            @if ($stats['by_action']->count() > 0)
                const actionLabels = @json($stats['by_action']->pluck('action'));
                const actionCounts = @json($stats['by_action']->pluck('count'));

                new Chart(document.getElementById('actionChart'), {
                    type: 'doughnut',
                    data: {
                        labels: actionLabels.map(label => label.charAt(0).toUpperCase() + label.slice(1)),
                        datasets: [{
                            data: actionCounts,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.8)',
                                'rgba(54, 162, 235, 0.8)',
                                'rgba(255, 206, 86, 0.8)',
                                'rgba(75, 192, 192, 0.8)',
                                'rgba(153, 102, 255, 0.8)',
                                'rgba(255, 159, 64, 0.8)'
                            ],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    padding: 15
                                }
                            }
                        },
                        cutout: '60%'
                    }
                });
            @endif

            // Module Chart
            @if ($stats['by_module']->count() > 0)
                const moduleLabels = @json($stats['by_module']->pluck('module'));
                const moduleCounts = @json($stats['by_module']->pluck('count'));

                new Chart(document.getElementById('moduleChart'), {
                    type: 'bar',
                    data: {
                        labels: moduleLabels.map(label => label.charAt(0).toUpperCase() + label.slice(1)),
                        datasets: [{
                            label: 'Number of Activities',
                            data: moduleCounts,
                            backgroundColor: 'rgba(67, 233, 123, 0.8)',
                            borderRadius: 6,
                            barPercentage: 0.6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    precision: 0
                                },
                                grid: {
                                    drawBorder: false
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            @endif
        });
    </script>
@endpush
