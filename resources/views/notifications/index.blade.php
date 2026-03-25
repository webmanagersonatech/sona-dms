{{-- resources/views/notifications/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
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
                </div>
            </div>
        </div>

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
                        </div>
                    @endif
                </div>
            </div>
        </div>
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
    </script>
@endpush
