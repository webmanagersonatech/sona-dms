@extends('layouts.app')

@section('title', 'Transfer Monitoring')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                <i class="fas fa-tachometer-alt"></i>
                                Transfer Monitoring
                            </h4>
                            <p class="text-muted mb-0">
                                Monitor and manage all file transfers
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transfer Statistics -->
    <div class="row">
        <div class="col-md-2 col-sm-4">
            <div class="info-box">
                <span class="info-box-icon bg-warning">
                    <i class="fas fa-clock"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Pending</span>
                    <span class="info-box-number">{{ $stats['pending'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4">
            <div class="info-box">
                <span class="info-box-icon bg-info">
                    <i class="fas fa-paper-plane"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">In Transit</span>
                    <span class="info-box-number">{{ $stats['in_transit'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4">
            <div class="info-box">
                <span class="info-box-icon bg-primary">
                    <i class="fas fa-check-circle"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Delivered</span>
                    <span class="info-box-number">{{ $stats['delivered'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4">
            <div class="info-box">
                <span class="info-box-icon bg-success">
                    <i class="fas fa-check-double"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Received</span>
                    <span class="info-box-number">{{ $stats['received'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4">
            <div class="info-box">
                <span class="info-box-icon bg-danger">
                    <i class="fas fa-times-circle"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Cancelled</span>
                    <span class="info-box-number">{{ $stats['cancelled'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4">
            <div class="info-box">
                <span class="info-box-icon bg-secondary">
                    <i class="fas fa-exchange-alt"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total</span>
                    <span class="info-box-number">{{ $transfers->total() }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Transfers</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" action="{{ route('admin.transfers') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                        <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Received</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Type</label>
                                    <select name="transfer_type" class="form-control">
                                        <option value="">All Types</option>
                                        <option value="physical" {{ request('transfer_type') == 'physical' ? 'selected' : '' }}>Physical</option>
                                        <option value="cloud" {{ request('transfer_type') == 'cloud' ? 'selected' : '' }}>Cloud</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date From</label>
                                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date To</label>
                                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="{{ route('admin.transfers') }}" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Transfers Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Transfer ID</th>
                                    <th>File</th>
                                    <th>Sender</th>
                                    <th>Receiver</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Expected Delivery</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transfers as $transfer)
                                <tr>
                                    <td>
                                        <small class="text-muted">{{ substr($transfer->transfer_uuid, 0, 8) }}...</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="mr-2">
                                                <i class="fas fa-file text-primary"></i>
                                            </div>
                                            <div>
                                                {{ Str::limit($transfer->file->original_name, 20) }}
                                                @if($transfer->third_party_involved)
                                                <br>
                                                <small class="text-warning">
                                                    <i class="fas fa-user-friends"></i> 3rd Party
                                                </small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        {{ $transfer->sender->name }}
                                        <br>
                                        <small class="text-muted">{{ $transfer->sender->department->name }}</small>
                                    </td>
                                    <td>
                                        {{ $transfer->receiver->name }}
                                        <br>
                                        <small class="text-muted">{{ $transfer->receiver->department->name }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $transfer->transfer_type === 'physical' ? 'info' : 'success' }}">
                                            {{ ucfirst($transfer->transfer_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = match($transfer->status) {
                                                'pending' => 'warning',
                                                'in_transit' => 'info',
                                                'delivered' => 'primary',
                                                'received' => 'success',
                                                'cancelled' => 'danger',
                                                default => 'secondary',
                                            };
                                        @endphp
                                        <span class="badge badge-{{ $badgeClass }}">
                                            {{ ucfirst($transfer->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $transfer->created_at->format('M d, Y') }}
                                        <br>
                                        <small>{{ $transfer->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        @if($transfer->expected_delivery_time)
                                        {{ $transfer->expected_delivery_time->format('M d, Y') }}
                                        <br>
                                        <small class="{{ $transfer->expected_delivery_time->isPast() && !in_array($transfer->status, ['received', 'cancelled']) ? 'text-danger' : 'text-muted' }}">
                                            {{ $transfer->expected_delivery_time->format('H:i') }}
                                        </small>
                                        @else
                                        <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('transfers.show', $transfer) }}" class="btn btn-sm btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($transfer->status === 'pending')
                                            <form action="{{ route('transfers.send', $transfer) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-primary" title="Send">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </form>
                                            @endif
                                            @if(in_array($transfer->status, ['pending', 'in_transit']))
                                            <form action="{{ route('transfers.cancel', $transfer) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Cancel" 
                                                        onclick="return confirm('Are you sure you want to cancel this transfer?')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i class="fas fa-paper-plane fa-3x text-muted mb-3"></i>
                                        <h5>No transfers found</h5>
                                        <p class="text-muted">Try changing your filter criteria</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $transfers->links() }}
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="small text-muted">
                                <i class="fas fa-info-circle"></i>
                                Showing {{ $transfers->firstItem() }} to {{ $transfers->lastItem() }} of {{ $transfers->total() }} transfers
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-sm btn-outline-primary" onclick="exportTransfers()">
                                <i class="fas fa-download"></i> Export Transfer Report
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
function exportTransfers() {
    // Collect filter parameters
    const params = new URLSearchParams({
        status: document.querySelector('select[name="status"]').value,
        transfer_type: document.querySelector('select[name="transfer_type"]').value,
        date_from: document.querySelector('input[name="date_from"]').value,
        date_to: document.querySelector('input[name="date_to"]').value,
        export: 'csv'
    });

    // Create download URL
    const url = '{{ route("admin.transfers") }}?' + params.toString();
    window.open(url, '_blank');
}
</script>
@endpush