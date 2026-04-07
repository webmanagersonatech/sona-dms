{{-- resources/views/reports/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Reports Dashboard')

@section('content')
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ $file_stats['total'] }}</h3>
                    <p>Total Files</p>
                </div>
                <div class="stat-icon info">
                    <i class="bi bi-files"></i>
                </div>
                <a href="{{ route('reports.files') }}" class="stretched-link"></a>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ $transfer_stats['total'] }}</h3>
                    <p>Total Transfers</p>
                </div>
                <div class="stat-icon success">
                    <i class="bi bi-truck"></i>
                </div>
                <a href="{{ route('reports.transfers') }}" class="stretched-link"></a>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ $user_stats['total'] ?? 0 }}</h3>
                    <p>Total Users</p>
                </div>
                <div class="stat-icon warning">
                    <i class="bi bi-people"></i>
                </div>
                <a href="{{ route('reports.users') }}" class="stretched-link"></a>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ $activity_stats['today'] }}</h3>
                    <p>Today's Activities</p>
                </div>
                <div class="stat-icon danger">
                    <i class="bi bi-activity"></i>
                </div>
                <a href="{{ route('reports.activities') }}" class="stretched-link"></a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- File Statistics -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">File Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Files by Type</h6>
                            <canvas id="fileTypeChart"></canvas>
                        </div>
                        <div class="col-md-6">
                            <h6>Daily Uploads (Last 7 Days)</h6>
                            <canvas id="fileTrendChart"></canvas>
                        </div>
                    </div>

                    <hr>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <p><strong>Total Storage:</strong> {{ number_format($file_stats['total_size'] / 1048576, 2) }}
                                MB</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Average File Size:</strong>
                                {{ number_format($file_stats['total_size'] / max($file_stats['total'], 1) / 1048576, 2) }}
                                MB</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transfer Statistics -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Transfer Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Transfer Status</h6>
                            <canvas id="transferStatusChart"></canvas>
                        </div>
                        <div class="col-md-6">
                            <h6>Daily Transfers (Last 7 Days)</h6>
                            <canvas id="transferTrendChart"></canvas>
                        </div>
                    </div>

                    <hr>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <p><strong>Pending:</strong> {{ $transfer_stats['pending'] }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Delivered:</strong> {{ $transfer_stats['delivered'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <!-- User Statistics -->
        @if ($user_stats)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">User Statistics</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="userRoleChart"></canvas>
                        <hr>
                        <div class="row mt-3">
                            <div class="col-6">
                                <p><strong>Active:</strong> {{ $user_stats['active'] }}</p>
                            </div>
                            <div class="col-6">
                                <p><strong>New (30d):</strong> {{ $user_stats['new'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Activity Statistics -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Activity Statistics</h5>
                </div>
                <div class="card-body">
                    <h6>Top Actions (Last 7 Days)</h6>
                    <canvas id="activityActionChart"></canvas>
                    <hr>
                    <div class="row mt-3">
                        <div class="col-4">
                            <p><strong>Today:</strong> {{ $activity_stats['today'] }}</p>
                        </div>
                        <div class="col-4">
                            <p><strong>Week:</strong> {{ $activity_stats['week'] }}</p>
                        </div>
                        <div class="col-4">
                            <p><strong>Month:</strong> {{ $activity_stats['month'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('reports.files', ['date_from' => now()->startOfMonth()->format('Y-m-d'), 'date_to' => now()->format('Y-m-d')]) }}"
                            class="btn btn-outline-primary">
                            <i class="bi bi-calendar"></i> This Month's Files
                        </a>
                        <a href="{{ route('reports.transfers', ['status' => 'pending']) }}"
                            class="btn btn-outline-warning">
                            <i class="bi bi-hourglass"></i> Pending Transfers
                        </a>
                        <a href="{{ route('reports.users', ['status' => 'active']) }}" class="btn btn-outline-success">
                            <i class="bi bi-person-check"></i> Active Users
                        </a>
                        <a href="{{ route('logs.export', ['format' => 'excel', 'date_from' => now()->startOfMonth()->format('Y-m-d')]) }}"
                            class="btn btn-outline-secondary">
                            <i class="bi bi-download"></i> Export This Month's Logs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // File Type Chart
        new Chart(document.getElementById('fileTypeChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(
                    $file_stats['by_type']->pluck('extension')->map(function ($ext) {
                        return strtoupper($ext);
                    }),
                ) !!},
                datasets: [{
                    data: {!! json_encode($file_stats['by_type']->pluck('count')) !!},
                    backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0']
                }]
            }
        });

        // File Trend Chart
        new Chart(document.getElementById('fileTrendChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($file_stats['trend']->pluck('date')) !!},
                datasets: [{
                    label: 'Uploads',
                    data: {!! json_encode($file_stats['trend']->pluck('count')) !!},
                    borderColor: '#0d6efd',
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        stepSize: 1
                    }
                }
            }
        });

        // Transfer Status Chart
        new Chart(document.getElementById('transferStatusChart'), {
            type: 'pie',
            data: {
                labels: ['Pending', 'In Transit', 'Delivered', 'Cancelled'],
                datasets: [{
                    data: [
                        {{ $transfer_stats['pending'] }},
                        {{ $transfer_stats['in_transit'] ?? 0 }},
                        {{ $transfer_stats['delivered'] }},
                        {{ $transfer_stats['cancelled'] ?? 0 }}
                    ],
                    backgroundColor: ['#ffc107', '#0dcaf0', '#198754', '#dc3545']
                }]
            }
        });

        // Transfer Trend Chart
        new Chart(document.getElementById('transferTrendChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($transfer_stats['trend']->pluck('date')) !!},
                datasets: [{
                    label: 'Transfers',
                    data: {!! json_encode($transfer_stats['trend']->pluck('count')) !!},
                    borderColor: '#198754',
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        stepSize: 1
                    }
                }
            }
        });

        @if ($user_stats)
            // User Role Chart
            new Chart(document.getElementById('userRoleChart'), {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($user_stats['by_role']->pluck('role.name')) !!},
                    datasets: [{
                        data: {!! json_encode($user_stats['by_role']->pluck('count')) !!},
                        backgroundColor: ['#0d6efd', '#198754', '#ffc107']
                    }]
                }
            });
        @endif

        // Activity Action Chart
        new Chart(document.getElementById('activityActionChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode(
                    $activity_stats['by_action']->pluck('action')->map(function ($action) {
                        return ucfirst(str_replace('_', ' ', $action));
                    }),
                ) !!},
                datasets: [{
                    label: 'Count',
                    data: {!! json_encode($activity_stats['by_action']->pluck('count')) !!},
                    backgroundColor: '#0d6efd'
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        stepSize: 1
                    }
                }
            }
        });
    </script>
@endpush
