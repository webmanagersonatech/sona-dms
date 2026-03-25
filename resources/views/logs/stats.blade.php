{{-- resources/views/logs/stats.blade.php --}}
@extends('layouts.app')

@section('title', 'Activity Statistics')

@section('content')
    <div class="row">
        <!-- Summary Cards -->
        <div class="col-lg-4 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_today'] }}</h3>
                    <p>Today's Activities</p>
                </div>
                <div class="icon">
                    <i class="bi bi-calendar-day"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['total_week'] }}</h3>
                    <p>This Week</p>
                </div>
                <div class="icon">
                    <i class="bi bi-calendar-week"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['total_month'] }}</h3>
                    <p>This Month</p>
                </div>
                <div class="icon">
                    <i class="bi bi-calendar-month"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Actions Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Activities by Action</h5>
                </div>
                <div class="card-body">
                    <canvas id="actionsChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Modules Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Activities by Module</h5>
                </div>
                <div class="card-body">
                    <canvas id="modulesChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Hourly Distribution -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Hourly Distribution (Today)</h5>
                </div>
                <div class="card-body">
                    <canvas id="hourlyChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Users -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Top Active Users (30 days)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Activities</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalTop = $stats['top_users']->sum('count');
                                @endphp
                                @foreach ($stats['top_users'] as $item)
                                    <tr>
                                        <td>
                                            <a href="{{ route('users.show', $item->user) }}">
                                                {{ $item->user->name }}
                                            </a>
                                        </td>
                                        <td>{{ $item->count }}</td>
                                        <td>
                                            @if ($totalTop > 0)
                                                {{ round(($item->count / $totalTop) * 100, 1) }}%
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
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
        // Actions Chart
        new Chart(document.getElementById('actionsChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(
                    $stats['by_action']->pluck('action')->map(function ($action) {
                        return ucfirst(str_replace('_', ' ', $action));
                    }),
                ) !!},
                datasets: [{
                    data: {!! json_encode($stats['by_action']->pluck('count')) !!},
                    backgroundColor: ['#17a2b8', '#28a745', '#ffc107', '#dc3545', '#6610f2', '#e83e8c']
                }]
            }
        });

        // Modules Chart
        new Chart(document.getElementById('modulesChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($stats['by_module']->pluck('module')->map('ucfirst')) !!},
                datasets: [{
                    label: 'Activities',
                    data: {!! json_encode($stats['by_module']->pluck('count')) !!},
                    backgroundColor: '#17a2b8'
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

        // Hourly Chart
        const hours = Array.from({
            length: 24
        }, (_, i) => i.toString().padStart(2, '0') + ':00');
        const hourlyData = Array(24).fill(0);

        @foreach ($stats['by_hour'] as $item)
            hourlyData[{{ $item->hour }}] = {{ $item->count }};
        @endforeach

        new Chart(document.getElementById('hourlyChart'), {
            type: 'line',
            data: {
                labels: hours,
                datasets: [{
                    label: 'Activities',
                    data: hourlyData,
                    borderColor: '#28a745',
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
    </script>
@endpush
