@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Backup Management</h1>
    
    <!-- Create Backup Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Create New Backup</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.backups.create') }}">
                @csrf
                
                <div class="form-group">
                    <label for="backup_type">Backup Type</label>
                    <select class="form-control" id="backup_type" name="type" required>
                        <option value="full">Full Backup (Database + Files)</option>
                        <option value="database">Database Only</option>
                        <option value="files">Files Only</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-database"></i> Create Backup
                </button>
            </form>
        </div>
    </div>
    
    <!-- Existing Backups Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Existing Backups</h6>
        </div>
        <div class="card-body">
            @if(count($backups) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Filename</th>
                                <th>Size</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($backups as $backup)
                                <tr>
                                    <td>{{ $backup['filename'] }}</td>
                                    <td>{{ round($backup['size'] / 1024 / 1024, 2) }} MB</td>
                                    <td>{{ $backup['created_at']->format('Y-m-d H:i:s') }}</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info" 
                                           onclick="downloadBackup('{{ $backup['filename'] }}')">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                        
                                        <button type="button" class="btn btn-sm btn-warning" 
                                                data-toggle="modal" 
                                                data-target="#restoreModal{{ $loop->index }}">
                                            <i class="fas fa-redo"></i> Restore
                                        </button>
                                        
                                        <form method="POST" 
                                              action="{{ route('admin.backups.restore', $backup['filename']) }}" 
                                              style="display: inline;">
                                            @csrf
                                            <!-- Restore Confirmation Modal -->
                                            <div class="modal fade" id="restoreModal{{ $loop->index }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Confirm Restoration</h5>
                                                            <button type="button" class="close" data-dismiss="modal">
                                                                <span>&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Are you sure you want to restore from <strong>{{ $backup['filename'] }}</strong>?</p>
                                                            <p class="text-danger">
                                                                <i class="fas fa-exclamation-triangle"></i> 
                                                                Warning: This will overwrite current data!
                                                            </p>
                                                            <div class="form-group">
                                                                <label for="confirmation{{ $loop->index }}">Type "yes" to confirm</label>
                                                                <input type="text" class="form-control" 
                                                                       id="confirmation{{ $loop->index }}" 
                                                                       name="confirmation" required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-warning">Restore</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center">No backups found.</p>
            @endif
        </div>
    </div>
</div>

<script>
function downloadBackup(filename) {
    // Create a temporary form to download the file
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/backups/download/' + filename;
    form.style.display = 'none';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
</script>
@endsection