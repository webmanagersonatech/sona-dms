{{-- resources/views/dashboard/super-admin.blade.php --}}
@extends('layouts.app')

@section('title', 'Super Admin Dashboard')

@section('content')
    <!-- Stats Cards (Updated to match new UI) -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_users']) }}</h3>
                    <p>Total Users</p>
                </div>
                <div class="stat-icon primary">
                    <i class="bi bi-people"></i>
                </div>
                <a href="{{ route('users.index') }}" class="stretched-link"></a>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_departments']) }}</h3>
                    <p>Departments</p>
                </div>
                <div class="stat-icon success">
                    <i class="bi bi-building"></i>
                </div>
                <a href="{{ route('departments.index') }}" class="stretched-link"></a>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_files']) }}</h3>
                    <p>Total Files</p>
                </div>
                <div class="stat-icon warning">
                    <i class="bi bi-files"></i>
                </div>
                <a href="{{ route('files.index') }}" class="stretched-link"></a>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_transfers']) }}</h3>
                    <p>Transfers</p>
                </div>
                <div class="stat-icon danger">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
                <a href="{{ route('transfers.index') }}" class="stretched-link"></a>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Users by Department</h5>
                    <div class="card-tools">
                        <button class="btn btn-sm btn-outline-primary" onclick="refreshChart()">
                            <i class="bi bi-arrow-repeat"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="usersChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Activities</h5>
                    <a href="{{ route('logs.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentActivities as $log)
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
                                                <strong>{{ $log->user->name ?? 'System' }}</strong>
                                                <span class="text-muted">•</span>
                                                <span>{{ $log->description }}</span>
                                            </div>
                                            <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                        </div>
                                        <small class="text-muted">
                                            <i class="bi bi-geo-alt"></i> {{ $log->ip_address ?? 'N/A' }}
                                            @if ($log->device_type)
                                                • <i
                                                    class="bi bi-{{ $log->device_type === 'mobile' ? 'phone' : 'laptop' }}"></i>
                                                {{ ucfirst($log->device_type) }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="bi bi-clock-history text-muted" style="font-size: 2rem;"></i>
                                <p class="mb-0 mt-2">No recent activities</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Files and Transfers Row -->
    <div class="row g-4">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Files</h5>
                    <a href="{{ route('files.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
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
                                @forelse($recentFiles as $file)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi {{ $file->icon }} me-2" style="color: var(--primary);"></i>
                                                <a href="{{ route('files.show', $file) }}" class="text-decoration-none">
                                                    {{ Str::limit($file->name, 30) }}
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if ($file->owner->avatar)
                                                    <img src="{{ Storage::url($file->owner->avatar) }}"
                                                        alt="{{ $file->owner->name }}" class="rounded-circle me-2"
                                                        width="25" height="25" style="object-fit: cover;">
                                                @else
                                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2"
                                                        style="width: 25px; height: 25px;">
                                                        <span
                                                            class="text-white small">{{ strtoupper(substr($file->owner->name, 0, 1)) }}</span>
                                                    </div>
                                                @endif
                                                {{ $file->owner->name }}
                                            </div>
                                        </td>
                                        <td>{{ $file->size_for_humans }}</td>
                                        <td>
                                            <span title="{{ $file->created_at->format('Y-m-d H:i:s') }}">
                                                {{ $file->created_at->diffForHumans() }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <i class="bi bi-files text-muted" style="font-size: 2rem;"></i>
                                            <p class="mb-0 mt-2">No files found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Transfers</h5>
                    <a href="{{ route('transfers.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Transfer ID</th>
                                    <th>Sender</th>
                                    <th>Receiver</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransfers as $transfer)
                                    <tr>
                                        <td>
                                            <a href="{{ route('transfers.show', $transfer) }}"
                                                class="text-decoration-none fw-medium">
                                                {{ $transfer->transfer_id }}
                                            </a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if ($transfer->sender->avatar)
                                                    <img src="{{ Storage::url($transfer->sender->avatar) }}"
                                                        alt="{{ $transfer->sender->name }}" class="rounded-circle me-2"
                                                        width="25" height="25" style="object-fit: cover;">
                                                @else
                                                    <div class="rounded-circle bg-info d-flex align-items-center justify-content-center me-2"
                                                        style="width: 25px; height: 25px;">
                                                        <span
                                                            class="text-white small">{{ strtoupper(substr($transfer->sender->name, 0, 1)) }}</span>
                                                    </div>
                                                @endif
                                                {{ $transfer->sender->name }}
                                            </div>
                                        </td>
                                        <td>
                                            @if ($transfer->receiver)
                                                <div class="d-flex align-items-center">
                                                    @if ($transfer->receiver->avatar)
                                                        <img src="{{ Storage::url($transfer->receiver->avatar) }}"
                                                            alt="{{ $transfer->receiver->name }}"
                                                            class="rounded-circle me-2" width="25" height="25"
                                                            style="object-fit: cover;">
                                                    @else
                                                        <div class="rounded-circle bg-success d-flex align-items-center justify-content-center me-2"
                                                            style="width: 25px; height: 25px;">
                                                            <span
                                                                class="text-white small">{{ strtoupper(substr($transfer->receiver->name, 0, 1)) }}</span>
                                                        </div>
                                                    @endif
                                                    {{ $transfer->receiver->name }}
                                                </div>
                                            @else
                                                {{ $transfer->receiver_name }}
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'in_transit' => 'info',
                                                    'delivered' => 'success',
                                                    'cancelled' => 'danger',
                                                    'failed' => 'danger',
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$transfer->status] ?? 'secondary' }}">
                                                {{ ucfirst(str_replace('_', ' ', $transfer->status)) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <i class="bi bi-truck text-muted" style="font-size: 2rem;"></i>
                                            <p class="mb-0 mt-2">No transfers found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Initialize Users Chart
        const ctx = document.getElementById('usersChart').getContext('2d');
        let usersChart;

        function initChart() {
            const labels = {!! json_encode($usersByDepartment->keys()) !!};
            const data = {!! json_encode($usersByDepartment->values()) !!};

            // Generate colors based on number of departments
            const backgroundColors = generateColors(labels.length);

            if (usersChart) {
                usersChart.destroy();
            }

            usersChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: backgroundColors,
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
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value} users (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Generate colors for chart
        function generateColors(count) {
            const baseColors = [
                '#4361ee', '#4cc9f0', '#f8961e', '#f72585', '#3f37c9',
                '#4895ef', '#4ad9d9', '#f9c74f', '#f9844a', '#7209b7'
            ];

            if (count <= baseColors.length) {
                return baseColors.slice(0, count);
            }

            // Generate additional colors if needed
            const colors = [...baseColors];
            for (let i = baseColors.length; i < count; i++) {
                const hue = (i * 137) % 360; // Golden angle approximation
                colors.push(`hsl(${hue}, 70%, 60%)`);
            }
            return colors;
        }

        // Refresh chart function
        function refreshChart() {
            // You can add AJAX here to fetch updated data
            showToast('Chart refreshed', 'success');
        }

        // Initialize chart when document is ready
        document.addEventListener('DOMContentLoaded', function() {
            initChart();
        });

        // Reinitialize chart on window resize (debounced)
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                if (usersChart) {
                    usersChart.resize();
                }
            }, 250);
        });
    </script>
@endpush
