<<<<<<< HEAD
{{-- resources/views/transfers/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Transfer ' . $transfer->transfer_id)

@section('content')
    <div class="row">
        <div class="col-md-8">
            <!-- Transfer Details Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Transfer Details</h5>
                    <div class="card-tools">
                        @if ($transfer->status === 'pending')
                            @if ($transfer->sender_id === Auth::id() || Auth::user()->isSuperAdmin())
                                <button class="btn btn-danger btn-sm" onclick="cancelTransfer()">
                                    <i class="bi bi-x-circle"></i> Cancel Transfer
                                </button>
                            @endif
                            @if ($transfer->receiver_id === Auth::id())
                                <button class="btn btn-success btn-sm" onclick="confirmDelivery()">
                                    <i class="bi bi-check-circle"></i> Confirm Delivery
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Status Timeline -->
                    <div class="timeline mb-4">
                        <div class="timeline-item">
                            <div class="timeline-badge bg-primary">
                                <i class="bi bi-plus-circle"></i>
                            </div>
                            <div class="timeline-content">
                                <strong>Transfer Created</strong>
                                <br>
                                <small class="text-muted">
                                    {{ $transfer->created_at->format('Y-m-d H:i:s') }}
                                    @if ($transfer->sender)
                                        by {{ $transfer->sender->name }}
                                    @endif
                                </small>
                            </div>
                        </div>

                        @if ($transfer->status === 'in_transit')
                            <div class="timeline-item">
                                <div class="timeline-badge bg-info">
                                    <i class="bi bi-truck"></i>
                                </div>
                                <div class="timeline-content">
                                    <strong>In Transit</strong>
                                    <br>
                                    <small class="text-muted">
                                        @if ($transfer->courier_name)
                                            via {{ $transfer->courier_name }}
                                            @if ($transfer->tracking_number)
                                                (Tracking: {{ $transfer->tracking_number }})
                                            @endif
                                        @endif
                                    </small>
                                </div>
                            </div>
                        @endif

                        @if ($transfer->status === 'delivered')
                            <div class="timeline-item">
                                <div class="timeline-badge bg-success">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <div class="timeline-content">
                                    <strong>Delivered</strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ $transfer->actual_delivery_time->format('Y-m-d H:i:s') }}
                                        @if ($transfer->received_by)
                                            <br>Received by: {{ $transfer->received_by }}
                                        @endif
                                        @if ($transfer->delivery_location)
                                            <br>Location: {{ $transfer->delivery_location }}
                                        @endif
                                    </small>
                                </div>
                            </div>
                        @endif

                        @if (in_array($transfer->status, ['cancelled', 'failed']))
                            <div class="timeline-item">
                                <div class="timeline-badge bg-danger">
                                    <i class="bi bi-x-circle"></i>
                                </div>
                                <div class="timeline-content">
                                    <strong>{{ ucfirst($transfer->status) }}</strong>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Transfer Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th style="width: 120px;">Transfer ID</th>
                                    <td><strong>{{ $transfer->transfer_id }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'in_transit' => 'info',
                                                'delivered' => 'success',
                                                'cancelled' => 'danger',
                                                'failed' => 'danger',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$transfer->status] ?? 'secondary' }}">
                                            {{ ucfirst(str_replace('_', ' ', $transfer->status)) }}
=======
@extends('layouts.app')

