@extends('layouts.app')

@section('title', 'Files')

@section('content')
<<<<<<< HEAD
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
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($files->total()) }}</h3>
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
                    <h3>{{ number_format($stats['shared_with_me'] ?? 0) }}</h3>
                    <p>Shared With Me</p>
                </div>
                <div class="stat-icon success">
                    <i class="bi bi-share"></i>
                </div>
                <a href="{{ route('files.shared-with-me') }}" class="stretched-link"></a>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($files->sum('download_count')) }}</h3>
                    <p>Total Downloads</p>
                </div>
                <div class="stat-icon info">
                    <i class="bi bi-download"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($files->sum('view_count')) }}</h3>
                    <p>Total Views</p>
                </div>
                <div class="stat-icon warning">
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
                    <a class="nav-link {{ !request()->has('filter') || request('filter') == 'all' ? 'active' : '' }}"
                        href="{{ route('files.index') }}">
                        <i class="bi bi-files me-1"></i> All Files
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('filter') == 'my-files' ? 'active' : '' }}"
                        href="{{ route('files.index', ['filter' => 'my-files']) }}">
                        <i class="bi bi-person me-1"></i> My Files
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('filter') == 'shared-with-me' ? 'active' : '' }}"
                        href="{{ route('files.index', ['filter' => 'shared-with-me']) }}">
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
                @if (request('filter') == 'shared-with-me')
                    Files Shared With Me
                @elseif(request('filter') == 'my-files')
                    My Files
                @else
                    All Files
                @endif
            </h5>
            <div class="btn-group">
                <a href="{{ route('files.by-permission', 'view') }}" class="btn btn-sm btn-outline-info">
                    <i class="bi bi-eye"></i> View Only
                </a>
                <a href="{{ route('files.by-permission', 'download') }}" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-download"></i> Downloadable
                </a>
                <a href="{{ route('files.by-permission', 'edit') }}" class="btn btn-sm btn-outline-warning">
                    <i class="bi bi-pencil"></i> Editable
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <form method="GET" action="{{ route('files.index') }}" class="row g-3 mb-4">
                @if (request('filter'))
                    <input type="hidden" name="filter" value="{{ request('filter') }}">
                @endif
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search files..."
                            value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="image" {{ request('type') == 'image' ? 'selected' : '' }}>Images</option>
                        <option value="document" {{ request('type') == 'document' ? 'selected' : '' }}>Documents</option>
                        <option value="spreadsheet" {{ request('type') == 'spreadsheet' ? 'selected' : '' }}>Spreadsheets
                        </option>
                        <option value="presentation" {{ request('type') == 'presentation' ? 'selected' : '' }}>
                            Presentations</option>
                        <option value="archive" {{ request('type') == 'archive' ? 'selected' : '' }}>Archives</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('files.index') }}" class="btn btn-secondary w-100">
                        <i class="bi bi-eraser"></i> Clear
                    </a>
                </div>
            </form>

            <!-- Files Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable">
                    <thead>
                        <tr>
                            <th>File</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th>Owner</th>
                            <th>Shared By</th>
                            <th>Permission</th>
                            <th>Uploaded</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($files as $file)
                            @php
                                $share = $file->shares->where('shared_with', Auth::id())->first();
                                $filePermission =
                                    $file->owner_id === Auth::id()
                                        ? 'full_control'
                                        : ($share
                                            ? $share->permission_level
                                            : 'none');
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi {{ $file->icon ?? 'bi-file' }} me-2 fs-4"
                                            style="color: var(--primary);"></i>
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
                                                width="30" height="30">
                                        @else
                                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2"
                                                style="width: 30px; height: 30px;">
                                                <span
                                                    class="text-white small">{{ $file->owner ? strtoupper(substr($file->owner->name, 0, 1)) : '?' }}</span>
                                            </div>
                                        @endif
                                        <span>{{ $file->owner->name ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if ($share && $share->sharedBy)
                                        <div class="d-flex align-items-center">
                                            @if ($share->sharedBy->avatar)
                                                <img src="{{ Storage::url($share->sharedBy->avatar) }}"
                                                    alt="{{ $share->sharedBy->name }}" class="rounded-circle me-2"
                                                    width="25" height="25">
                                            @else
                                                <div class="rounded-circle bg-info d-flex align-items-center justify-content-center me-2"
                                                    style="width: 25px; height: 25px;">
                                                    <span
                                                        class="text-white small">{{ strtoupper(substr($share->sharedBy->name, 0, 1)) }}</span>
                                                </div>
                                            @endif
                                            <span class="small">{{ $share->sharedBy->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($file->owner_id === Auth::id())
                                        <span class="badge bg-danger">Owner</span>
                                    @else
                                        @php
                                            $permissionColors = [
                                                'view' => 'info',
                                                'download' => 'success',
                                                'edit' => 'warning',
                                                'print' => 'secondary',
                                                'full_control' => 'danger',
                                            ];
                                        @endphp
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
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('files.show', $file) }}" class="btn btn-outline-primary"
                                            title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        @if (in_array($filePermission, ['download', 'edit', 'full_control']) || $file->owner_id === Auth::id())
                                            <a href="{{ route('files.download', $file) }}"
                                                class="btn btn-outline-success" title="Download">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        @endif

                                        @if ($file->owner_id === Auth::id())
                                            <a href="{{ route('files.edit', $file) }}" class="btn btn-outline-info"
                                                title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @if ($file->status === 'active')
                                                <button class="btn btn-outline-warning" title="Archive"
                                                    onclick="archiveFile({{ $file->id }})">
                                                    <i class="bi bi-archive"></i>
                                                </button>
                                            @else
                                                <button class="btn btn-outline-success" title="Restore"
                                                    onclick="restoreFile({{ $file->id }})">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-folder2-open display-4 text-muted"></i>
                                    <p class="mt-2">No files found</p>
                                    @if (request('filter') == 'shared-with-me')
                                        <p class="text-muted">No one has shared any files with you yet.</p>
                                    @else
                                        <a href="{{ route('files.create') }}" class="btn btn-primary">
                                            <i class="bi bi-upload"></i> Upload Your First File
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    Showing {{ $files->firstItem() ?? 0 }} to {{ $files->lastItem() ?? 0 }} of {{ $files->total() }}
                    entries
                </div>
                <div>
                    {{ $files->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function archiveFile(fileId) {
            Swal.fire({
                title: 'Archive File',
                text: 'Are you sure you want to archive this file?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, archive it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('/files/' + fileId + '/archive', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    }).then(() => {
                        window.location.reload();
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
                confirmButtonText: 'Yes, restore it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('/files/' + fileId + '/restore', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    }).then(() => {
                        window.location.reload();
                    });
                }
            });
        }
    </script>
@endpush
=======
    <div class="container-fluid">

        <!-- Button Row -->
        <div class="row mb-3">
            <div class="col-12 text-right">
                <a href="{{ route('files.create') }}" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Upload File
                </a>
            </div>
        </div>

        <!-- Table Row -->
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover datatable">
                                <thead>
                                    <tr>
                                        <th>File Name</th>
                                        <th>Type</th>
                                        <th>Size</th>
                                        <th>Owner</th>
                                        <th>Department</th>
                                        <th>Uploaded</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($files as $file)
                                        <tr>
                                            <td>
                                                <i
                                                    class="fas fa-file-{{ $file->extension === 'pdf' ? 'pdf' : 'word' }} text-danger"></i>
                                                {{ $file->original_name }}
                                                @if ($file->description)
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ Str::limit($file->description, 50) }}
                                                    </small>
                                                @endif
                                            </td>

                                            <td>{{ strtoupper($file->extension) }}</td>
                                            <td>{{ $file->formatted_size }}</td>
                                            <td>{{ $file->owner->name }}</td>
                                            <td>{{ $file->department->name }}</td>
                                            <td>{{ $file->created_at->format('M d, Y') }}</td>

                                            <td>
                                                @if ($file->is_archived)
                                                    <span class="badge badge-secondary">Archived</span>
                                                @elseif($file->isExpired())
                                                    <span class="badge badge-warning">Expired</span>
                                                @elseif($file->is_shared)
                                                    <span class="badge badge-info">Shared</span>
                                                @else
                                                    <span class="badge badge-success">Active</span>
                                                @endif
                                            </td>

                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('files.show', $file) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                    @if ($file->canBeAccessedBy(auth()->user()))
                                                        <a href="{{ route('files.download', $file) }}"
                                                            class="btn btn-sm btn-success">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    @endif

                                                    @if ($file->owner_id === auth()->id())
                                                        <a href="{{ route('files.shares', $file) }}"
                                                            class="btn btn-sm btn-warning">
                                                            <i class="fas fa-share-alt"></i>
                                                        </a>
                                                    @endif
                                                </div>
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

    </div>
@endsection
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
