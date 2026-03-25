<<<<<<< HEAD
{{-- resources/views/files/show.blade.php --}}
@extends('layouts.app')

@section('title', $file->name)

@section('content')
=======
@extends('layouts.app')

@section('title', 'File Details')

@section('content')
<div class="container-fluid">
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
<<<<<<< HEAD
                    <h5 class="card-title mb-0">File Details</h5>
                    <div class="card-tools">
                        <div class="btn-group">
                            <a href="{{ route('files.download', $file) }}" class="btn btn-success btn-sm">
                                <i class="bi bi-download"></i> Download
                            </a>
                            @if ($file->owner_id === Auth::id() || Auth::user()->isSuperAdmin())
                                <button class="btn btn-primary btn-sm" onclick="showShareModal()">
                                    <i class="bi bi-share"></i> Share
                                </button>
                                <button class="btn btn-warning btn-sm" onclick="archiveFile()">
                                    <i class="bi bi-archive"></i> Archive
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="file-icon mb-3">
                                <i class="bi {{ $file->icon }}" style="font-size: 5rem;"></i>
                            </div>
                            @if ($file->is_encrypted)
                                <span class="badge bg-warning">
                                    <i class="bi bi-lock"></i> Encrypted
                                </span>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <table class="table">
                                <tr>
                                    <th style="width: 150px;">File Name</th>
                                    <td>{{ $file->original_name }}</td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td>{{ strtoupper($file->extension) }} ({{ $file->mime_type }})</td>
                                </tr>
                                <tr>
                                    <th>Size</th>
                                    <td>{{ $file->size_for_humans }}</td>
                                </tr>
                                <tr>
                                    <th>Owner</th>
                                    <td>
                                        {{ $file->owner->name }}
                                        <br>
                                        <small class="text-muted">{{ $file->owner->email }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Department</th>
                                    <td>{{ $file->department->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Uploaded</th>
                                    <td>{{ $file->created_at->format('F d, Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Accessed</th>
                                    <td>{{ $file->last_accessed_at?->diffForHumans() ?? 'Never' }}</td>
                                </tr>
                                <tr>
                                    <th>Downloads</th>
                                    <td>{{ $file->download_count }}</td>
                                </tr>
                                <tr>
                                    <th>Views</th>
                                    <td>{{ $file->view_count }}</td>
                                </tr>
                            </table>

                            @if ($file->description)
                                <div class="mt-3">
                                    <h6>Description</h6>
                                    <p class="text-muted">{{ $file->description }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- @if (in_array($file->extension, ['jpg', 'jpeg', 'png', 'gif', 'pdf']))
                        <hr>
                        <h6>Preview</h6>
                        <div class="text-center">
                            @if (in_array($file->extension, ['jpg', 'jpeg', 'png', 'gif']))
                                <img src="{{ route('files.download', $file) }}" alt="{{ $file->name }}"
                                    class="img-fluid" style="max-height: 400px;">
                            @elseif($file->extension === 'pdf')
                                <iframe src="{{ route('files.download', $file) }}" style="width: 100%; height: 500px;"
                                    frameborder="0"></iframe>
                            @endif
                        </div>
                    @endif --}}
                    @if ($file->is_previewable)
                        <div class="card mt-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">File Preview</h5>

                                <a href="{{ asset('storage/' . $file->path) }}" class="btn btn-sm btn-outline-primary"
                                    target="_blank">
                                    <i class="bi bi-arrows-fullscreen"></i> Full Screen
                                </a>
                            </div>

                            <div class="card-body text-center">

                                {{-- Image --}}
                                @if (in_array(strtolower($file->extension), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp']))
                                    <img src="{{ asset('storage/' . $file->path) }}" class="img-fluid"
                                        style="max-height:400px; object-fit:contain;">

                                    {{-- PDF --}}
                                @elseif(strtolower($file->extension) === 'pdf')
                                    <iframe src="{{ asset('storage/' . $file->path) }}#toolbar=0"
                                        style="width:100%; height:600px;">
                                    </iframe>

                                    {{-- Video --}}
                                @elseif(in_array(strtolower($file->extension), ['mp4', 'webm', 'ogg', 'mov', 'avi']))
                                    <video controls style="max-width:100%;">
                                        <source src="{{ asset('storage/' . $file->path) }}">
                                    </video>

                                    {{-- Audio --}}
                                @elseif(in_array(strtolower($file->extension), ['mp3', 'wav', 'ogg', 'm4a']))
                                    <audio controls style="width:100%;">
                                        <source src="{{ asset('storage/' . $file->path) }}">
                                    </audio>

                                    {{-- ALL OTHER FILES --}}
                                @else
                                    <iframe
                                        src="https://docs.google.com/gview?url={{ urlencode(asset('storage/' . $file->path)) }}&embedded=true"
                                        style="width:100%; height:600px;">
                                    </iframe>
                                @endif

                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Activity Log for this file -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">File Activity</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach ($file->activityLogs()->latest()->take(10)->get() as $log)
                            <div class="timeline-item">
                                <div class="timeline-badge bg-{{ $log->action === 'download' ? 'success' : 'info' }}">
                                    <i class="bi bi-{{ $log->action === 'download' ? 'download' : 'eye' }}"></i>
                                </div>
                                <div class="timeline-content">
                                    <strong>{{ $log->user->name }}</strong> {{ $log->description }}
                                    <br>
                                    <small class="text-muted">
                                        {{ $log->created_at->diffForHumans() }}
                                        @if ($log->ip_address)
                                            · {{ $log->ip_address }}
                                        @endif
                                    </small>
                                </div>
                            </div>
=======
                    <h3 class="card-title">File Preview</h3>
                </div>
                <div class="card-body">
                    @if(str_starts_with($file->mime_type, 'image/'))
                    <div class="text-center">
                        <img src="{{ route('files.preview', $file) }}" alt="{{ $file->original_name }}" class="img-fluid" style="max-height: 600px;">
                    </div>
                    @elseif($file->mime_type === 'application/pdf')
                    <iframe src="{{ route('files.preview', $file) }}" width="100%" height="600px"></iframe>
                    @elseif(in_array($file->mime_type, ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']))
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Document preview available for download only. Click the download button to view.
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Preview not available for this file type.
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">File Information</h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>File Name:</th>
                            <td>{{ $file->original_name }}</td>
                        </tr>
                        <tr>
                            <th>Type:</th>
                            <td>{{ strtoupper($file->extension) }} ({{ $file->mime_type }})</td>
                        </tr>
                        <tr>
                            <th>Size:</th>
                            <td>{{ $file->formatted_size }}</td>
                        </tr>
                        <tr>
                            <th>Owner:</th>
                            <td>{{ $file->owner->name }}</td>
                        </tr>
                        <tr>
                            <th>Department:</th>
                            <td>{{ $file->department->name }}</td>
                        </tr>
                        <tr>
                            <th>Uploaded:</th>
                            <td>{{ $file->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Encryption:</th>
                            <td>
                                <span class="badge badge-success">{{ $file->encryption_status }}</span>
                            </td>
                        </tr>
                        @if($file->description)
                        <tr>
                            <th>Description:</th>
                            <td>{{ $file->description }}</td>
                        </tr>
                        @endif
                        @if($file->expires_at)
                        <tr>
                            <th>Expires:</th>
                            <td>{{ $file->expires_at->format('M d, Y H:i') }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Status:</th>
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
                        </tr>
                    </table>

                    <div class="mt-3">
                        <a href="{{ route('files.download', $file) }}" class="btn btn-primary btn-block">
                            <i class="fas fa-download"></i> Download File
                        </a>
                        
                        @if($file->owner_id === auth()->id())
                        <div class="btn-group d-flex mt-2" role="group">
                            <a href="{{ route('files.shares', $file) }}" class="btn btn-warning flex-fill">
                                <i class="fas fa-share-alt"></i> Manage Shares
                            </a>
                            @if(!$file->is_archived)
                            <form action="{{ route('files.archive', $file) }}" method="POST" class="flex-fill">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-secondary w-100">
                                    <i class="fas fa-archive"></i> Archive
                                </button>
                            </form>
                            @else
                            <form action="{{ route('files.restore', $file) }}" method="POST" class="flex-fill">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-redo"></i> Restore
                                </button>
                            </form>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Recent Activity</h3>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($file->activityLogs()->latest('id')->limit(5)->get() as $activity)
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <small>{{ $activity->description }}</small>
                                <small class="text-muted">{{ $activity->performed_at->diffForHumans() }}</small>
                            </div>
                            @if($activity->user)
                            <small class="text-muted">By: {{ $activity->user->name }}</small>
                            @endif
                        </div>
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
<<<<<<< HEAD

        <div class="col-md-4">
            <!-- Sharing Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Shared With</h5>
                </div>
                <div class="card-body">
                    @if ($file->shares()->where('status', 'active')->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach ($file->shares()->where('status', 'active')->with('sharedWith')->get() as $share)
                                <li class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $share->sharedWith->name }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="bi bi-{{ $share->permission_level }}"></i>
                                                {{ ucfirst($share->permission_level) }}
                                                @if ($share->expires_at)
                                                    · Expires {{ $share->expires_at->diffForHumans() }}
                                                @endif
                                            </small>
                                        </div>
                                        @if ($file->owner_id === Auth::id())
                                            <form method="POST" action="{{ route('files.shares.revoke', $share) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Revoke access?')">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted text-center mb-0">Not shared with anyone</p>
                    @endif
                </div>
            </div>

            <!-- File Information -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">File ID</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">{{ $file->uuid }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Share Modal -->
    <div class="modal fade" id="shareModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('files.share', $file) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Share File</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Share with</label>
                            <select name="user_id" id="user_id" class="form-select" required>
                                <option value="">Select User</option>
                                @foreach (\App\Models\User::where('id', '!=', Auth::id())->get() as $user)
<option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
@endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="permission_level" class="form-label">Permission</label>
                        <select name="permission_level" id="permission_level" class="form-select" required>
                            <option value="view">View Only</option>
                            <option value="download">Download</option>
                            <option value="edit">Edit</option>
                            <option value="print">Print</option>
                            <option value="full_control">Full Control</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="expires_at" class="form-label">Expires At (Optional)</label>
                        <input type="datetime-local" name="expires_at" id="expires_at" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Share</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <style>
                                                                                                            .timeline {
                                                                                                                position: relative;
                                                                                                                padding: 10px 0;
                                                                                                            }
                                                                                                            .timeline-item {
                                                                                                                position: relative;
                                                                                                                padding-left: 50px;
                                                                                                                margin-bottom: 20px;
                                                                                                            }
                                                                                                            .timeline-badge {
                                                                                                                position: absolute;
                                                                                                                left: 0;
                                                                                                                top: 0;
                                                                                                                width: 35px;
                                                                                                                height: 35px;
                                                                                                                border-radius: 50%;
                                                                                                                display: flex;
                                                                                                                align-items: center;
                                                                                                                justify-content: center;
                                                                                                                color: white;
                                                                                                            }
                                                                                                            .timeline-content {
                                                                                                                background: #f8f9fa;
                                                                                                                padding: 10px 15px;
                                                                                                                border-radius: 8px;
                                                                                                            }
                                                                                                        </style>
@endpush

@push('scripts')
    <script>
        function showShareModal() {
            $('#shareModal').modal('show');
        }

        function archiveFile() {
            if (confirm('Are you sure you want to archive this file?')) {
                $.post('{{ route('files.archive', $file) }}', {
                    _token: '{{ csrf_token() }}'
                }).then(() => {
                    window.location.href = '{{ route('files.index') }}';
                });
            }
        }
    </script>
@endpush



)
=======
    </div>
</div>
@endsection
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
