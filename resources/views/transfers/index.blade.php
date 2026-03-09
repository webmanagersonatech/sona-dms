@extends('layouts.app')

@section('title', 'Transfers')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">File Transfers</h3>
                    @can('transfers.create')
                    <div class="card-tools">
                        <a href="{{ route('transfers.create') }}" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> New Transfer
                        </a>
                    </div>
                    @endcan
                </div>
                <div class="card-body">
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
                                    <th>Expected Delivery</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transfers as $transfer)
                                <tr>
                                    <td>
                                        <small class="text-muted">{{ substr($transfer->transfer_uuid, 0, 8) }}...</small>
                                    </td>
                                    <td>
                                        <i class="fas fa-file text-primary"></i>
                                        {{ $transfer->file->original_name }}
                                    </td>
                                    <td>{{ $transfer->sender->name }}</td>
                                    <td>{{ $transfer->receiver->name }}</td>
                                    <td>
                                        <span class="badge badge-{{ $transfer->transfer_type === 'physical' ? 'info' : 'success' }}">
                                            {{ ucfirst($transfer->transfer_type) }}
                                        </span>
                                        @if($transfer->third_party_involved)
                                        <span class="badge badge-warning">3rd Party</span>
                                        @endif
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
                                            {{ ucfirst(str_replace('_', ' ', $transfer->status)) }}
                                        </span>
                                    </td>
                                    <td>{{ $transfer->expected_delivery_time->format('M d, Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('transfers.show', $transfer) }}" class="btn btn-sm btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($transfer->sender_id === auth()->id() && $transfer->status === 'pending')
                                            <form action="{{ route('transfers.send', $transfer) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-primary" title="Send">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </form>
                                            @endif
                                            @if($transfer->receiver_id === auth()->id() && $transfer->status === 'delivered')
                                            <form action="{{ route('transfers.receive', $transfer) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-success" title="Receive">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            @endif
                                            @if(in_array($transfer->status, ['pending', 'in_transit']) && 
                                                ($transfer->sender_id === auth()->id() || $transfer->receiver_id === auth()->id()))
                                            <form action="{{ route('transfers.cancel', $transfer) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Cancel">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $transfers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection