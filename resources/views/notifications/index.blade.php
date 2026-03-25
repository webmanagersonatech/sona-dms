<<<<<<< HEAD
{{-- resources/views/notifications/index.blade.php --}}
=======
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<<<<<<< HEAD
    <div class="row">
        <div class="col-md-3">
            <!-- Sidebar -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Filters</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select class="form-select" id="typeFilter">
                            <option value="">All Types</option>
                            <option value="info">Info</option>
                            <option value="success">Success</option>
                            <option value="warning">Warning</option>
                            <option value="danger">Alert</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="readFilter">
                            <option value="">All</option>
                            <option value="false">Unread</option>
                            <option value="true">Read</option>
                        </select>
                    </div>

                    <button class="btn btn-primary w-100" onclick="applyFilters()">
                        <i class="bi bi-filter"></i> Apply Filters
                    </button>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Total:</strong> {{ $stats['total'] }}
                    </div>
                    <div class="mb-2">
                        <strong>Unread:</strong> {{ $stats['unread'] }}
                    </div>
                    <hr>
                    <h6>By Type</h6>
                    @foreach ($stats['by_type'] as $type)
                        <div class="d-flex justify-content-between mb-1">
                            <span>
                                <i
                                    class="bi bi-{{ $type->type === 'info' ? 'info-circle' : ($type->type === 'success' ? 'check-circle' : ($type->type === 'warning' ? 'exclamation-triangle' : 'exclamation-circle')) }} text-{{ $type->type }}"></i>
                                {{ ucfirst($type->type) }}
                            </span>
                            <span class="badge bg-secondary">{{ $type->count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Actions -->
            <div class="card mt-3">
                <div class="card-body">
                    <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success w-100" {{ $stats['unread'] === 0 ? 'disabled' : '' }}>
                            <i class="bi bi-check-all"></i> Mark All Read
                        </button>
                    </form>
                    <form action="{{ route('notifications.clear-all') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100"
                            onclick="return confirm('Clear all notifications?')">
                            <i class="bi bi-trash"></i> Clear All
                        </button>
                    </form>
=======
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">

                <div class="d-flex justify-content-end align-items-center">

                    <div class="mb-0">
                        <button type="button" class="btn btn-primary" onclick="markAllAsRead()">
                            <i class="fas fa-check-double"></i> Mark All as Read
                        </button>
                    </div>

>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
                </div>
            </div>
        </div>

<<<<<<< HEAD
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Notifications</h3>
                </div>
                <div class="card-body">
                    @if ($notifications->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-bell-slash display-1 text-muted"></i>
                            <h5 class="mt-3">No Notifications</h5>
                            <p class="text-muted">You don't have any notifications at the moment.</p>
                        </div>
                    @else
                        <div class="list-group">
                            @foreach ($notifications as $notification)
                                <div
                                    class="list-group-item list-group-item-action {{ !$notification->is_read ? 'list-group-item-primary' : '' }}">
                                    <div class="d-flex w-100 justify-content-between align-items-start">
                                        <div class="d-flex">
                                            <div class="me-3">
                                                <i
                                                    class="bi bi-{{ $notification->icon }} fs-4 text-{{ $notification->type }}"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">{{ $notification->message }}</h6>
                                                <small class="text-muted">
                                                    <i class="bi bi-clock"></i>
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </small>
                                                @if (!$notification->is_read)
                                                    <span class="badge bg-primary ms-2">New</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="btn-group">
                                            @if ($notification->link)
                                                <a href="{{ route('notifications.show', $notification) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                            @endif
                                            @if (!$notification->is_read)
                                                <button class="btn btn-sm btn-outline-success mark-read"
                                                    data-id="{{ $notification->id }}">
                                                    <i class="bi bi-check"></i>
                                                </button>
                                            @endif
                                            <button class="btn btn-sm btn-outline-danger delete-notification"
                                                data-id="{{ $notification->id }}">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-3">
                            {{ $notifications->links() }}
=======
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
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
                        </div>
                    @endif
                </div>
            </div>
        </div>
<<<<<<< HEAD
    </div>
@endsection

@push('scripts')
    <script>
        function applyFilters() {
            const type = document.getElementById('typeFilter').value;
            const read = document.getElementById('readFilter').value;

            let url = '{{ route('notifications.index') }}';
            let params = [];

            if (type) params.push('type=' + type);
            if (read !== '') params.push('read=' + read);

            if (params.length > 0) {
                url += '?' + params.join('&');
            }

            window.location.href = url;
        }

        // Mark as read
        document.querySelectorAll('.mark-read').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                fetch(`/notifications/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(() => {
                    location.reload();
                });
            });
        });

        // Delete notification
        document.querySelectorAll('.delete-notification').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Delete this notification?')) {
                    const id = this.dataset.id;
                    fetch(`/notifications/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        });
=======

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
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
    </script>
@endpush
