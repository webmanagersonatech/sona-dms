@extends('layouts.app')

@section('title', ucfirst($permission) . ' Files')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            @switch($permission)
                                @case('view')
                                    <i class="bi bi-eye text-info"></i> Files You Can View
                                @break

                                @case('download')
                                    <i class="bi bi-download text-success"></i> Files You Can Download
                                @break

                                @case('edit')
                                    <i class="bi bi-pencil text-warning"></i> Files You Can Edit
                                @break

                                @case('print')
                                    <i class="bi bi-printer text-secondary"></i> Files You Can Print
                                @break

                                @case('full_control')
                                    <i class="bi bi-shield-lock text-danger"></i> Your Files (Full Control)
                                @break
                            @endswitch
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('files.index') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-arrow-left"></i> Back to All Files
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Permission Stats Cards -->
                        <div class="row mb-4">
                            <div class="col-md-2 col-6">
                                <a href="{{ route('files.by-permission', 'view') }}" class="text-decoration-none">
                                    <div class="small-box bg-info">
                                        <div class="inner">
                                            <h3>{{ $counts['view'] ?? 0 }}</h3>
                                            <p>View Only</p>
                                        </div>
                                        <div class="icon">
                                            <i class="bi bi-eye"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-2 col-6">
                                <a href="{{ route('files.by-permission', 'download') }}" class="text-decoration-none">
                                    <div class="small-box bg-success">
                                        <div class="inner">
                                            <h3>{{ $counts['download'] ?? 0 }}</h3>
                                            <p>Download</p>
                                        </div>
                                        <div class="icon">
                                            <i class="bi bi-download"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-2 col-6">
                                <a href="{{ route('files.by-permission', 'edit') }}" class="text-decoration-none">
                                    <div class="small-box bg-warning">
                                        <div class="inner">
                                            <h3>{{ $counts['edit'] ?? 0 }}</h3>
                                            <p>Edit</p>
                                        </div>
                                        <div class="icon">
                                            <i class="bi bi-pencil"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-2 col-6">
                                <a href="{{ route('files.by-permission', 'print') }}" class="text-decoration-none">
                                    <div class="small-box bg-secondary">
                                        <div class="inner">
                                            <h3>{{ $counts['print'] ?? 0 }}</h3>
                                            <p>Print</p>
                                        </div>
                                        <div class="icon">
                                            <i class="bi bi-printer"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-2 col-6">
                                <a href="{{ route('files.by-permission', 'full_control') }}" class="text-decoration-none">
                                    <div class="small-box bg-danger">
                                        <div class="inner">
                                            <h3>{{ $counts['full_control'] ?? 0 }}</h3>
                                            <p>My Files</p>
                                        </div>
                                        <div class="icon">
                                            <i class="bi bi-shield-lock"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Search and Filter -->
                        <form method="GET" action="{{ route('files.by-permission', $permission) }}" class="mb-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Search files..." value="{{ request('search') }}">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="bi bi-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select name="type" class="form-select">
                                        <option value="">All Types</option>
                                        <option value="image" {{ request('type') == 'image' ? 'selected' : '' }}>Images
                                        </option>
                                        <option value="document" {{ request('type') == 'document' ? 'selected' : '' }}>
                                            Documents</option>
                                        <option value="spreadsheet"
                                            {{ request('type') == 'spreadsheet' ? 'selected' : '' }}>Spreadsheets</option>
                                        <option value="presentation"
                                            {{ request('type') == 'presentation' ? 'selected' : '' }}>Presentations
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <a href="{{ route('files.by-permission', $permission) }}"
                                        class="btn btn-secondary w-100">
                                        <i class="bi bi-eraser"></i> Clear
                                    </a>
                                </div>
                            </div>
                        </form>

                        <!-- Files Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>File</th>
                                        <th>Owner</th>
                                        <th>Size</th>
                                        <th>Type</th>
                                        <th>Uploaded</th>
                                        <th>Permission</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($files as $file)
                                        @php
                                            $filePermission =
                                                $file->owner_id === Auth::id()
                                                    ? 'full_control'
                                                    : ($file->shares && $file->shares->first()
                                                        ? $file->shares->first()->permission_level
                                                        : 'none');
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi {{ $file->icon ?? 'bi-file' }} me-2 fs-4"
                                                        style="color: var(--primary);"></i>
                                                    <div>
                                                        <a href="{{ route('files.show', $file) }}"
                                                            class="text-decoration-none">
                                                            {{ Str::limit($file->name, 40) }}
                                                        </a>
                                                        @if ($file->is_encrypted)
                                                            <span class="badge bg-warning ms-1">
                                                                <i class="bi bi-lock"></i>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if ($file->owner && $file->owner->avatar)
                                                        <img src="{{ Storage::url($file->owner->avatar) }}"
                                                            class="rounded-circle me-2" width="25" height="25">
                                                    @else
                                                        <div class="rounded-circle bg-info d-flex align-items-center justify-content-center me-2"
                                                            style="width: 25px; height: 25px;">
                                                            <span
                                                                class="text-white small">{{ $file->owner ? strtoupper(substr($file->owner->name, 0, 1)) : '?' }}</span>
                                                        </div>
                                                    @endif
                                                    {{ $file->owner->name ?? 'Unknown' }}
                                                </div>
                                            </td>
                                            <td>{{ $file->size_for_humans ?? '0 B' }}</td>
                                            <td><span
                                                    class="badge bg-secondary">{{ strtoupper($file->extension ?? 'N/A') }}</span>
                                            </td>
                                            <td>
                                                @if ($file->created_at && method_exists($file->created_at, 'diffForHumans'))
                                                    <span title="{{ $file->created_at->format('Y-m-d H:i:s') }}">
                                                        {{ $file->created_at->diffForHumans() }}
                                                    </span>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                @if ($file->owner_id === Auth::id())
                                                    <span class="badge bg-danger">Owner</span>
                                                @else
                                                    <span
                                                        class="badge bg-{{ $filePermission === 'view' ? 'info' : ($filePermission === 'download' ? 'success' : 'warning') }}">
                                                        {{ ucfirst($filePermission) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('files.show', $file) }}"
                                                        class="btn btn-outline-primary" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>

                                                    @if (in_array($filePermission, ['download', 'edit', 'full_control']) || $file->owner_id === Auth::id())
                                                        <a href="{{ route('files.download', $file) }}"
                                                            class="btn btn-outline-success" title="Download">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <i class="bi bi-files display-4 text-muted"></i>
                                                <p class="mt-2">No files found with {{ $permission }} permission</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if (method_exists($files, 'links'))
                            <div class="mt-3">
                                {{ $files->appends(request()->query())->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
