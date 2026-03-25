{{-- resources/views/reports/files.blade.php --}}
@extends('layouts.app')

@section('title', 'Files Report')

@section('content')
    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Files Report</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('reports.dashboard') }}">Reports</a></li>
                    <li class="breadcrumb-item active">Files Report</li>
                </ol>
            </nav>
        </div>
        <!-- <div class="btn-group">
            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-download me-2"></i> Export Report
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item"
                        href="{{ route('reports.export', ['report_type' => 'files', 'type' => 'excel']) }}">
                        <i class="bi bi-file-earmark-excel text-success me-2"></i> Excel
                    </a>
                </li>
                <li>
                    <a class="dropdown-item"
                        href="{{ route('reports.export', ['report_type' => 'files', 'type' => 'pdf']) }}">
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
                <i class="bi bi-funnel me-2"></i> Filter Reports
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.files') }}" class="row g-3">
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
                    <label class="form-label">Department</label>
                    <select name="department_id" class="form-select">
                        <option value="">All Departments</option>
                        @foreach ($departments ?? [] as $department)
                            <option value="{{ $department->id }}"
                                {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">File Type</label>
                    <select name="file_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="image" {{ request('file_type') == 'image' ? 'selected' : '' }}>Images</option>
                        <option value="document" {{ request('file_type') == 'document' ? 'selected' : '' }}>Documents
                        </option>
                        <option value="spreadsheet" {{ request('file_type') == 'spreadsheet' ? 'selected' : '' }}>
                            Spreadsheets</option>
                        <option value="presentation" {{ request('file_type') == 'presentation' ? 'selected' : '' }}>
                            Presentations</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-filter me-2"></i> Apply Filters
                    </button>
                </div>
                @if (request()->has('date_from') ||
                        request()->has('date_to') ||
                        request()->has('department_id') ||
                        request()->has('file_type'))
                    <div class="col-12 text-end">
                        <a href="{{ route('reports.files') }}" class="btn btn-sm btn-light">
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
                    <h3>{{ number_format($stats['total_files']) }}</h3>
                    <p>Total Files</p>
                </div>
                <div class="stat-icon primary">
                    <i class="bi bi-files"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_size'] / 1048576, 2) }} MB</h3>
                    <p>Total Storage</p>
                </div>
                <div class="stat-icon success">
                    <i class="bi bi-hdd-stack"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_downloads'] ?? 0) }}</h3>
                    <p>Total Downloads</p>
                </div>
                <div class="stat-icon warning">
                    <i class="bi bi-download"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_views'] ?? 0) }}</h3>
                    <p>Total Views</p>
                </div>
                <div class="stat-icon info">
                    <i class="bi bi-eye"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pie-chart me-2"></i> Files by Type
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="typeChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up me-2"></i> Daily Uploads
                    </h5>
                    <select class="form-select form-select-sm w-auto" id="chartPeriod">
                        <option value="7">Last 7 Days</option>
                        <option value="30" selected>Last 30 Days</option>
                        <option value="90">Last 90 Days</option>
                    </select>
                </div>
                <div class="card-body">
                    <canvas id="dailyChart" style="height: 300px;"></canvas>
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
                        <i class="bi bi-building me-2"></i> Files by Department
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="departmentChart" style="height: 250px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-file-earmark me-2"></i> Top File Extensions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Extension</th>
                                    <th>Count</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stats['by_extension'] ?? [] as $extension)
                                    @php
                                        $percentage = ($extension->count / $stats['total_files']) * 100;
                                    @endphp
                                    <tr>
                                        <td><span
                                                class="badge bg-light text-dark">{{ strtoupper($extension->extension) }}</span>
                                        </td>
                                        <td>{{ number_format($extension->count) }}</td>
                                        <td>
                                            <div class="progress" style="height: 5px;">
                                                <div class="progress-bar bg-primary" style="width: {{ $percentage }}%">
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ number_format($percentage, 1) }}%</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-trophy me-2"></i> Top Downloaded Files
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach ($stats['top_files'] ?? [] as $file)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="{{ route('files.show', $file) }}"
                                            class="text-decoration-none fw-medium">
                                            {{ Str::limit($file->name, 25) }}
                                        </a>
                                        <br>
                                        <small class="text-muted">
                                            <i class="bi bi-person"></i> {{ $file->owner->name }}
                                        </small>
                                    </div>
                                    <span class="badge bg-info">{{ number_format($file->download_count) }}
                                        downloads</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Files Table Card -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-table me-2"></i> Files Details
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable" id="filesTable">
                    <thead>
                        <tr>
                            <th>File</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th>Owner</th>
                            <th>Department</th>
                            <th>Uploaded</th>
                            <th>Downloads</th>
                            <th>Views</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($files as $file)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi {{ $file->icon }} me-2 fs-4" style="color: var(--primary);"></i>
                                        <div>
                                            <a href="{{ route('files.show', $file) }}"
                                                class="text-decoration-none fw-medium">
                                                {{ Str::limit($file->name, 35) }}
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
                                    <span class="badge bg-light text-dark">{{ strtoupper($file->extension) }}</span>
                                </td>
                                <td>{{ $file->size_for_humans }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if ($file->owner->avatar)
                                            <img src="{{ Storage::url($file->owner->avatar) }}"
                                                alt="{{ $file->owner->name }}" class="rounded-circle me-2"
                                                width="28" height="28" style="object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2"
                                                style="width: 28px; height: 28px;">
                                                <span
                                                    class="text-white small">{{ strtoupper(substr($file->owner->name, 0, 1)) }}</span>
                                            </div>
                                        @endif
                                        <span>{{ $file->owner->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if ($file->department)
                                        <span class="badge bg-light text-dark">{{ $file->department->name }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span title="{{ $file->created_at->format('Y-m-d H:i:s') }}">
                                        {{ $file->created_at->format('M d, Y') }}
                                    </span>
                                    <br>
                                    <small class="text-muted">{{ $file->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <span>{{ number_format($file->download_count) }}</span>
                                </td>
                                <td>
                                    <span>{{ number_format($file->view_count) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-files display-4 text-muted"></i>
                                    <p class="mt-2">No files found matching the criteria</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($files->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Showing {{ $files->firstItem() }} to {{ $files->lastItem() }} of {{ $files->total() }} entries
                    </div>
                    <div>
                        {{ $files->appends(request()->query())->links() }}
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Files by Type Chart
        const typeData = @json($stats['by_type'] ?? []);
        new Chart(document.getElementById('typeChart'), {
            type: 'doughnut',
            data: {
                labels: typeData.map(item => {
                    const type = item.file_type?.split('/').pop()?.toUpperCase() || 'Unknown';
                    return type;
                }),
                datasets: [{
                    data: typeData.map(item => item.count),
                    backgroundColor: [
                        '#4361ee', '#4cc9f0', '#f8961e', '#f72585', '#3f37c9',
                        '#4895ef', '#4ad9d9', '#f9c74f', '#f9844a', '#7209b7'
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

        // Daily Uploads Chart
        const dailyData = @json($stats['daily_uploads'] ?? []);
        new Chart(document.getElementById('dailyChart'), {
            type: 'line',
            data: {
                labels: dailyData.map(item => item.date),
                datasets: [{
                    label: 'Uploads',
                    data: dailyData.map(item => item.count),
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
                        stepSize: 1,
                        grid: {
                            display: true,
                            color: 'rgba(0,0,0,0.05)'
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

        // Department Chart
        const deptData = @json($stats['by_department'] ?? []);
        if (deptData.length > 0) {
            new Chart(document.getElementById('departmentChart'), {
                type: 'bar',
                data: {
                    labels: deptData.map(item => item.department?.name || 'No Department'),
                    datasets: [{
                        label: 'Files',
                        data: deptData.map(item => item.count),
                        backgroundColor: '#4361ee',
                        borderRadius: 6
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
                            stepSize: 1,
                            grid: {
                                display: true,
                                color: 'rgba(0,0,0,0.05)'
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
        } else {
            document.getElementById('departmentChart').parentNode.innerHTML =
                '<div class="text-center py-4"><i class="bi bi-building text-muted" style="font-size: 3rem;"></i><p class="mt-2">No department data available</p></div>';
        }

        // Chart period change handler
        document.getElementById('chartPeriod')?.addEventListener('change', function() {
            const period = this.value;
            fetch(`{{ route('reports.files') }}?period=${period}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Update chart with new data
                    dailyChart.data.labels = data.labels;
                    dailyChart.data.datasets[0].data = data.values;
                    dailyChart.update();
                });
        });

        // Initialize DataTables
        $(document).ready(function() {
            if (!$.fn.DataTable.isDataTable('#filesTable')) {
                $('#filesTable').DataTable({
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
                    columnDefs: [{
                        orderable: false,
                        targets: [7]
                    }]
                });
            }
        });
    </script>
@endpush