@section('title', 'Transfer Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Transfer Information</h3>
                    <div class="card-tools">
                        @php
                            $statusColors = [
                                'pending' => 'warning',
                                'in_transit' => 'info',
                                'delivered' => 'primary',
                                'received' => 'success',
                                'cancelled' => 'danger',
                            ];
                        @endphp
                        <span class="badge badge-{{ $statusColors[$transfer->status] ?? 'secondary' }}">
                            {{ ucfirst(str_replace('_', ' ', $transfer->status)) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Transfer Details</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th>Transfer ID:</th>
                                    <td>{{ $transfer->transfer_uuid }}</td>
                                </tr>
                                <tr>
                                    <th>Type:</th>
                                    <td>
                                        <span class="badge badge-{{ $transfer->transfer_type === 'physical' ? 'info' : 'success' }}">
                                            {{ ucfirst($transfer->transfer_type) }}
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
                                        </span>
                                    </td>
                                </tr>
                                <tr>
<<<<<<< HEAD
                                    <th>Purpose</th>
                                    <td>{{ $transfer->purpose }}</td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td>{{ $transfer->description ?? 'N/A' }}</td>
=======
                                    <th>File:</th>
                                    <td>
                                        <i class="fas fa-file"></i>
                                        {{ $transfer->file->original_name }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Purpose:</th>
                                    <td>{{ $transfer->purpose }}</td>
                                </tr>
                                <tr>
                                    <th>Created:</th>
                                    <td>{{ $transfer->created_at->format('M d, Y H:i') }}</td>
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
<<<<<<< HEAD
                            <table class="table table-sm">
                                <tr>
                                    <th style="width: 120px;">Expected</th>
                                    <td>
                                        {{ $transfer->expected_delivery_time->format('Y-m-d H:i') }}
                                        @if ($transfer->isOverdue())
                                            <span class="badge bg-danger">Overdue</span>
                                        @endif
                                    </td>
                                </tr>
                                @if ($transfer->actual_delivery_time)
                                    <tr>
                                        <th>Delivered</th>
                                        <td>{{ $transfer->actual_delivery_time->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @endif
                                @if ($transfer->courier_name)
                                    <tr>
                                        <th>Courier</th>
                                        <td>{{ $transfer->courier_name }}</td>
                                    </tr>
                                @endif
                                @if ($transfer->tracking_number)
                                    <tr>
                                        <th>Tracking</th>
                                        <td>{{ $transfer->tracking_number }}</td>
                                    </tr>
=======
                            <h5>Parties Involved</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th>Sender:</th>
                                    <td>{{ $transfer->sender->name }} ({{ $transfer->sender->department->name }})</td>
                                </tr>
                                <tr>
                                    <th>Receiver:</th>
                                    <td>{{ $transfer->receiver->name }} ({{ $transfer->receiver->department->name }})</td>
                                </tr>
                                @if($transfer->third_party_involved)
                                <tr>
                                    <th>Third Party:</th>
                                    <td>
                                        {{ $transfer->third_party_name }}
                                        <br><small>{{ $transfer->third_party_email }}</small>
                                    </td>
                                </tr>
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
                                @endif
                            </table>
                        </div>
                    </div>

<<<<<<< HEAD
                    @if ($transfer->signature)
                        <hr>
                        <h6>Digital Signature</h6>
                        <img src="{{ $transfer->signature }}" alt="Signature" class="img-fluid" style="max-height: 100px;">
                    @endif

                    @if ($transfer->notes)
                        <hr>
                        <h6>Delivery Notes</h6>
                        <p class="text-muted">{{ $transfer->notes }}</p>
                    @endif
                </div>
            </div>

            <!-- Associated File -->
            @if ($transfer->file)
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Associated File</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi {{ $transfer->file->icon }} me-3" style="font-size: 2rem;"></i>
                            <div>
                                <h6 class="mb-1">{{ $transfer->file->name }}</h6>
                                <small class="text-muted">
                                    {{ $transfer->file->size_for_humans }} ·
                                    Uploaded {{ $transfer->file->created_at->diffForHumans() }}
                                </small>
                            </div>
                            <div class="ms-auto">
                                <a href="{{ route('files.show', $transfer->file) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <a href="{{ route('files.download', $transfer->file) }}" class="btn btn-sm btn-success">
                                    <i class="bi bi-download"></i> Download
                                </a>
=======
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5>Delivery Information</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th>Expected Delivery:</th>
                                    <td>{{ $transfer->expected_delivery_time->format('M d, Y H:i') }}</td>
                                </tr>
                                @if($transfer->actual_delivery_time)
                                <tr>
                                    <th>Actual Delivery:</th>
                                    <td>{{ $transfer->actual_delivery_time->format('M d, Y H:i') }}</td>
                                </tr>
                                @endif
                                @if($transfer->delivery_location)
                                <tr>
                                    <th>Delivery Location:</th>
                                    <td>{{ $transfer->delivery_location }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    @if($transfer->notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5>Notes</h5>
                            <div class="alert alert-info">
                                {{ $transfer->notes }}
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row mt-3">
                        <div class="col-12">
                            <h5>Transfer Actions</h5>
                            <div class="btn-group" role="group">
                                @if($transfer->sender_id === auth()->id())
                                    @if($transfer->status === 'pending')
                                    <form action="{{ route('transfers.send', $transfer) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane"></i> Send Transfer
                                        </button>
                                    </form>
                                    @endif
                                    
                                    @if($transfer->status === 'in_transit')
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#deliverModal">
                                        <i class="fas fa-check-circle"></i> Mark as Delivered
                                    </button>
                                    @endif

                                    @if($transfer->transfer_type === 'cloud' && in_array($transfer->status, ['pending', 'in_transit']))
                                    <form action="{{ route('transfers.cloud.share', $transfer) }}" method="POST" class="ml-2">
                                        @csrf
                                        <button type="submit" class="btn btn-info">
                                            <i class="fas fa-cloud-share-alt"></i> Create Cloud Share
                                        </button>
                                    </form>
                                    @endif
                                @endif

                                @if($transfer->receiver_id === auth()->id())
                                    @if($transfer->status === 'delivered')
                                    <form action="{{ route('transfers.receive', $transfer) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check"></i> Confirm Receipt
                                        </button>
                                    </form>
                                    @endif

                                    @if($transfer->third_party_involved && $transfer->status === 'received')
                                    <form action="{{ route('transfers.third-party.request', $transfer) }}" method="POST" class="ml-2">
                                        @csrf
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fas fa-user-friends"></i> Request Third-Party Access
                                        </button>
                                    </form>
                                    @endif
                                @endif

                                @if(in_array($transfer->status, ['pending', 'in_transit']) && 
                                    ($transfer->sender_id === auth()->id() || $transfer->receiver_id === auth()->id()))
                                <form action="{{ route('transfers.cancel', $transfer) }}" method="POST" class="ml-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-times"></i> Cancel Transfer
                                    </button>
                                </form>
                                @endif
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
                            </div>
                        </div>
                    </div>
                </div>
<<<<<<< HEAD
            @endif

            <!-- Activity Log -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Activity Log</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach ($transfer->activityLogs as $log)
                            <div class="timeline-item">
                                <div class="timeline-badge bg-info">
                                    <i class="bi bi-activity"></i>
                                </div>
                                <div class="timeline-content">
                                    <strong>{{ $log->user->name ?? 'System' }}</strong>
                                    <br>
                                    {{ $log->description }}
                                    <br>
                                    <small class="text-muted">
                                        {{ $log->created_at->diffForHumans() }}
                                        @if ($log->ip_address)
                                            · IP: {{ $log->ip_address }}
                                        @endif
                                    </small>
                                </div>
                            </div>
=======
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Transfer Activity</h3>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($transfer->activityLogs()->latest('id')->limit(10)->get() as $activity)
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

        <div class="col-md-4">
<<<<<<< HEAD
            <!-- Sender Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Sender</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if ($transfer->sender->avatar)
                            <img src="{{ Storage::url($transfer->sender->avatar) }}" alt="{{ $transfer->sender->name }}"
                                class="rounded-circle img-thumbnail" width="80">
                        @else
                            <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center"
                                style="width: 80px; height: 80px;">
                                <span class="text-white" style="font-size: 2rem;">
                                    {{ strtoupper(substr($transfer->sender->name, 0, 2)) }}
                                </span>
                            </div>
                        @endif
                    </div>
                    <h6 class="text-center">{{ $transfer->sender->name }}</h6>
                    <p class="text-center text-muted mb-3">{{ $transfer->sender->email }}</p>
                    <hr>
                    <p class="mb-1"><i class="bi bi-building me-2"></i>
                        {{ $transfer->sender->department->name ?? 'N/A' }}</p>
                    <p class="mb-0"><i class="bi bi-telephone me-2"></i> {{ $transfer->sender->phone ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- Receiver Information -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Receiver</h5>
                </div>
                <div class="card-body">
                    @if ($transfer->receiver)
                        <div class="text-center mb-3">
                            @if ($transfer->receiver->avatar)
                                <img src="{{ Storage::url($transfer->receiver->avatar) }}"
                                    alt="{{ $transfer->receiver->name }}" class="rounded-circle img-thumbnail"
                                    width="80">
                            @else
                                <div class="rounded-circle bg-success d-inline-flex align-items-center justify-content-center"
                                    style="width: 80px; height: 80px;">
                                    <span class="text-white" style="font-size: 2rem;">
                                        {{ strtoupper(substr($transfer->receiver->name, 0, 2)) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        <h6 class="text-center">{{ $transfer->receiver->name }}</h6>
                        <p class="text-center text-muted mb-3">{{ $transfer->receiver->email }}</p>
                        <hr>
                        <p class="mb-1"><i class="bi bi-building me-2"></i>
                            {{ $transfer->receiver->department->name ?? 'N/A' }}</p>
                        <p class="mb-0"><i class="bi bi-telephone me-2"></i>
                            {{ $transfer->receiver->phone ?? ($transfer->receiver_phone ?? 'N/A') }}</p>
                    @else
                        <div class="text-center">
                            <div class="rounded-circle bg-warning d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 80px; height: 80px;">
                                <span class="text-white" style="font-size: 2rem;">
                                    <i class="bi bi-person"></i>
                                </span>
                            </div>
                            <h6>{{ $transfer->receiver_name }}</h6>
                            <p class="text-muted">{{ $transfer->receiver_email }}</p>
                            @if ($transfer->receiver_phone)
                                <p class="mb-0"><i class="bi bi-telephone me-2"></i> {{ $transfer->receiver_phone }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- QR Code -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tracking QR Code</h5>
                </div>
                <div class="card-body text-center">
                    <div id="qrcode"></div>
                    <p class="mt-2">Scan to track transfer</p>
                    <a href="{{ route('transfers.track', $transfer->transfer_id) }}" class="btn btn-sm btn-primary"
                        target="_blank">
                        <i class="bi bi-box-arrow-up-right"></i> Track Online
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('transfers.cancel', $transfer) }}">
                    @csrf
                    @method('POST')
                    <div class="modal-header">
                        <h5 class="modal-title">Cancel Transfer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to cancel this transfer?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                        <button type="submit" class="btn btn-danger">Yes, Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirm Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('transfers.confirm', $transfer) }}">
                    @csrf
                    @method('POST')
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Delivery</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="delivery_location" class="form-label">Delivery Location</label>
                            <input type="text" class="form-control" id="delivery_location" name="delivery_location"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="received_by" class="form-label">Received By</label>
                            <input type="text" class="form-control" id="received_by" name="received_by"
                                value="{{ Auth::user()->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Confirm Delivery</button>
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
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script>
        // Generate QR Code
        new QRCode(document.getElementById("qrcode"), {
            text: "{{ route('transfers.track', $transfer->transfer_id) }}",
            width: 200,
            height: 200
        });

        function cancelTransfer() {
            $('#cancelModal').modal('show');
        }

        function confirmDelivery() {
            $('#confirmModal').modal('show');
        }
    </script>
@endpush
=======
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">File Information</h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>File Name:</th>
                            <td>{{ $transfer->file->original_name }}</td>
                        </tr>
                        <tr>
                            <th>Type:</th>
                            <td>{{ strtoupper($transfer->file->extension) }}</td>
                        </tr>
                        <tr>
                            <th>Size:</th>
                            <td>{{ $transfer->file->formatted_size }}</td>
                        </tr>
                        <tr>
                            <th>Owner:</th>
                            <td>{{ $transfer->file->owner->name }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($transfer->file->is_archived)
                                <span class="badge badge-secondary">Archived</span>
                                @elseif($transfer->file->isExpired())
                                <span class="badge badge-warning">Expired</span>
                                @else
                                <span class="badge badge-success">Active</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                    
                    <div class="mt-3">
                        <a href="{{ route('files.show', $transfer->file) }}" class="btn btn-info btn-block">
                            <i class="fas fa-eye"></i> View File Details
                        </a>
                        @if($transfer->file->canBeAccessedBy(auth()->user()))
                        <a href="{{ route('files.download', $transfer->file) }}" class="btn btn-success btn-block mt-2">
                            <i class="fas fa-download"></i> Download File
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            @if($transfer->delivery_location)
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Delivery Location</h3>
                </div>
                <div class="card-body">
                    <p><strong>Location:</strong> {{ $transfer->delivery_location }}</p>
                    @if($transfer->latitude && $transfer->longitude)
                    <div id="map" style="height: 200px; background: #f0f0f0;" class="rounded">
                        <!-- In production, integrate with Google Maps -->
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-map-marker-alt fa-2x"></i>
                            <p class="mt-2">Map integration available<br>in production</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Deliver Modal -->
@if($transfer->sender_id === auth()->id() && $transfer->status === 'in_transit')
<div class="modal fade" id="deliverModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('transfers.deliver', $transfer) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Mark as Delivered</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="delivery_location">Delivery Location *</label>
                        <input type="text" class="form-control" id="delivery_location" name="delivery_location" required placeholder="e.g., Reception Desk, Building A">
                    </div>
                    <div class="form-group">
                        <label for="latitude">Latitude (Optional)</label>
                        <input type="number" step="any" class="form-control" id="latitude" name="latitude" placeholder="e.g., 12.345678">
                    </div>
                    <div class="form-group">
                        <label for="longitude">Longitude (Optional)</label>
                        <input type="number" step="any" class="form-control" id="longitude" name="longitude" placeholder="e.g., 98.765432">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Mark as Delivered</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
