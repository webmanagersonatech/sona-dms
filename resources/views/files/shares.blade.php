@extends('layouts.app')

@section('title', 'File Shares - ' . $file->original_name)

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                <i class="fas fa-share-alt"></i>
                                Shares for: {{ $file->original_name }}
                            </h4>
                            <p class="text-muted mb-0">
                                <i class="fas fa-user"></i> Owned by: {{ $file->owner->name }} 
                                | <i class="fas fa-building"></i> {{ $file->department->name }}
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('files.show', $file) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to File
                            </a>
                            <a href="{{ route('files.shares.create', $file) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> New Share
                            </a>
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
                    <h3 class="card-title">Active Shares</h3>
                </div>
                <div class="card-body">
                    @if($shares->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Shared With</th>
                                    <th>Permissions</th>
                                    <th>Created</th>
                                    <th>Valid Until</th>
                                    <th>Access Count</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shares as $share)
                                <tr>
                                    <td>
                                        @if($share->sharedWith)
                                        <i class="fas fa-user"></i> {{ $share->sharedWith->name }}
                                        <br><small>{{ $share->sharedWith->email }}</small>
                                        @elseif($share->shared_email)
                                        <i class="fas fa-envelope"></i> {{ $share->shared_email }}
                                        <br><small class="text-muted">External User</small>
                                        @endif
                                        <br>
                                        <small class="text-muted">
                                            Shared by: {{ $share->sharedBy->name }}
                                        </small>
                                    </td>
                                    <td>
                                        @foreach($share->permissions as $permission)
                                        <span class="badge badge-info mr-1 mb-1">
                                            {{ ucfirst($permission) }}
                                        </span>
                                        @endforeach
                                        @if($share->requires_otp_approval)
                                        <br>
                                        <span class="badge badge-warning mt-1">
                                            <i class="fas fa-shield-alt"></i> OTP Required
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $share->created_at->format('M d, Y') }}
                                        <br>
                                        <small>{{ $share->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        @if($share->valid_until->isPast())
                                        <span class="text-danger">
                                            {{ $share->valid_until->format('M d, Y H:i') }}
                                        </span>
                                        <br>
                                        <span class="badge badge-danger">Expired</span>
                                        @else
                                        {{ $share->valid_until->format('M d, Y H:i') }}
                                        <br>
                                        <small>
                                            Expires in: {{ $share->valid_until->diffForHumans() }}
                                        </small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $share->access_count }}
                                        @if($share->max_access_count)
                                        / {{ $share->max_access_count }}
                                        @endif
                                        @if($share->last_accessed_at)
                                        <br>
                                        <small class="text-muted">
                                            Last: {{ $share->last_accessed_at->format('M d') }}
                                        </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$share->is_active)
                                        <span class="badge badge-secondary">Revoked</span>
                                        @elseif($share->isExpired())
                                        <span class="badge badge-danger">Expired</span>
                                        @elseif($share->valid_from && $share->valid_from->isFuture())
                                        <span class="badge badge-warning">Scheduled</span>
                                        @else
                                        <span class="badge badge-success">Active</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            @if($share->is_active && !$share->isExpired())
                                            <form action="{{ route('files.shares.revoke', $share) }}" method="POST"
                                                  data-confirm="Are you sure you want to revoke this share?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Revoke">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                            @endif
                                            
                                            <!-- Copy Share Link -->
                                            <button type="button" class="btn btn-sm btn-info copy-share-link" 
                                                    data-share-url="{{ route('shared.show', $share->share_token) }}"
                                                    title="Copy Share Link">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-share-alt fa-4x text-muted mb-3"></i>
                        <h4>No Shares Found</h4>
                        <p class="text-muted">This file hasn't been shared with anyone yet.</p>
                        <a href="{{ route('files.shares.create', $file) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create First Share
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Share Statistics -->
    <div class="row mt-3">
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-info">
                    <i class="fas fa-share-alt"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Shares</span>
                    <span class="info-box-number">{{ $shares->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-success">
                    <i class="fas fa-check-circle"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Active Shares</span>
                    <span class="info-box-number">
                        {{ $shares->where('is_active', true)->where('valid_until', '>', now())->count() }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-primary">
                    <i class="fas fa-mouse-pointer"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Accesses</span>
                    <span class="info-box-number">{{ $shares->sum('access_count') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Copy share link to clipboard
    document.querySelectorAll('.copy-share-link').forEach(button => {
        button.addEventListener('click', function() {
            const shareUrl = this.dataset.shareUrl;
            navigator.clipboard.writeText(shareUrl).then(() => {
                const originalHTML = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check"></i>';
                this.classList.remove('btn-info');
                this.classList.add('btn-success');
                
                setTimeout(() => {
                    this.innerHTML = originalHTML;
                    this.classList.remove('btn-success');
                    this.classList.add('btn-info');
                }, 2000);
            });
        });
    });
</script>
@endpush