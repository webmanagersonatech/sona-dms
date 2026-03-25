{{-- resources/views/files/shared-with-me.blade.php --}}
@extends('layouts.app')

@section('title', 'Shared With Me')

@section('content')
    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Shared With Me</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('files.index') }}">Files</a></li>
                    <li class="breadcrumb-item active">Shared With Me</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('files.index') }}" class="btn btn-primary">
            <i class="bi bi-arrow-left me-2"></i> Back to Files
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('files.by-permission', 'view') }}" class="text-decoration-none">
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>{{ $counts['view'] ?? 0 }}</h3>
                        <p>View Only</p>
                    </div>
                    <div class="stat-icon info">
                        <i class="bi bi-eye"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('files.by-permission', 'download') }}" class="text-decoration-none">
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>{{ $counts['download'] ?? 0 }}</h3>
                        <p>Downloadable</p>
                    </div>
                    <div class="stat-icon success">
                        <i class="bi bi-download"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('files.by-permission', 'edit') }}" class="text-decoration-none">
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>{{ $counts['edit'] ?? 0 }}</h3>
                        <p>Editable</p>
                    </div>
                    <div class="stat-icon warning">
                        <i class="bi bi-pencil"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('files.by-permission', 'print') }}" class="text-decoration-none">
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>{{ $counts['print'] ?? 0 }}</h3>
                        <p>Printable</p>
                    </div>
                    <div class="stat-icon secondary">
                        <i class="bi bi-printer"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="card mb-4">
        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ !request()->has('permission') ? 'active' : '' }}"
                        href="{{ route('files.shared-with-me') }}">
                        <i class="bi bi-grid-3x3-gap-fill me-1"></i> All Shared
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('permission') == 'view' ? 'active' : '' }}"
                        href="{{ route('files.shared-with-me', ['permission' => 'view']) }}">
                        <i class="bi bi-eye me-1"></i> View Only
                        @if (($counts['view'] ?? 0) > 0)
                            <span class="badge bg-info ms-1">{{ $counts['view'] }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('permission') == 'download' ? 'active' : '' }}"
                        href="{{ route('files.shared-with-me', ['permission' => 'download']) }}">
                        <i class="bi bi-download me-1"></i> Downloadable
                        @if (($counts['download'] ?? 0) > 0)
                            <span class="badge bg-success ms-1">{{ $counts['download'] }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('permission') == 'edit' ? 'active' : '' }}"
                        href="{{ route('files.shared-with-me', ['permission' => 'edit']) }}">
                        <i class="bi bi-pencil me-1"></i> Editable
                        @if (($counts['edit'] ?? 0) > 0)
                            <span class="badge bg-warning ms-1">{{ $counts['edit'] }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('permission') == 'print' ? 'active' : '' }}"
                        href="{{ route('files.shared-with-me', ['permission' => 'print']) }}">
                        <i class="bi bi-printer me-1"></i> Printable
                        @if (($counts['print'] ?? 0) > 0)
                            <span class="badge bg-secondary ms-1">{{ $counts['print'] }}</span>
                        @endif
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Shares Table Card -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                @if (request('permission'))
                    {{ ucfirst(request('permission')) }} Files Shared With Me
                @else
                    All Shared Files
                @endif
            </h5>
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="window.print()">
                    <i class="bi bi-printer"></i> Print
                </button>
                <button type="button" class="btn btn-sm btn-outline-success" onclick="exportTable()">
                    <i class="bi bi-download"></i> Export
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Search and Filter -->
            <form method="GET" action="{{ route('files.shared-with-me') }}" class="row g-3 mb-4">
                @if (request('permission'))
                    <input type="hidden" name="permission" value="{{ request('permission') }}">
                @endif
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search shared files..."
                            value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="sort" class="form-select">
                        <option value="desc" {{ request('sort', 'desc') == 'desc' ? 'selected' : '' }}>Newest First
                        </option>
                        <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('files.shared-with-me') }}" class="btn btn-secondary w-100">
                        <i class="bi bi-eraser me-1"></i> Clear Filters
                    </a>
                </div>
            </form>

            <!-- Shares Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable" id="shared-files-table">
                    <thead>
                        <tr>
                            <th>File</th>
                            <th>Shared By</th>
                            <th>Permission</th>
                            <th>Shared On</th>
                            <th>Expires</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shares as $share)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi {{ $share->file->icon ?? 'bi-file' }} me-2 fs-4"
                                            style="color: var(--primary);"></i>
                                        <div>
                                            <a href="{{ route('files.show', $share->file) }}"
                                                class="text-decoration-none fw-medium">
                                                {{ Str::limit($share->file->name ?? 'Unnamed', 35) }}
                                            </a>
                                            @if ($share->file->is_encrypted ?? false)
                                                <span class="badge bg-warning ms-1" title="Encrypted">
                                                    <i class="bi bi-lock"></i>
                                                </span>
                                            @endif
                                            <br>
                                            <small class="text-muted">
                                                <i class="bi bi-file-earmark"></i>
                                                {{ $share->file->size_for_humans ?? '0 B' }} •
                                                {{ strtoupper($share->file->extension ?? 'N/A') }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if ($share->sharedBy && $share->sharedBy->avatar)
                                            <img src="{{ Storage::url($share->sharedBy->avatar) }}"
                                                alt="{{ $share->sharedBy->name }}" class="rounded-circle me-2"
                                                width="32" height="32" style="object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-info d-flex align-items-center justify-content-center me-2"
                                                style="width: 32px; height: 32px;">
                                                <span
                                                    class="text-white small">{{ $share->sharedBy ? strtoupper(substr($share->sharedBy->name, 0, 1)) : '?' }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <span class="fw-medium">{{ $share->sharedBy->name ?? 'Unknown' }}</span>
                                            <br>
                                            <small class="text-muted">{{ $share->sharedBy->email ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $permissionColors = [
                                            'view' => 'info',
                                            'download' => 'success',
                                            'edit' => 'warning',
                                            'print' => 'secondary',
                                            'full_control' => 'danger',
                                        ];
                                        $permissionIcons = [
                                            'view' => 'eye',
                                            'download' => 'download',
                                            'edit' => 'pencil',
                                            'print' => 'printer',
                                            'full_control' => 'shield-lock',
                                        ];
                                    @endphp
                                    <span
                                        class="badge bg-{{ $permissionColors[$share->permission_level] ?? 'secondary' }} py-2 px-3">
                                        <i
                                            class="bi bi-{{ $permissionIcons[$share->permission_level] ?? 'question' }} me-1"></i>
                                        {{ ucfirst($share->permission_level) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($share->created_at && method_exists($share->created_at, 'diffForHumans'))
                                        <span class="fw-medium">{{ $share->created_at->format('M d, Y') }}</span>
                                        <br>
                                        <small class="text-muted"
                                            title="{{ $share->created_at->format('Y-m-d H:i:s') }}">
                                            {{ $share->created_at->diffForHumans() }}
                                        </small>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if ($share->expires_at)
                                        @if (method_exists($share->expires_at, 'isPast') && $share->expires_at->isPast())
                                            <span class="badge bg-danger py-2 px-3">
                                                <i class="bi bi-clock-history me-1"></i> Expired
                                            </span>
                                        @elseif(method_exists($share->expires_at, 'diffForHumans'))
                                            <span class="badge bg-warning py-2 px-3">
                                                <i class="bi bi-hourglass-split me-1"></i>
                                                {{ $share->expires_at->diffForHumans() }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge bg-success py-2 px-3">
                                            <i class="bi bi-infinity me-1"></i> Never
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('files.show', $share->file) }}"
                                            class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if (in_array($share->permission_level, ['download', 'edit', 'full_control']))
                                            <a href="{{ route('files.download', $share->file) }}"
                                                class="btn btn-sm btn-outline-success" title="Download">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        @endif
                                        @if (in_array($share->permission_level, ['edit', 'full_control']))
                                            <a href="{{ route('files.edit', $share->file) }}"
                                                class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-share display-1 text-muted"></i>
                                    <h5 class="mt-3">No Shared Files Found</h5>
                                    <p class="text-muted mb-3">No one has shared any files with you yet.</p>
                                    <a href="{{ route('files.index') }}" class="btn btn-primary">
                                        <i class="bi bi-arrow-left me-2"></i> Go to Files
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if (method_exists($shares, 'links') && $shares->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Showing {{ $shares->firstItem() }} to {{ $shares->lastItem() }} of {{ $shares->total() }} entries
                    </div>
                    <div>
                        {{ $shares->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
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

        .stat-card {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--box-shadow-hover);
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Export table function
        function exportTable() {
            const table = document.getElementById('shared-files-table');
            const rows = table.querySelectorAll('tr');
            let csv = [];

            // Get headers
            const headers = [];
            table.querySelectorAll('thead th').forEach(th => {
                headers.push(th.innerText);
            });
            csv.push(headers.join(','));

            // Get data
            rows.forEach(row => {
                if (row.parentElement.tagName === 'TBODY') {
                    const rowData = [];
                    row.querySelectorAll('td').forEach((td, index) => {
                        if (index < 5) { // Skip actions column (index 5)
                            // Clean the data
                            let text = td.innerText.replace(/,/g, ';').replace(/\n/g, ' ').trim();
                            rowData.push(text);
                        }
                    });
                    csv.push(rowData.join(','));
                }
            });

            // Download CSV
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], {
                type: 'text/csv'
            });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'shared-files-{{ date('Y-m-d') }}.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        }

        // Initialize DataTables
        $(document).ready(function() {
            if (!$.fn.DataTable.isDataTable('#shared-files-table')) {
                $('#shared-files-table').DataTable({
                    responsive: true,
                    paging: false,
                    searching: false,
                    info: false,
                    ordering: true,
                    columnDefs: [{
                            orderable: false,
                            targets: 5
                        } // Actions column
                    ]
                });
            }
        });
    </script>
@endpush
