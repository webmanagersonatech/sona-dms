@extends('layouts.app')

@section('title', 'My Files')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-person text-info"></i> My Files
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('files.create') }}" class="btn btn-sm btn-success">
                                <i class="bi bi-upload"></i> Upload New File
                            </a>
                            <a href="{{ route('files.index') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-arrow-left"></i> Back to All Files
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search and Filter -->
                        <form method="GET" action="{{ route('files.my-files') }}" class="mb-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Search my files..." value="{{ request('search') }}">
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
                                            {{ request('type') == 'presentation' ? 'selected' : '' }}>Presentations</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <a href="{{ route('files.my-files') }}" class="btn btn-secondary w-100">
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
                                        <th>Size</th>
                                        <th>Type</th>
                                        <th>Uploaded</th>
                                        <th>Downloads</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($files as $file)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi {{ $file->icon ?? 'bi-file' }} me-2 fs-4"
                                                        style="color: var(--primary);"></i>
                                                    <div>
                                                        <a href="{{ route('files.show', $file) }}"
                                                            class="text-decoration-none">
                                                            {{ Str::limit($file->name ?? 'Unnamed', 40) }}
                                                        </a>
                                                        @if ($file->is_encrypted ?? false)
                                                            <span class="badge bg-warning ms-1">
                                                                <i class="bi bi-lock"></i>
                                                            </span>
                                                        @endif
                                                    </div>
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
                                            <td><span
                                                    class="badge bg-info">{{ number_format($file->download_count ?? 0) }}</span>
                                            </td>
                                            <td>
                                                @if ($file->status === 'active')
                                                    <span class="badge bg-success">Active</span>
                                                @elseif($file->status === 'archived')
                                                    <span class="badge bg-warning">Archived</span>
                                                @else
                                                    <span
                                                        class="badge bg-secondary">{{ ucfirst($file->status ?? 'unknown') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('files.show', $file) }}"
                                                        class="btn btn-outline-primary" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('files.download', $file) }}"
                                                        class="btn btn-outline-success" title="Download">
                                                        <i class="bi bi-download"></i>
                                                    </a>
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
                                                    <button class="btn btn-outline-danger" title="Delete"
                                                        onclick="deleteFile({{ $file->id }})">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <i class="bi bi-files display-4 text-muted"></i>
                                                <p class="mt-2">You haven't uploaded any files yet</p>
                                                <a href="{{ route('files.create') }}" class="btn btn-primary">
                                                    <i class="bi bi-upload"></i> Upload Your First File
                                                </a>
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

@push('scripts')
    <script>
        function deleteFile(fileId) {
            Swal.fire({
                title: 'Delete File',
                text: 'Are you sure you want to delete this file?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('/files/' + fileId, {
                        method: 'DELETE',
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
