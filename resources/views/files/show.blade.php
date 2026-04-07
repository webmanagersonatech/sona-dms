@extends('layouts.app')

@section('title', $file->name)

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">File Details</h5>
                    <div class="btn-group">

                        @if (in_array($permission, ['download', 'edit', 'full_control']))
                            <a href="{{ route('files.download', $file) }}" class="btn btn-success btn-sm">
                                <i class="bi bi-download"></i> Download
                            </a>
                        @endif
                        @if ($permission === 'edit' || $permission === 'full_control')
                            <a href="{{ route('files.edit', $file) }}" class="btn btn-info btn-sm text-white">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        @endif
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
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="file-icon mb-3">
                                <i class="bi {{ $file->icon ?? 'bi-file-earmark' }}" style="font-size: 5rem;"></i>
                            </div>
                            @if ($file->is_encrypted)
                                <span class="badge bg-warning">
                                    <i class="bi bi-lock"></i> Encrypted
                                </span>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <table class="table table-sm">
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
                                    <td>{{ $file->size_for_humans ?? $file->file_size }}</td>
                                </tr>
                                <tr>
                                    <th>Owner</th>
                                    <td>
                                        {{ $file->owner->name ?? 'Unknown' }}
                                        @if (isset($file->owner->email))
                                            <br>
                                            <small class="text-muted">{{ $file->owner->email }}</small>
                                        @endif
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
                                    <td>{{ $file->download_count ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th>Views</th>
                                    <td>{{ $file->view_count ?? 0 }}</td>
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

                    @php
                        $ext = strtolower($file->extension);
                        $previewableTypes = [
                            'jpg',
                            'jpeg',
                            'png',
                            'gif',
                            'bmp',
                            'svg',
                            'webp',
                            'pdf',
                            'mp4',
                            'webm',
                            'ogg',
                            'mov',
                            'avi',
                            'mp3',
                            'wav',
                            'm4a',
                            'txt',
                            'php',
                            'js',
                            'html',
                            'css',
                            'json',
                            'xml',
                            'sql',
                            'md',
                            'doc',
                            'docx',
                            'xls',
                            'xlsx',
                            'ppt',
                            'pptx',
                        ];
                        $isPreviewable = in_array($ext, $previewableTypes);
                    @endphp

                    @if ($isPreviewable)
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">File Preview</h5>
                            </div>
                            <div class="card-body text-center">
                                @if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp']))
                                    <img src="{{ route('files.preview', $file) }}" class="img-fluid"
                                        style="max-height:400px; object-fit:contain;">
                                @elseif ($ext === 'pdf')
                                    <embed src="{{ route('files.preview', $file) }}#toolbar=0&navpanes=0&scrollbar=0"
                                        type="application/pdf" width="100%" height="600px">
                                @elseif (in_array($ext, ['mp4', 'webm', 'ogg', 'mov', 'avi']))
                                    <video controls style="max-width:100%; height:400px;">
                                        <source src="{{ route('files.preview', $file) }}">
                                        Your browser does not support video.
                                    </video>
                                @elseif (in_array($ext, ['mp3', 'wav', 'ogg', 'm4a']))
                                    <audio controls style="width:100%;">
                                        <source src="{{ route('files.preview', $file) }}">
                                        Your browser does not support audio.
                                    </audio>
                                @elseif (in_array($ext, ['txt', 'php', 'js', 'html', 'css', 'json', 'xml', 'sql', 'md']))
                                    <iframe src="{{ route('files.preview', $file) }}" style="width:100%; height:400px;"
                                        frameborder="0"></iframe>
                                @else
                                    <iframe
                                        src="https://docs.google.com/gview?url={{ urlencode(route('files.preview', $file)) }}&embedded=true"
                                        style="width:100%; height:600px;" frameborder="0">
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
                    <h5 class="card-title mb-0">Recent Activity</h5>
                </div>
                <div class="card-body">
                    @if (isset($activityLogs) && $activityLogs->count() > 0)
                        <div class="timeline">
                            @foreach ($activityLogs as $log)
                                <div class="timeline-item">
                                    <div class="timeline-badge bg-{{ $log->action === 'download' ? 'success' : 'info' }}">
                                        <i class="bi bi-{{ $log->action === 'download' ? 'download' : 'eye' }}"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <strong>{{ $log->user->name ?? 'Unknown' }}</strong> {{ $log->description }}
                                        <br>
                                        <small class="text-muted">
                                            {{ $log->created_at->diffForHumans() }}
                                            @if ($log->ip_address)
                                                · {{ $log->ip_address }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No activity recorded yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Sharing Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Shared With</h5>
                </div>
                <div class="card-body">
                    @if ($file->shares->where('status', 'active')->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach ($file->shares->where('status', 'active') as $share)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $share->sharedWith->name ?? 'Unknown' }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="bi bi-{{ $share->permission_level }}"></i>
                                                {{ ucfirst($share->permission_level) }}
                                                @if ($share->expires_at)
                                                    · Expires {{ $share->expires_at->diffForHumans() }}
                                                @endif
                                                @if ($share->share_reason)
                                                    <br>
                                                    <span class="text-info small"><i class="bi bi-info-circle"></i> {{ $share->share_reason }}</span>
                                                @endif
                                                @if ((Auth::user()->isSuperAdmin() || $file->owner_id === Auth::id() || (Auth::user()->isDepartmentAdmin() && Auth::user()->department_id === $file->department_id)) && isset($pendingOtps[$share->shared_with]) && $pendingOtps[$share->shared_with]->isNotEmpty())
                                                    <div class="mt-2 text-warning bg-light p-2 rounded border border-warning-subtle d-inline-block">
                                                        <i class="bi bi-key-fill"></i> 
                                                        Access OTP: <strong>{{ $pendingOtps[$share->shared_with]->first()->otp_code }}</strong>
                                                        <br>
                                                        <small class="text-muted">(Expires {{ $pendingOtps[$share->shared_with]->first()->expires_at->diffForHumans() }})</small>
                                                    </div>
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
                                </div>
                            @endforeach
                        </div>
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
                    <p class="text-muted mb-0"><code>{{ $file->uuid }}</code></p>
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
                                @foreach (\App\Models\User::where('id', '!=', Auth::id())->where('status', 'active')->get() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})
                                    </option>
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
                        <div class="mb-3">
                            <label for="share_reason" class="form-label">Reason for Sharing</label>
                            <textarea name="share_reason" id="share_reason" class="form-control" rows="2" placeholder="e.g., Audit review, Project collaboration..." required></textarea>
                            <small class="text-muted">This reason will be logged for security purposes.</small>
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
                fetch('{{ route('files.archive', $file) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(response => {
                    if (response.ok) {
                        window.location.href = '{{ route('files.index') }}';
                    } else {
                        alert('Failed to archive file');
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    alert('Failed to archive file');
                });
            }
        }
    </script>
@endpush
