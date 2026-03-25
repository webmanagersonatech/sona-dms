{{-- resources/views/reports/activities.blade.php --}}
@extends('layouts.app')

@section('title', 'Activity Report')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Activity Report</h3>
                    <div class="card-tools">
                        <div class="btn-group">
                            <a href="{{ route('reports.export', ['report_type' => 'activities', 'type' => 'excel']) }}"
                                class="btn btn-success btn-sm">
                                <i class="bi bi-file-excel"></i> Excel
                            </a>
                            <a href="{{ route('reports.export', ['report_type' => 'activities', 'type' => 'pdf']) }}"
                                class="btn btn-danger btn-sm">
                                <i class="bi bi-file-pdf"></i> PDF
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $stats['total'] }}</h3>
                                    <p>Total Activities</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $stats['by_action']->first()?->count ?? 0 }}</h3>
                                    <p>Most Common Action</p>
                                    <small>{{ $stats['by_action']->first()?->action ?? 'N/A' }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $stats['by_hour']->max('count') ?? 0 }}</h3>
                                    <p>Peak Hour Activity</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ now()->format('h A') }}</h3>
                                    <p>Current Hour</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Activities by Hour (Today)</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="hourlyChart" style="height: 250px;"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Daily Activity (Last 30 Days)</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="dailyChart" style="height: 250px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activities Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th>Date/Time</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Module</th>
                                    <th>Description</th>
                                    <th>IP Address</th>
                                    <th>Device</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activities as $activity)
                                    <tr>
                                        <td>{{ $activity->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td>{{ $activity->user->name ?? 'System' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $activity->action === 'login' ? 'success' : 'info' }}">
                                                {{ ucfirst(str_replace('_', ' ', $activity->action)) }}
                                            </span>
                                        </td>
                                        <td>{{ ucfirst($activity->module) }}</td>
                                        <td>{{ $activity->description }}</td>
                                        <td>{{ $activity->ip_address ?? 'N/A' }}</td>
                                        <td>
                                            @if ($activity->device_type)
                                                <i
                                                    class="bi bi-{{ $activity->device_type === 'mobile' ? 'phone' : 'laptop' }}"></i>
                                                {{ ucfirst($activity->device_type) }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No activities found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $activities->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Hourly Chart
        const hours = Array.from({
            length: 24
        }, (_, i) => i.toString().padStart(2, '0') + ':00');
        const hourlyData = Array(24).fill(0);

        @foreach ($stats['by_hour'] as $item)
            hourlyData[{{ $item->hour }}] = {{ $item->count }};
        @endforeach

        new Chart(document.getElementById('hourlyChart'), {
            type: 'bar',
            data: {
                labels: hours,
                datasets: [{
                    label: 'Activities',
                    data: hourlyData,
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

        // Daily Chart
        new Chart(document.getElementById('dailyChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($stats['by_day']->pluck('date')) !!},
                datasets: [{
                    label: 'Activities',
                    data: {!! json_encode($stats['by_day']->pluck('count')) !!},
                    borderColor: '#198754',
                    fill: false
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

        // Initialize DataTable
        $(document).ready(function() {
            $('.datatable').DataTable({
                paging: false,
                searching: false,
                ordering: true,
                info: false
            });
        });
    </script>
@endpush
