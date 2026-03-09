@extends('layouts.app')

@section('title', 'System Statistics')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="row mb-3 d-flex justify-content-end align-items-center">
                    <div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                <i class="fas fa-download"></i> Export Report
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" onclick="exportReport('daily')">
                                    <i class="fas fa-calendar-day"></i> Daily Report
                                </a>
                                <a class="dropdown-item" href="#" onclick="exportReport('weekly')">
                                    <i class="fas fa-calendar-week"></i> Weekly Report
                                </a>
                                <a class="dropdown-item" href="#" onclick="exportReport('monthly')">
                                    <i class="fas fa-calendar-alt"></i> Monthly Report
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Key Metrics -->
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-info">
                        <i class="fas fa-users"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Users</span>
                        <span class="info-box-number">{{ $userStats->sum('count') }}</span>
                        <div class="progress">
                            <div class="progress-bar" style="width: 100%"></div>
                        </div>
                        <span class="progress-description">
                            {{ $userStats->where('role_name', 'User')->first()->count ?? 0 }} regular users
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-success">
                        <i class="fas fa-file"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Files</span>
                        <span class="info-box-number">{{ $fileStats->sum('count') }}</span>
                        <div class="progress">
                            <div class="progress-bar" style="width: 100%"></div>
                        </div>
                        <span class="progress-description">
                            {{ $fileStats->where('extension', 'pdf')->first()->count ?? 0 }} PDF files
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-warning">
                        <i class="fas fa-paper-plane"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Transfers</span>
                        <span class="info-box-number">{{ $transferStats->sum('count') }}</span>
                        <div class="progress">
                            <div class="progress-bar" style="width: 100%"></div>
                        </div>
                        <span class="progress-description">
                            {{ $transferStats->where('status', 'received')->first()->count ?? 0 }} completed
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-danger">
                        <i class="fas fa-database"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Storage Used</span>
                        <span class="info-box-number">
                            {{ \App\Models\File::sum('size') > 0
                                ? number_format(\App\Models\File::sum('size') / (1024 * 1024), 2) . ' MB'
                                : '0 MB' }}
                        </span>
                        <div class="progress">
                            <div class="progress-bar" style="width: 100%"></div>
                        </div>
                        <span class="progress-description">
                            Average file size: {{ number_format(\App\Models\File::avg('size') / 1024, 2) ?? 0 }} KB
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Analytics -->
        <div class="row">
            <!-- User Distribution by Role -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            User Distribution by Role
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="userRoleChart" height="250"></canvas>
                    </div>
                </div>
            </div>

            <!-- File Types Distribution -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar mr-1"></i>
                            File Types Distribution
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="fileTypeChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <!-- Transfer Status Distribution -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Transfer Status Distribution
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="transferStatusChart" height="250"></canvas>
                    </div>
                </div>
            </div>

            <!-- Daily Activity Trends -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-1"></i>
                            Daily Activity Trends (Last 30 Days)
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="dailyActivityChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Statistics Tables -->
        <div class="row mt-3">
            <!-- Top Users by Activity -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-trophy mr-1"></i>
                            Top Users by Activity
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Role</th>
                                        <th>Files</th>
                                        <th>Transfers</th>
                                        <th>Activities</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $topUsers = \App\Models\User::withCount([
                                            'files',
                                            'sentTransfers',
                                            'activityLogs',
                                        ])
                                            ->orderBy('activity_logs_count', 'desc')
                                            ->limit(10)
                                            ->get();
                                    @endphp
                                    @foreach ($topUsers as $user)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="mr-2">
                                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0D8ABC&color=fff&size=32"
                                                            class="img-circle elevation-1" alt="User Image">
                                                    </div>
                                                    <div>
                                                        <strong>{{ $user->name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $user->department->name }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ getRoleBadgeColor($user->role->slug) }}">
                                                    {{ $user->role->name }}
                                                </span>
                                            </td>
                                            <td>{{ $user->files_count }}</td>
                                            <td>{{ $user->sent_transfers_count }}</td>
                                            <td>{{ $user->activity_logs_count }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .info-box .progress {
                height: 5px;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // User Role Distribution Chart
            const userRoleCtx = document.getElementById('userRoleChart').getContext('2d');
            const userRoleChart = new Chart(userRoleCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($userStats->pluck('role_name')),
                    datasets: [{
                        data: @json($userStats->pluck('count')),
                        backgroundColor: [
                            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                            '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // File Type Distribution Chart
            const fileTypeCtx = document.getElementById('fileTypeChart').getContext('2d');
            const fileTypeChart = new Chart(fileTypeCtx, {
                type: 'bar',
                data: {
                    labels: @json($fileStats->pluck('extension')),
                    datasets: [{
                        label: 'Number of Files',
                        data: @json($fileStats->pluck('count')),
                        backgroundColor: '#36A2EB',
                        borderColor: '#36A2EB',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });

            // Transfer Status Distribution Chart
            const transferStatusCtx = document.getElementById('transferStatusChart').getContext('2d');
            const transferStatusChart = new Chart(transferStatusCtx, {
                type: 'pie',
                data: {
                    labels: @json($transferStats->pluck('status')),
                    datasets: [{
                        data: @json($transferStats->pluck('count')),
                        backgroundColor: [
                            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Daily Activity Chart
            const dailyActivityCtx = document.getElementById('dailyActivityChart').getContext('2d');
            const dailyActivityChart = new Chart(dailyActivityCtx, {
                type: 'line',
                data: {
                    labels: @json($dailyActivity->pluck('date')),
                    datasets: [{
                        label: 'Activities',
                        data: @json($dailyActivity->pluck('count')),
                        borderColor: '#FF6384',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });

            function exportReport(type) {
                Swal.fire({
                    title: 'Exporting Report',
                    text: 'Please wait while we generate your ' + type + ' report...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Simulate report generation
                setTimeout(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Report Ready',
                        text: 'Your ' + type + ' report has been generated successfully.',
                        showCancelButton: true,
                        confirmButtonText: 'Download',
                        cancelButtonText: 'Close'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // In a real implementation, this would download the report
                            window.location.href = `/admin/stats/export?type=${type}`;
                        }
                    });
                }, 1500);
            }
        </script>
    @endpush
@endsection
