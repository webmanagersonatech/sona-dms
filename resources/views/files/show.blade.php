@extends('layouts.app')

@section('title', 'File Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
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
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection