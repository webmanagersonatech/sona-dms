@extends('layouts.app')

@section('title', 'File Management')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                <i class="fas fa-file-alt"></i>
                                File Management
                            </h4>
                            <p class="text-muted mb-0">
                                Manage all system files
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Files List</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 300px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search files...">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" action="{{ route('admin.files') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Department</label>
                                    <select name="department_id" class="form-control">
                                        <option value="">All Departments</option>
                                        @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Owner</label>
                                    <select name="owner_id" class="form-control">
                                        <option value="">All Users</option>
                                        @foreach($owners as $owner)
                                        <option value="{{ $owner->id }}" {{ request('owner_id') == $owner->id ? 'selected' : '' }}>
                                            {{ $owner->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                                        <option value="shared" {{ request('status') == 'shared' ? 'selected' : '' }}>Shared</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="d-flex">
                                        <button type="submit" class="btn btn-primary mr-2">
                                            <i class="fas fa-filter"></i> Filter
                                        </button>
                                        <a href="{{ route('admin.files') }}" class="btn btn-secondary">
                                            <i class="fas fa-redo"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Bulk Actions -->
                    <form action="{{ route('admin.files.bulk-actions') }}" method="POST" id="bulkActionsForm" class="mb-3">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <select name="action" class="form-control" required>
                                    <option value="">Select Action</option>
                                    <option value="archive">Archive Selected</option>
                                    <option value="restore">Restore Selected</option>
                                    <option value="delete">Delete Selected</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary" onclick="return confirmBulkAction()">
                                    <i class="fas fa-play"></i> Apply
                                </button>
                            </div>
                            <div class="col-md-6 text-right">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="selectAll">
                                    <label class="custom-control-label" for="selectAll">Select All</label>
                                </div>
                            </div>
                        </div>

                        <!-- Files Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" id="selectAllCheckbox">
                                        </th>
                                        <th>File Name</th>
                                        <th>Owner</th>
                                        <th>Department</th>
                                        <th>Type</th>
                                        <th>Size</th>
                                        <th>Uploaded</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($files as $file)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="files[]" value="{{ $file->id }}" class="file-checkbox">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="mr-2">
                                                    @if($file->extension === 'pdf')
                                                    <i class="fas fa-file-pdf text-danger"></i>
                                                    @elseif(in_array($file->extension, ['doc', 'docx']))
                                                    <i class="fas fa-file-word text-primary"></i>
                                                    @elseif(in_array($file->extension, ['jpg', 'jpeg', 'png', 'gif']))
                                                    <i class="fas fa-file-image text-success"></i>
                                                    @else
                                                    <i class="fas fa-file text-secondary"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <strong>{{ $file->original_name }}</strong>
                                                    @if($file->description)
                                                    <br>
                                                    <small class="text-muted">{{ Str::limit($file->description, 30) }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ $file->owner->name }}
                                            <br>
                                            <small class="text-muted">{{ $file->owner->email }}</small>
                                        </td>
                                        <td>{{ $file->department->name }}</td>
                                        <td>
                                            <span class="badge badge-info">{{ strtoupper($file->extension) }}</span>
                                        </td>
                                        <td>{{ $file->formatted_size }}</td>
                                        <td>
                                            {{ $file->created_at->format('M d, Y') }}
                                            <br>
                                            <small>{{ $file->created_at->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            @if($file->is_archived)
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
                                                <a href="{{ route('files.show', $file) }}" class="btn btn-sm btn-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($file->is_archived)
                                                <form action="{{ route('files.restore', $file) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-success" title="Restore">
                                                        <i class="fas fa-redo"></i>
                                                    </button>
                                                </form>
                                                @else
                                                <form action="{{ route('files.archive', $file) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-secondary" title="Archive">
                                                        <i class="fas fa-archive"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <i class="fas fa-file fa-3x text-muted mb-3"></i>
                                            <h5>No files found</h5>
                                            <p class="text-muted">Try changing your filter criteria</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </form>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $files->links() }}
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="small text-muted">
                                <i class="fas fa-info-circle"></i>
                                Showing {{ $files->firstItem() }} to {{ $files->lastItem() }} of {{ $files->total() }} files
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-sm btn-outline-primary" onclick="exportFiles()">
                                <i class="fas fa-download"></i> Export File List
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Bulk selection
document.getElementById('selectAllCheckbox').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.file-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.file-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    document.getElementById('selectAllCheckbox').checked = this.checked;
});

function confirmBulkAction() {
    const selectedCount = document.querySelectorAll('.file-checkbox:checked').length;
    if (selectedCount === 0) {
        alert('Please select at least one file.');
        return false;
    }
    
    const action = document.querySelector('select[name="action"]').value;
    if (!action) {
        alert('Please select an action.');
        return false;
    }
    
    return confirm(`Are you sure you want to ${action} ${selectedCount} file(s)?`);
}

function exportFiles() {
    // Collect filter parameters
    const params = new URLSearchParams({
        department_id: document.querySelector('select[name="department_id"]').value,
        owner_id: document.querySelector('select[name="owner_id"]').value,
        status: document.querySelector('select[name="status"]').value,
        export: 'csv'
    });

    // Create download URL
    const url = '{{ route("admin.files") }}?' + params.toString();
    window.open(url, '_blank');
}
</script>
@endpush