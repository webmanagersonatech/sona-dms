{{-- resources/views/dashboard/department-admin.blade.php --}}
@extends('layouts.app')

@section('title', 'Department Admin Dashboard')

@section('content')
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_users']) }}</h3>
                    <p>Department Users</p>
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
                    <h3>{{ number_format($stats['active_users']) }}</h3>
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
                    <h3>{{ number_format($stats['total_files']) }}</h3>
                    <p>Department Files</p>
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
                    <h3>{{ number_format($stats['pending_transfers']) }}</h3>
                    <p>Pending Transfers</p>
                </div>
                <div class="stat-icon danger">
                    <i class="bi bi-truck"></i>
                </div>
                <a href="{{ route('transfers.index', ['status' => 'pending']) }}" class="stretched-link"></a>
            </div>
        </div>
    </div>

    <!-- Storage Usage Card -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Department Storage Usage</h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="progress" style="height: 25px;">
                                @php
                                    $storagePercentage =
                                        $stats['storage_used'] > 0
                                            ? min(
                                                round(($stats['storage_used'] / ($stats['storage_used'] * 2)) * 100),
                                                100,
                                            )
                                            : 0;
                                    $storageGB = $stats['storage_used'] / 1073741824; // Convert to GB
                                @endphp
                                <div class="progress-bar bg-primary progress-bar-striped" role="progressbar"
                                    style="width: {{ $storagePercentage }}%;" aria-valuenow="{{ $storagePercentage }}"
                                    aria-valuemin="0" aria-valuemax="100">
                                    {{ number_format($storageGB, 2) }} GB Used
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-end">
                                <h4 class="mb-0">{{ number_format($storageGB, 2) }} GB</h4>
                                <small class="text-muted">Total Storage Used</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- First Row: File Types Chart and Recent Activities -->
    <div class="row g-4 mb-4">
        <!-- File Types Distribution -->
        <div class="col-xl-5">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Files by Type</h5>
                    <button class="btn btn-sm btn-outline-primary" onclick="refreshChart()">
                        <i class="bi bi-arrow-repeat"></i>
                    </button>
                </div>
                <div class="card-body">
                    <canvas id="fileTypesChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activities Table -->
        <div class="col-xl-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Department Activities</h5>
                    <a href="{{ route('logs.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye me-1"></i> View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Module</th>
                                    <th>Time</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentActivities as $log)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if ($log->user && $log->user->avatar)
                                                    <img src="{{ Storage::url($log->user->avatar) }}"
                                                        alt="{{ $log->user->name }}" class="rounded-circle me-2"
                                                        width="32" height="32" style="object-fit: cover;">
                                                @else
                                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2"
                                                        style="width: 32px; height: 32px;">
                                                        <span class="text-white small">
                                                            {{ $log->user ? strtoupper(substr($log->user->name, 0, 1)) : 'S' }}
                                                        </span>
                                                    </div>
                                                @endif
                                                <span class="fw-medium">{{ $log->user->name ?? 'System' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $log->action === 'login' ? 'success' : ($log->action === 'logout' ? 'secondary' : 'info') }}">
                                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ ucfirst($log->module) }}</span>
                                        </td>
                                        <td>
                                            <span title="{{ $log->created_at->format('Y-m-d H:i:s') }}">
                                                {{ $log->created_at->diffForHumans() }}
                                            </span>
                                        </td>
                                        <td>
                                            <code>{{ $log->ip_address ?? 'N/A' }}</code>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <i class="bi bi-clock-history text-muted" style="font-size: 2rem;"></i>
                                            <p class="mb-0 mt-2">No recent activities</p>
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

    <!-- Second Row: Recent Files and Pending Transfers -->
    <div class="row g-4">
        <!-- Recent Department Files -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Department Files</h5>
                    <a href="{{ route('files.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye me-1"></i> View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>File</th>
                                    <th>Owner</th>
                                    <th>Size</th>
                                    <th>Type</th>
                                    <th>Uploaded</th>
                                    <th>Downloads</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentFiles as $file)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi {{ $file->icon }} me-2 fs-4"
                                                    style="color: var(--primary);"></i>
                                                <div>
                                                    <a href="{{ route('files.show', $file) }}"
                                                        class="text-decoration-none fw-medium">
                                                        {{ Str::limit($file->name, 25) }}
                                                    </a>
                                                    @if ($file->is_encrypted)
                                                        <span class="badge bg-warning ms-1" title="Encrypted">
                                                            <i class="bi bi-lock"></i>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if ($file->owner->avatar)
                                                    <img src="{{ Storage::url($file->owner->avatar) }}"
                                                        alt="{{ $file->owner->name }}" class="rounded-circle me-2"
                                                        width="28" height="28" style="object-fit: cover;">
                                                @else
                                                    <div class="rounded-circle bg-info d-flex align-items-center justify-content-center me-2"
                                                        style="width: 28px; height: 28px;">
                                                        <span
                                                            class="text-white small">{{ strtoupper(substr($file->owner->name, 0, 1)) }}</span>
                                                    </div>
                                                @endif
                                                <span>{{ $file->owner->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $file->size_for_humans }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ strtoupper($file->extension) }}</span>
                                        </td>
                                        <td>
                                            <span title="{{ $file->created_at->format('Y-m-d H:i:s') }}">
                                                {{ $file->created_at->diffForHumans() }}
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-info text-white">{{ number_format($file->download_count) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="bi bi-files text-muted" style="font-size: 2rem;"></i>
                                            <p class="mb-0 mt-2">No files found in your department</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Transfers -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Pending Transfers</h5>
                    <a href="{{ route('transfers.index', ['status' => 'pending']) }}"
                        class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye me-1"></i> View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Transfer ID</th>
                                    <th>Sender</th>
                                    <th>Receiver</th>
                                    <th>Purpose</th>
                                    <th>Expected</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingTransfers as $transfer)
                                    <tr>
                                        <td>
                                            <a href="{{ route('transfers.show', $transfer) }}"
                                                class="text-decoration-none fw-medium">
                                                <code>{{ $transfer->transfer_id }}</code>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if ($transfer->sender->avatar)
                                                    <img src="{{ Storage::url($transfer->sender->avatar) }}"
                                                        alt="{{ $transfer->sender->name }}" class="rounded-circle me-2"
                                                        width="28" height="28" style="object-fit: cover;">
                                                @else
                                                    <div class="rounded-circle bg-info d-flex align-items-center justify-content-center me-2"
                                                        style="width: 28px; height: 28px;">
                                                        <span
                                                            class="text-white small">{{ strtoupper(substr($transfer->sender->name, 0, 1)) }}</span>
                                                    </div>
                                                @endif
                                                <span>{{ $transfer->sender->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($transfer->receiver)
                                                <div class="d-flex align-items-center">
                                                    @if ($transfer->receiver->avatar)
                                                        <img src="{{ Storage::url($transfer->receiver->avatar) }}"
                                                            alt="{{ $transfer->receiver->name }}"
                                                            class="rounded-circle me-2" width="28" height="28"
                                                            style="object-fit: cover;">
                                                    @else
                                                        <div class="rounded-circle bg-success d-flex align-items-center justify-content-center me-2"
                                                            style="width: 28px; height: 28px;">
                                                            <span
                                                                class="text-white small">{{ strtoupper(substr($transfer->receiver->name, 0, 1)) }}</span>
                                                        </div>
                                                    @endif
                                                    <span>{{ $transfer->receiver->name }}</span>
                                                </div>
                                            @else
                                                <span>{{ $transfer->receiver_name }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span title="{{ $transfer->purpose }}">
                                                {{ Str::limit($transfer->purpose, 20) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span title="{{ $transfer->expected_delivery_time->format('Y-m-d H:i:s') }}">
                                                {{ $transfer->expected_delivery_time->format('M d, H:i') }}
                                            </span>
                                            @if ($transfer->isOverdue())
                                                <br>
                                                <span class="badge bg-danger mt-1">Overdue</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('transfers.show', $transfer) }}"
                                                    class="btn btn-outline-primary" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if ($transfer->receiver_id === Auth::id())
                                                    <button class="btn btn-outline-success" title="Confirm Delivery"
                                                        onclick="confirmAction('Confirm Delivery', 'Mark this transfer as delivered?', 'question', function() {
                                                            document.getElementById('confirm-form-{{ $transfer->id }}').submit();
                                                        })">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                    <form id="confirm-form-{{ $transfer->id }}"
                                                        action="{{ route('transfers.confirm', $transfer) }}"
                                                        method="POST" class="d-none">
                                                        @csrf
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="bi bi-check-circle text-muted" style="font-size: 2rem;"></i>
                                            <p class="mb-0 mt-2">No pending transfers</p>
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
        // Initialize File Types Chart
        const ctx = document.getElementById('fileTypesChart').getContext('2d');
        let fileTypesChart;

        function initChart() {
            const labels = {!! json_encode(
                $fileTypes->pluck('extension')->map(function ($ext) {
                    return strtoupper($ext);
                }),
            ) !!};
            const data = {!! json_encode($fileTypes->pluck('count')) !!};

            // Generate colors
            const backgroundColors = [
                '#4361ee', '#4cc9f0', '#f8961e', '#f72585', '#3f37c9',
                '#4895ef', '#4ad9d9', '#f9c74f', '#f9844a'
            ];

            if (fileTypesChart) {
                fileTypesChart.destroy();
            }

            fileTypesChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: backgroundColors.slice(0, labels.length),
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
                                    return `${label}: ${value} files (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Refresh chart function
        function refreshChart() {
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
                if (fileTypesChart) {
                    fileTypesChart.resize();
                }
            }, 250);
        });

        // Helper function for confirm actions
        window.confirmAction = function(title, text, icon, callback) {
            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: '#4361ee',
                cancelButtonColor: '#f72585',
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    callback();
                }
            });
        };

        // Toast notification function
        window.showToast = function(message, type = 'success') {
            Swal.fire({
                icon: type,
                title: message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        };
    </script>
@endpush
