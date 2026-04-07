{{-- resources/views/dashboard/user.blade.php --}}
@extends('layouts.app')

@section('title', 'User Dashboard')

@section('content')
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-2 col-md-4 col-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($stats['my_files'] ?? 0) }}</h3>
                    <p>My Files</p>
                </div>
                <div class="stat-icon primary">
                    <i class="bi bi-files"></i>
                </div>
                <a href="{{ route('files.index') }}" class="stretched-link"></a>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($stats['shared_with_me'] ?? 0) }}</h3>
                    <p>Shared With Me</p>
                </div>
                <div class="stat-icon success">
                    <i class="bi bi-share"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($stats['my_transfers'] ?? 0) }}</h3>
                    <p>Physical Tracking</p>
                </div>
                <div class="stat-icon warning">
                    <i class="bi bi-truck"></i>
                </div>
                <a href="{{ route('transfers.index') }}" class="stretched-link"></a>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($stats['pending_transfers'] ?? 0) }}</h3>
                    <p>Pending</p>
                </div>
                <div class="stat-icon danger">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <a href="{{ route('transfers.index', ['status' => 'pending']) }}" class="stretched-link"></a>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($stats['recent_uploads'] ?? 0) }}</h3>
                    <p>This Month</p>
                </div>
                <div class="stat-icon info">
                    <i class="bi bi-calendar"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_downloads'] ?? 0) }}</h3>
                    <p>Downloads</p>
                </div>
                <div class="stat-icon secondary">
                    <i class="bi bi-download"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Storage Usage Card -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">My Storage Usage</h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="progress" style="height: 25px;">
                                @php
                                    $storageUsed = $stats['storage_used'] ?? 0;
                                    $storageGB = $storageUsed > 0 ? $storageUsed / 1073741824 : 0; // Convert to GB
                                    $storageLimit = 10; // 10GB limit
                                    $storagePercentage =
                                        $storageGB > 0 ? min(($storageGB / $storageLimit) * 100, 100) : 0;
                                @endphp
                                <div class="progress-bar bg-primary progress-bar-striped" role="progressbar"
                                    style="width: {{ $storagePercentage }}%;" aria-valuenow="{{ $storagePercentage }}"
                                    aria-valuemin="0" aria-valuemax="100">
                                    {{ number_format($storageGB, 2) }} GB / {{ $storageLimit }} GB
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-end">
                                <h4 class="mb-0">{{ number_format($storageGB, 2) }} GB</h4>
                                <small class="text-muted">of {{ $storageLimit }} GB used
                                    ({{ round($storagePercentage) }}%)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- First Row: Recent Files and Shared Files -->
    <div class="row g-4 mb-4">
        <!-- My Recent Files -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Recent Files</h5>
                    <a href="{{ route('files.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye me-1"></i> View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>File</th>
                                    <th>Size</th>
                                    <th>Type</th>
                                    <th>Uploaded</th>
                                    <th>Downloads</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentFiles ?? [] as $file)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi {{ $file->icon ?? 'bi-file' }} me-2 fs-4"
                                                    style="color: var(--primary);"></i>
                                                <div>
                                                    <a href="{{ route('files.show', $file) }}"
                                                        class="text-decoration-none fw-medium">
                                                        {{ Str::limit($file->name ?? 'Unnamed', 25) }}
                                                    </a>
                                                    @if ($file->is_encrypted ?? false)
                                                        <span class="badge bg-warning ms-1" title="Encrypted">
                                                            <i class="bi bi-lock"></i>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-light text-dark">{{ $file->size_for_humans ?? '0 B' }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-secondary">{{ strtoupper($file->extension ?? 'N/A') }}</span>
                                        </td>
                                        <td>
                                            @if (isset($file->created_at) && $file->created_at && method_exists($file->created_at, 'diffForHumans'))
                                                <span title="{{ $file->created_at->format('Y-m-d H:i:s') }}">
                                                    {{ $file->created_at->diffForHumans() }}
                                                </span>
                                            @else
                                                <span>N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-info text-white">{{ number_format($file->download_count ?? 0) }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('files.show', $file) }}" class="btn btn-outline-primary"
                                                    title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('files.download', $file) }}"
                                                    class="btn btn-outline-success" title="Download">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                <button class="btn btn-outline-info" title="Share"
                                                    onclick="showShareModal({{ $file->id ?? 0 }})">
                                                    <i class="bi bi-share"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="bi bi-files text-muted" style="font-size: 2rem;"></i>
                                            <p class="mb-0 mt-2">No files found</p>
                                            <a href="{{ route('files.create') }}" class="btn btn-primary btn-sm mt-2">
                                                <i class="bi bi-upload"></i> Upload Your First File
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Files Shared With Me -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Files Shared With Me</h5>
                    <a href="{{ route('files.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye me-1"></i> View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>File</th>
                                    <th>Owner</th>
                                    <th>Permission</th>
                                    <th>Shared</th>
                                    <th>Expires</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sharedFiles ?? [] as $file)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi {{ $file->icon ?? 'bi-file' }} me-2 fs-4"
                                                    style="color: var(--primary);"></i>
                                                <a href="{{ route('files.show', $file) }}"
                                                    class="text-decoration-none fw-medium">
                                                    {{ Str::limit($file->name ?? 'Unnamed', 20) }}
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if (isset($file->owner) && $file->owner)
                                                    @if ($file->owner->avatar ?? false)
                                                        <img src="{{ Storage::url($file->owner->avatar) }}"
                                                            alt="{{ $file->owner->name ?? 'User' }}"
                                                            class="rounded-circle me-2" width="28" height="28"
                                                            style="object-fit: cover;">
                                                    @else
                                                        <div class="rounded-circle bg-info d-flex align-items-center justify-content-center me-2"
                                                            style="width: 28px; height: 28px;">
                                                            <span
                                                                class="text-white small">{{ strtoupper(substr($file->owner->name ?? 'U', 0, 1)) }}</span>
                                                        </div>
                                                    @endif
                                                    <span>{{ $file->owner->name ?? 'Unknown' }}</span>
                                                @else
                                                    <span>Unknown</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $permission = $file->pivot->permission_level ?? 'view';
                                                $permissionColors = [
                                                    'view' => 'info',
                                                    'download' => 'success',
                                                    'edit' => 'warning',
                                                    'full_control' => 'danger',
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $permissionColors[$permission] ?? 'secondary' }}">
                                                {{ ucfirst($permission) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if (isset($file->pivot->created_at) &&
                                                    $file->pivot->created_at &&
                                                    method_exists($file->pivot->created_at, 'diffForHumans'))
                                                <span title="{{ $file->pivot->created_at->format('Y-m-d H:i:s') }}">
                                                    {{ $file->pivot->created_at->diffForHumans() }}
                                                </span>
                                            @else
                                                <span>N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if (isset($file->pivot->expires_at) && $file->pivot->expires_at)
                                                @php
                                                    $expiresAt = $file->pivot->expires_at;
                                                    $isExpired =
                                                        $expiresAt && method_exists($expiresAt, 'lt')
                                                            ? $expiresAt->lt(now())
                                                            : false;
                                                @endphp
                                                @if ($expiresAt && method_exists($expiresAt, 'diffForHumans'))
                                                    <span class="badge bg-{{ $isExpired ? 'danger' : 'warning' }}">
                                                        {{ $expiresAt->diffForHumans() }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">Expires soon</span>
                                                @endif
                                            @else
                                                <span class="badge bg-success">Never</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('files.download', $file) }}"
                                                class="btn btn-sm btn-outline-success" title="Download">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="bi bi-share text-muted" style="font-size: 2rem;"></i>
                                            <p class="mb-0 mt-2">No files shared with you</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row: Transfers -->
    <div class="row g-4">
        <!-- Outgoing Transfers -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Outgoing Movements</h5>
                    <a href="{{ route('transfers.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye me-1"></i> View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Transfer ID</th>
                                    <th>Receiver</th>
                                    <th>Purpose</th>
                                    <th>Expected</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($outgoingTransfers ?? [] as $transfer)
                                    <tr>
                                        <td>
                                            <a href="{{ route('transfers.show', $transfer) }}"
                                                class="text-decoration-none fw-medium">
                                                <code>{{ $transfer->transfer_id ?? 'N/A' }}</code>
                                            </a>
                                        </td>
                                        <td>
                                            @if (isset($transfer->receiver) && $transfer->receiver)
                                                <div class="d-flex align-items-center">
                                                    @if ($transfer->receiver->avatar ?? false)
                                                        <img src="{{ Storage::url($transfer->receiver->avatar) }}"
                                                            alt="{{ $transfer->receiver->name }}"
                                                            class="rounded-circle me-2" width="28" height="28"
                                                            style="object-fit: cover;">
                                                    @else
                                                        <div class="rounded-circle bg-success d-flex align-items-center justify-content-center me-2"
                                                            style="width: 28px; height: 28px;">
                                                            <span
                                                                class="text-white small">{{ strtoupper(substr($transfer->receiver->name ?? 'U', 0, 1)) }}</span>
                                                        </div>
                                                    @endif
                                                    <span>{{ $transfer->receiver->name ?? 'Unknown' }}</span>
                                                </div>
                                            @else
                                                <span>{{ $transfer->receiver_name ?? 'External' }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span title="{{ $transfer->purpose ?? '' }}">
                                                {{ Str::limit($transfer->purpose ?? '', 20) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if (isset($transfer->expected_delivery_time) &&
                                                    $transfer->expected_delivery_time &&
                                                    method_exists($transfer->expected_delivery_time, 'format'))
                                                <span
                                                    title="{{ $transfer->expected_delivery_time->format('Y-m-d H:i:s') }}">
                                                    {{ $transfer->expected_delivery_time->format('M d, H:i') }}
                                                </span>
                                            @else
                                                <span>N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'in_transit' => 'info',
                                                    'delivered' => 'success',
                                                    'cancelled' => 'danger',
                                                ];
                                                $status = $transfer->status ?? 'pending';
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$status] ?? 'secondary' }}">
                                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('transfers.show', $transfer) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="bi bi-truck text-muted" style="font-size: 2rem;"></i>
                                            <p class="mb-0 mt-2">No outgoing transfers</p>
                                            <a href="{{ route('transfers.create') }}"
                                                class="btn btn-primary btn-sm mt-2">
                                                <i class="bi bi-plus-circle"></i> Create Movement
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Incoming Pending Transfers -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Incoming Movements</h5>
                    <a href="{{ route('transfers.index', ['status' => 'pending']) }}"
                        class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye me-1"></i> View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Transfer ID</th>
                                    <th>Sender</th>
                                    <th>Purpose</th>
                                    <th>Expected</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingTransfers ?? [] as $transfer)
                                    <tr>
                                        <td>
                                            <a href="{{ route('transfers.show', $transfer) }}"
                                                class="text-decoration-none fw-medium">
                                                <code>{{ $transfer->transfer_id ?? 'N/A' }}</code>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if (isset($transfer->sender) && $transfer->sender)
                                                    @if ($transfer->sender->avatar ?? false)
                                                        <img src="{{ Storage::url($transfer->sender->avatar) }}"
                                                            alt="{{ $transfer->sender->name }}"
                                                            class="rounded-circle me-2" width="28" height="28"
                                                            style="object-fit: cover;">
                                                    @else
                                                        <div class="rounded-circle bg-info d-flex align-items-center justify-content-center me-2"
                                                            style="width: 28px; height: 28px;">
                                                            <span
                                                                class="text-white small">{{ strtoupper(substr($transfer->sender->name ?? 'U', 0, 1)) }}</span>
                                                        </div>
                                                    @endif
                                                    <span>{{ $transfer->sender->name ?? 'Unknown' }}</span>
                                                @else
                                                    <span>Unknown</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span title="{{ $transfer->purpose ?? '' }}">
                                                {{ Str::limit($transfer->purpose ?? '', 20) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if (isset($transfer->expected_delivery_time) &&
                                                    $transfer->expected_delivery_time &&
                                                    method_exists($transfer->expected_delivery_time, 'format'))
                                                <span
                                                    title="{{ $transfer->expected_delivery_time->format('Y-m-d H:i:s') }}">
                                                    {{ $transfer->expected_delivery_time->format('M d, H:i') }}
                                                </span>
                                                @php
                                                    $isOverdue =
                                                        $transfer->status !== 'delivered' &&
                                                        $transfer->expected_delivery_time->lt(now());
                                                @endphp
                                                @if ($isOverdue)
                                                    <br>
                                                    <span class="badge bg-danger mt-1">Overdue</span>
                                                @endif
                                            @else
                                                <span>N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('transfers.show', $transfer) }}"
                                                    class="btn btn-outline-primary" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if (isset($transfer->receiver_id) && $transfer->receiver_id === Auth::id() && ($transfer->status ?? '') === 'pending')
                                                    <button class="btn btn-outline-success" title="Confirm Delivery"
                                                        onclick="confirmAction('Confirm Delivery', 'Mark this transfer as delivered?', 'question', function() {
                                                            document.getElementById('confirm-form-{{ $transfer->id ?? 0 }}').submit();
                                                        })">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                    <form id="confirm-form-{{ $transfer->id ?? 0 }}"
                                                        action="{{ route('transfers.confirm', $transfer) }}"
                                                        method="POST" class="d-none">
                                                        @csrf
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <i class="bi bi-check-circle text-muted" style="font-size: 2rem;"></i>
                                            <p class="mb-0 mt-2">No pending incoming transfers</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Recent Activities</h5>
                    <a href="{{ route('logs.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye me-1"></i> View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>Module</th>
                                    <th>Time</th>
                                    <th>IP Address</th>
                                    <th>Device</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentActivities ?? [] as $activity)
                                    <tr>
                                        <td>
                                            @php
                                                $actionColor = 'info';
                                                if (($activity->action ?? '') === 'login') {
                                                    $actionColor = 'success';
                                                } elseif (($activity->action ?? '') === 'upload') {
                                                    $actionColor = 'primary';
                                                } elseif (($activity->action ?? '') === 'download') {
                                                    $actionColor = 'success';
                                                } elseif (($activity->action ?? '') === 'share') {
                                                    $actionColor = 'warning';
                                                }
                                            @endphp
                                            <span class="badge bg-{{ $actionColor }}">
                                                {{ ucfirst(str_replace('_', ' ', $activity->action ?? 'unknown')) }}
                                            </span>
                                        </td>
                                        <td>{{ $activity->description ?? '' }}</td>
                                        <td>
                                            <span
                                                class="badge bg-light text-dark">{{ ucfirst($activity->module ?? 'unknown') }}</span>
                                        </td>
                                        <td>
                                            @if (isset($activity->created_at) && $activity->created_at && method_exists($activity->created_at, 'diffForHumans'))
                                                <span title="{{ $activity->created_at->format('Y-m-d H:i:s') }}">
                                                    {{ $activity->created_at->diffForHumans() }}
                                                </span>
                                            @else
                                                <span>N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <code>{{ $activity->ip_address ?? 'N/A' }}</code>
                                        </td>
                                        <td>
                                            @if ($activity->device_type ?? false)
                                                <i
                                                    class="bi bi-{{ $activity->device_type === 'mobile' ? 'phone' : 'laptop' }} me-1"></i>
                                                {{ ucfirst($activity->device_type) }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="bi bi-clock-history text-muted" style="font-size: 2rem;"></i>
                                            <p class="mb-0 mt-2">No recent activities</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Helper function for confirm actions
        window.confirmAction = function(title, text, icon, callback) {
            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: '#4361ee',
                cancelButtonColor: '#f72585',
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    callback();
                }
            });
        };

        // Function to show share modal
        window.showShareModal = function(fileId) {
            if (!fileId) return;

            // You can implement a proper share modal here
            // For now, redirect to the file page with share tab
            window.location.href = '/files/' + fileId + '?tab=share';
        };
    </script>
@endpush
