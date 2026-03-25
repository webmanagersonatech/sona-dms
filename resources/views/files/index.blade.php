@extends('layouts.app')

@section('title', 'Files')

@section('content')
    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">File Management</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Files</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('files.create') }}" class="btn btn-primary">
            <i class="bi bi-upload me-2"></i> Upload File
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card primary">
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_files'] ?? $files->total()) }}</h3>
                    <p>Total Files</p>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-files"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card success">
                <div class="stat-info">
                    <h3>{{ number_format($stats['shared_with_me'] ?? 0) }}</h3>
                    <p>Shared With Me</p>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-share"></i>
                </div>
                <a href="{{ route('files.shared-with-me') }}" class="stretched-link"></a>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card info">
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_downloads'] ?? $files->sum('download_count')) }}</h3>
                    <p>Total Downloads</p>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-download"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card warning">
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_views'] ?? $files->sum('view_count')) }}</h3>
                    <p>Total Views</p>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-eye"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="card mb-4">
        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    @php
                        $allFilesParams = request()->except(['filter', 'page']);
                    @endphp
                    <a class="nav-link {{ Route::currentRouteName() === 'files.index' && !request()->has('filter') ? 'active' : '' }}"
                        href="{{ route('files.index', $allFilesParams) }}">
                        <i class="bi bi-files me-1"></i> All Files
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() === 'files.my-files' ? 'active' : '' }}"
                        href="{{ route('files.my-files', request()->except(['filter', 'page'])) }}">
                        <i class="bi bi-person me-1"></i> My Files
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() === 'files.shared-with-me' ? 'active' : '' }}"
                        href="{{ route('files.shared-with-me', request()->except(['filter', 'page'])) }}">
                        <i class="bi bi-share me-1"></i> Shared With Me
                        @if (($stats['shared_with_me'] ?? 0) > 0)
                            <span class="badge bg-primary ms-1">{{ $stats['shared_with_me'] }}</span>
                        @endif
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Files Table Card -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                @if (Route::currentRouteName() === 'files.shared-with-me')
                    Files Shared With Me
                @elseif(Route::currentRouteName() === 'files.my-files')
                    My Files
                @else
                    All Files
                @endif
            </h5>
            @if (Route::currentRouteName() !== 'files.shared-with-me')
                <div class="btn-group">
                    @php
                        $viewParams = array_merge(request()->except(['permission', 'page']), ['permission' => 'view']);
                        $downloadParams = array_merge(request()->except(['permission', 'page']), [
                            'permission' => 'download',
                        ]);
                        $editParams = array_merge(request()->except(['permission', 'page']), ['permission' => 'edit']);
                    @endphp
                    <a href="{{ route('files.index', $viewParams) }}"
                        class="btn btn-sm {{ request('permission') == 'view' ? 'btn-info' : 'btn-outline-info' }}">
                        <i class="bi bi-eye"></i> View Only
                    </a>
                    <a href="{{ route('files.index', $downloadParams) }}"
                        class="btn btn-sm {{ request('permission') == 'download' ? 'btn-success' : 'btn-outline-success' }}">
                        <i class="bi bi-download"></i> Downloadable
                    </a>
                    <a href="{{ route('files.index', $editParams) }}"
                        class="btn btn-sm {{ request('permission') == 'edit' ? 'btn-warning' : 'btn-outline-warning' }}">
                        <i class="bi bi-pencil"></i> Editable
                    </a>
                </div>
            @endif
        </div>
        <div class="card-body">
            <!-- Search and Filter Form -->
            <form method="GET"
                action="{{ Route::currentRouteName() === 'files.shared-with-me' ? route('files.shared-with-me') : (Route::currentRouteName() === 'files.my-files' ? route('files.my-files') : route('files.index')) }}"
                class="row g-3 mb-4" id="filterForm">
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search files by name..."
                            value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="type" class="form-select" onchange="this.form.submit()">
                        <option value="">All File Types</option>
                        <option value="image" {{ request('type') == 'image' ? 'selected' : '' }}>Images</option>
                        <option value="document" {{ request('type') == 'document' ? 'selected' : '' }}>Documents</option>
                        <option value="spreadsheet" {{ request('type') == 'spreadsheet' ? 'selected' : '' }}>Spreadsheets
                        </option>
                        <option value="presentation" {{ request('type') == 'presentation' ? 'selected' : '' }}>
                            Presentations</option>
                        <option value="archive" {{ request('type') == 'archive' ? 'selected' : '' }}>Archives</option>
                        <option value="pdf" {{ request('type') == 'pdf' ? 'selected' : '' }}>PDF</option>
                        <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="sort" class="form-select" onchange="this.form.submit()">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)
                        </option>
                        <option value="size_desc" {{ request('sort') == 'size_desc' ? 'selected' : '' }}>Largest First
                        </option>
                        <option value="size_asc" {{ request('sort') == 'size_asc' ? 'selected' : '' }}>Smallest First
                        </option>
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <a href="{{ Route::currentRouteName() === 'files.shared-with-me' ? route('files.shared-with-me') : (Route::currentRouteName() === 'files.my-files' ? route('files.my-files') : route('files.index')) }}"
                            class="btn btn-secondary w-50">
                            <i class="bi bi-eraser"></i> Clear
                        </a>
                        <button type="submit" class="btn btn-primary w-50">
                            <i class="bi bi-filter"></i> Apply
                        </button>
                    </div>
                </div>
            </form>

            <!-- Files Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>File</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th>Owner</th>
                            @if (Route::currentRouteName() === 'files.shared-with-me')
                                <th>Shared By</th>
                            @endif
                            <th>Permission</th>
                            <th>Uploaded</th>
                            <th>Actions</th>
                    </thead>
                    <tbody>
                        @forelse($files as $file)
                            @php
                                $share = $file->shares->where('shared_with', Auth::id())->first();
                                $isOwner = $file->owner_id === Auth::id();
                                $filePermission = $isOwner
                                    ? 'full_control'
                                    : ($share
                                        ? $share->permission_level
                                        : 'none');

                                $permissionColors = [
                                    'view' => 'info',
                                    'download' => 'success',
                                    'edit' => 'warning',
                                    'print' => 'secondary',
                                    'full_control' => 'danger',
                                ];

                                $fileIcon =
                                    $file->icon ??
                                    match ($file->extension) {
                                        'pdf' => 'bi-file-pdf',
                                        'doc', 'docx' => 'bi-file-word',
                                        'xls', 'xlsx' => 'bi-file-excel',
                                        'ppt', 'pptx' => 'bi-file-ppt',
                                        'jpg', 'jpeg', 'png', 'gif' => 'bi-file-image',
                                        'zip', 'rar', '7z' => 'bi-file-zip',
                                        'txt' => 'bi-file-text',
                                        default => 'bi-file',
                                    };
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi {{ $fileIcon }} me-2 fs-4 text-primary"></i>
                                        <div>
                                            <a href="{{ route('files.show', $file) }}"
                                                class="text-decoration-none fw-medium">
                                                {{ Str::limit($file->name, 30) }}
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
                                        @if ($file->owner && $file->owner->avatar)
                                            <img src="{{ Storage::url($file->owner->avatar) }}"
                                                alt="{{ $file->owner->name }}" class="rounded-circle me-2"
                                                width="30" height="30" style="object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2"
                                                style="width: 30px; height: 30px;">
                                                <span class="text-white small fw-bold">
                                                    {{ $file->owner ? strtoupper(substr($file->owner->name, 0, 1)) : '?' }}
                                                </span>
                                            </div>
                                        @endif
                                        <span class="small">{{ $file->owner->name ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                                @if (Route::currentRouteName() === 'files.shared-with-me')
                                    <td>
                                        @if ($share && $share->sharedBy)
                                            <div class="d-flex align-items-center">
                                                @if ($share->sharedBy->avatar)
                                                    <img src="{{ Storage::url($share->sharedBy->avatar) }}"
                                                        alt="{{ $share->sharedBy->name }}" class="rounded-circle me-2"
                                                        width="25" height="25" style="object-fit: cover;">
                                                @else
                                                    <div class="rounded-circle bg-info d-flex align-items-center justify-content-center me-2"
                                                        style="width: 25px; height: 25px;">
                                                        <span class="text-white small fw-bold">
                                                            {{ strtoupper(substr($share->sharedBy->name, 0, 1)) }}
                                                        </span>
                                                    </div>
                                                @endif
                                                <span class="small">{{ $share->sharedBy->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                @endif
                                <td>
                                    @if ($isOwner)
                                        <span class="badge bg-danger">Owner</span>
                                    @else
                                        <span class="badge bg-{{ $permissionColors[$filePermission] ?? 'secondary' }}">
                                            {{ ucfirst($filePermission) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if ($file->created_at)
                                        <span title="{{ $file->created_at->format('Y-m-d H:i:s') }}">
                                            {{ $file->created_at->diffForHumans() }}
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('files.show', $file) }}" class="btn btn-outline-primary"
                                            title="View Details" data-bs-toggle="tooltip">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        @if (in_array($filePermission, ['download', 'edit', 'full_control']) || $isOwner)
                                            <a href="{{ route('files.download', $file) }}"
                                                class="btn btn-outline-success" title="Download"
                                                data-bs-toggle="tooltip">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        @endif

                                        @if ($filePermission === 'edit' || $isOwner)
                                            <a href="{{ route('files.edit', $file) }}" class="btn btn-outline-info"
                                                title="Edit" data-bs-toggle="tooltip">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endif

                                        @if ($isOwner)
                                            @if ($file->status === 'active')
                                                <button type="button" class="btn btn-outline-warning" title="Archive"
                                                    data-bs-toggle="tooltip" onclick="archiveFile({{ $file->id }})">
                                                    <i class="bi bi-archive"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-outline-success" title="Restore"
                                                    data-bs-toggle="tooltip" onclick="restoreFile({{ $file->id }})">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                            @endif

                                            <button type="button" class="btn btn-outline-danger" title="Delete"
                                                data-bs-toggle="tooltip" onclick="deleteFile({{ $file->id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ Route::currentRouteName() === 'files.shared-with-me' ? '8' : '7' }}"
                                    class="text-center py-5">
                                    <i class="bi bi-folder2-open display-1 text-muted"></i>
                                    <p class="mt-3 fs-5">No files found</p>
                                    @if (Route::currentRouteName() === 'files.shared-with-me')
                                        <p class="text-muted">No one has shared any files with you yet.</p>
                                    @elseif(request()->has('search') || request()->has('type') || request()->has('permission'))
                                        <p class="text-muted">Try adjusting your search or filter criteria.</p>
                                        <a href="{{ route('files.index') }}" class="btn btn-primary mt-2">
                                            <i class="bi bi-x-circle"></i> Clear Filters
                                        </a>
                                    @else
                                        <a href="{{ route('files.create') }}" class="btn btn-primary mt-2">
                                            <i class="bi bi-upload"></i> Upload Your First File
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination and Info -->
            @if ($files->total() > 0)
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center mt-4">
                    <div class="text-muted mb-3 mb-sm-0">
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
            border-radius: 12px;
            padding: 1.5rem;
            color: white;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
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

        .stat-card.primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-card.success {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .stat-card.info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .stat-card.warning {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        .table td {
            vertical-align: middle;
        }

        .btn-group-sm>.btn {
            padding: 0.25rem 0.5rem;
        }

        .nav-tabs .nav-link {
            color: #6c757d;
            border: none;
            padding: 0.75rem 1rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .nav-tabs .nav-link:hover {
            color: #495057;
            background-color: rgba(0, 0, 0, 0.05);
        }

        .nav-tabs .nav-link.active {
            color: #667eea;
            background: transparent;
            border-bottom: 2px solid #667eea;
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid #e9ecef;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        function archiveFile(fileId) {
            Swal.fire({
                title: 'Archive File',
                text: 'Are you sure you want to archive this file? Archived files can be restored later.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, archive it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('/files/' + fileId + '/archive', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Archived!',
                                    text: 'File has been archived successfully.',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                throw new Error(data.message || 'Something went wrong');
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'Error!',
                                text: error.message || 'Something went wrong. Please try again.',
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        });
                }
            });
        }

        function restoreFile(fileId) {
            Swal.fire({
                title: 'Restore File',
                text: 'Are you sure you want to restore this file?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, restore it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('/files/' + fileId + '/restore', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Restored!',
                                    text: 'File has been restored successfully.',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                throw new Error(data.message || 'Something went wrong');
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'Error!',
                                text: error.message || 'Something went wrong. Please try again.',
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        });
                }
            });
        }

        function deleteFile(fileId) {
            Swal.fire({
                title: 'Delete File',
                text: 'Are you sure you want to delete this file? This action cannot be undone!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('/files/' + fileId, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'File has been deleted successfully.',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                throw new Error(data.message || 'Something went wrong');
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'Error!',
                                text: error.message || 'Something went wrong. Please try again.',
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        });
                }
            });
        }
    </script>
@endpush
