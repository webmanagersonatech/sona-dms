@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">

                <div class="d-flex justify-content-end align-items-center">

                    <div class="mb-0">
                        <button type="button" class="btn btn-primary" onclick="markAllAsRead()">
                            <i class="fas fa-check-double"></i> Mark All as Read
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">All Notifications</h3>

                    </div>
                    <div class="card-body p-0">
                        @if ($notifications->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($notifications as $notification)
                                    <a href="{{ route('notifications.show', $notification) }}"
                                        class="list-group-item list-group-item-action {{ !$notification->is_read ? 'unread-notification' : '' }}">
                                        <div class="d-flex w-100 justify-content-between">
                                            <div class="d-flex align-items-start">
                                                <div class="mr-3">
                                                    @switch($notification->type)
                                                        @case('file_shared')
                                                            <i class="fas fa-share-alt text-primary fa-2x"></i>
                                                        @break

                                                        @case('transfer_created')
                                                            <i class="fas fa-paper-plane text-info fa-2x"></i>
                                                        @break

                                                        @case('otp_sent')
                                                            <i class="fas fa-shield-alt text-success fa-2x"></i>
                                                        @break

                                                        @case('security_alert')
                                                            <i class="fas fa-exclamation-triangle text-danger fa-2x"></i>
                                                        @break

                                                        @default
                                                            <i class="fas fa-bell text-secondary fa-2x"></i>
                                                    @endswitch
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="mb-1">{{ $notification->title }}</h5>
                                                    <p class="mb-1">{{ $notification->message }}</p>
                                                    <small class="text-muted">
                                                        @if ($notification->data)
                                                            @php $data = json_decode($notification->data, true); @endphp
                                                            @if (isset($data['file_name']))
                                                                File: {{ $data['file_name'] }}
                                                            @endif
                                                        @endif
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <small class="text-muted">
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </small>
                                                <br>
                                                @if (!$notification->is_read)
                                                    <span class="badge badge-danger">New</span>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                                <h4>No Notifications</h4>
                                <p class="text-muted">You don't have any notifications yet.</p>
                            </div>
                        @endif
                    </div>
                    @if ($notifications->count() > 0)
                        <div class="card-footer">
                            <div class="d-flex justify-content-between">
                                <div>
                                    Showing {{ $notifications->firstItem() }} to {{ $notifications->lastItem() }}
                                    of {{ $notifications->total() }} notifications
                                </div>
                                <div>
                                    {{ $notifications->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Notification Statistics -->
        <div class="row mt-3">
            <div class="col-md-3 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-primary">
                        <i class="fas fa-bell"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Notifications</span>
                        <span class="info-box-number">{{ $notifications->total() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-success">
                        <i class="fas fa-check-circle"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Read</span>
                        <span class="info-box-number">{{ $notifications->where('is_read', true)->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-warning">
                        <i class="fas fa-exclamation-circle"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Unread</span>
                        <span class="info-box-number">{{ $notifications->where('is_read', false)->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-info">
                        <i class="fas fa-calendar-day"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Today</span>
                        {{ $notifications->filter(fn($n) => $n->created_at->isToday())->count() }}

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .unread-notification {
            background-color: rgba(0, 123, 255, 0.05);
            border-left: 4px solid #007bff !important;
        }

        .list-group-item:hover {
            background-color: #f8f9fa;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function markAllAsRead() {
            fetch('{{ route('notifications.mark-all-read') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
        }

        // Auto-refresh notifications every 60 seconds
        setInterval(() => {
            fetch('{{ route('notifications.get-unread-count') }}')
                .then(response => response.json())
                .then(data => {
                    if (data.count > 0) {
                        document.title = `(${data.count}) Notifications - Secure DMS`;
                    } else {
                        document.title = 'Notifications - Secure DMS';
                    }
                });
        }, 60000);
    </script>
@endpush
