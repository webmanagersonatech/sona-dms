@extends('layouts.app')

@section('title', 'Files Report')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Files Report</h3>
                    <div class="card-tools">
                        <div class="btn-group">
                            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-download"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ route('reports.export', ['report_type' => 'files', 'type' => 'excel']) }}">
                                        <i class="bi bi-file-excel"></i> Excel
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ route('reports.export', ['report_type' => 'files', 'type' => 'pdf']) }}">
                                        <i class="bi bi-file-pdf"></i> PDF
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('reports.files') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Date From</label>
                                <input type="date" name="date_from" class="form-control"
                                    value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-3">
                                <label>Date To</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary d-block w-100">
                                    <i class="bi bi-filter"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $stats['total_files'] }}</h3>
                                    <p>Total Files</p>
                                </div>
                                <div class="icon"><i class="bi bi-files"></i></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ number_format($stats['total_size'] / 1048576, 2) }} MB</h3>
                                    <p>Total Storage</p>
                                </div>
                                <div class="icon"><i class="bi bi-hdd"></i></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $stats['daily_uploads']->sum('count') }}</h3>
                                    <p>Last 30 Days</p>
                                </div>
                                <div class="icon"><i class="bi bi-calendar"></i></div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Files by Type</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="typeChart" style="height: 250px;"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Daily Uploads (Last 30 Days)</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="dailyChart" style="height: 250px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Files Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="filesTable">
                            <thead>
                                <tr>
                                    <th>File Name</th>
                                    <th>Type</th>
                                    <th>Size</th>
                                    <th>Owner</th>
                                    <th>Department</th>
                                    <th>Uploaded</th>
                                    <th>Downloads</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($files as $file)
                                    <tr>
                                        <td>
                                            <a href="{{ route('files.show', $file) }}">{{ $file->name }}</a>
                                        </td>
                                        <td>{{ strtoupper($file->extension) }}</td>
                                        <td>{{ $file->size_for_humans }}</td>
                                        <td>{{ $file->owner->name }}</td>
                                        <td>{{ $file->department->name ?? 'N/A' }}</td>
                                        <td>{{ $file->created_at->format('Y-m-d') }}</td>
                                        <td>{{ $file->download_count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $files->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Files by Type Chart
        const typeData = @json($stats['by_type']);
        new Chart(document.getElementById('typeChart'), {
            type: 'doughnut',
            data: {
                labels: typeData.map(item => item.file_type.split('/').pop().toUpperCase()),
                datasets: [{
                    data: typeData.map(item => item.count),
                    backgroundColor: ['#17a2b8', '#28a745', '#ffc107', '#dc3545', '#6610f2']
                }]
            }
        });

        // Daily Uploads Chart
        const dailyData = @json($stats['daily_uploads']);
        new Chart(document.getElementById('dailyChart'), {
            type: 'line',
            data: {
                labels: dailyData.map(item => item.date),
                datasets: [{
                    label: 'Uploads',
                    data: dailyData.map(item => item.count),
                    borderColor: '#17a2b8',
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
