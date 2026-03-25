{{-- resources/views/search/partials/transfers.blade.php --}}
<div class="list-group">
    @foreach ($transfers as $transfer)
        <a href="{{ route('transfers.show', $transfer) }}" class="list-group-item list-group-item-action">
            <div class="row">
                <div class="col-auto">
                    <i class="bi bi-truck fs-2 text-success"></i>
                </div>
                <div class="col">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">{{ $transfer->transfer_id }}</h6>
                            <p class="mb-1">{{ $transfer->purpose }}</p>
                        </div>
                        <span
                            class="badge bg-{{ $transfer->status === 'delivered' ? 'success' : ($transfer->status === 'pending' ? 'warning' : 'danger') }}">
                            {{ ucfirst($transfer->status) }}
                        </span>
                    </div>
                    <div class="d-flex flex-wrap gap-3 mt-2">
                        <small class="text-muted">
                            <i class="bi bi-person-up"></i> From: {{ $transfer->sender->name }}
                        </small>
                        <small class="text-muted">
                            <i class="bi bi-person-down"></i> To:
                            {{ $transfer->receiver->name ?? $transfer->receiver_name }}
                        </small>
                        <small class="text-muted">
                            <i class="bi bi-calendar-check"></i> Expected:
                            {{ $transfer->expected_delivery_time->format('Y-m-d H:i') }}
                        </small>
                        @if ($transfer->actual_delivery_time)
                            <small class="text-muted">
                                <i class="bi bi-calendar-check-fill"></i> Delivered:
                                {{ $transfer->actual_delivery_time->format('Y-m-d H:i') }}
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </a>
    @endforeach
</div>
