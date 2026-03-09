@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <!-- Welcome Card -->
        <div class="row">
            <div class="col-12">
                <div class="card card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-home mr-1"></i>
                            Welcome, {{ auth()->user()->name }}!
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="info-box bg-light">
                                            <span class="info-box-icon bg-info">
                                                <i class="fas fa-file"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Your Files</span>
                                                <span class="info-box-number">
                                                    {{ auth()->user()->files()->count() }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="info-box bg-light">
                                            <span class="info-box-icon bg-success">
                                                <i class="fas fa-paper-plane"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Sent Transfers</span>
                                                <span class="info-box-number">
                                                    {{ auth()->user()->sentTransfers()->count() }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="info-box bg-light">
                                            <span class="info-box-icon bg-warning">
                                                <i class="fas fa-inbox"></i>
                                            </span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Received</span>
                                                <span class="info-box-number">
                                                    {{ auth()->user()->receivedTransfers()->count() }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-gradients">
                                    <div class="card-body">
                                        <h5><i class="fas fa-shield-alt"></i> Security Status</h5>
                                        <div class="mt-3">
                                            <div class="mb-2">
                                                <i class="fas fa-check-circle text-success"></i>
                                                <span>Device Verified</span>
                                            </div>
                                            <div class="mb-2">
                                                <i class="fas fa-check-circle text-success"></i>
                                                <span>OTP Enabled</span>
                                            </div>
                                            <div class="mb-2">
                                                <i class="fas fa-check-circle text-success"></i>
                                                <span>Encryption Active</span>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-bolt mr-1"></i> Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @can('files.upload')
                                <div class="col-sm-3 col-6">
                                    <a href="{{ route('files.create') }}" class="btn btn-app bg-primary">
                                        <i class="fas fa-upload fa-2x"></i>
                                        <span>Upload File</span>
                                    </a>
                                </div>
                            @endcan

                            @can('files.view')
                                <div class="col-sm-3 col-6">
                                    <a href="{{ route('files.index') }}" class="btn btn-app bg-info">
                                        <i class="fas fa-folder fa-2x"></i>
                                        <span>View Files</span>
                                    </a>
                                </div>
                            @endcan

                            @can('transfers.create')
                                <div class="col-sm-3 col-6">
                                    <a href="{{ route('transfers.create') }}" class="btn btn-app bg-success">
                                        <i class="fas fa-paper-plane fa-2x"></i>
                                        <span>New Transfer</span>
                                    </a>
                                </div>
                            @endcan

                            @can('transfers.view')
                                <div class="col-sm-3 col-6">
                                    <a href="{{ route('transfers.index') }}" class="btn btn-app bg-warning">
                                        <i class="fas fa-exchange-alt fa-2x"></i>
                                        <span>View Transfers</span>
                                    </a>
                                </div>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity & Stats -->
        <div class="row">
            <!-- Recent Files -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-file mr-1"></i> Recent Files</h3>
                        <div class="card-tools">
                            <a href="{{ route('files.index') }}" class="btn btn-sm btn-primary">View All</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>File Name</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (auth()->user()->files()->latest()->limit(5)->get() as $file)
                                        <tr>
                                            <td>
                                                <i
                                                    class="fas fa-file-{{ $file->extension === 'pdf' ? 'pdf text-danger' : 'word text-primary' }}"></i>
                                                {{ Str::limit($file->original_name, 25) }}
                                            </td>
                                            <td>{{ strtoupper($file->extension) }}</td>
                                            <td>{{ $file->created_at->format('M d') }}</td>
                                            <td>
                                                <a href="{{ route('files.show', $file) }}" class="btn btn-xs btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transfers -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-exchange-alt mr-1"></i> Recent Transfers</h3>
                        <div class="card-tools">
                            <a href="{{ route('transfers.index') }}" class="btn btn-sm btn-primary">View All</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>File</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $recentTransfers = auth()
                                            ->user()
                                            ->sentTransfers()
                                            ->orWhere('receiver_id', auth()->id())
                                            ->with('file')
                                            ->latest()
                                            ->limit(5)
                                            ->get();
                                    @endphp
                                    @foreach ($recentTransfers as $transfer)
                                        <tr>
                                            <td>
                                                {{ Str::limit($transfer->file->original_name, 20) }}
                                            </td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $transfer->transfer_type === 'physical' ? 'info' : 'success' }}">
                                                    {{ ucfirst($transfer->transfer_type) }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $badgeClass = match ($transfer->status) {
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
                                                <a href="{{ route('transfers.show', $transfer) }}"
                                                    class="btn btn-xs btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-bell mr-1"></i> Notifications</h3>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @php
                                $notifications = \App\Models\ActivityLog::where('user_id', auth()->id())
                                    ->orWhere('action', 'like', '%' . auth()->user()->email . '%')
                                    ->orderBy('performed_at', 'desc')
                                    ->limit(5)
                                    ->get();

                            @endphp

                            @forelse($notifications as $notification)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            @switch($notification->action)
                                                @case('file_upload')
                                                    <i class="fas fa-file-upload text-success"></i>
                                                @break

                                                @case('file_download')
                                                    <i class="fas fa-file-download text-primary"></i>
                                                @break

                                                @case('transfer_create')
                                                    <i class="fas fa-paper-plane text-info"></i>
                                                @break

                                                @case('transfer_receive')
                                                    <i class="fas fa-check-circle text-success"></i>
                                                @break

                                                @default
                                                    <i class="fas fa-info-circle text-secondary"></i>
                                            @endswitch
                                            {{ $notification->description }}
                                        </h6>
                                        <small>{{ $notification->performed_at->diffForHumans() }}</small>
                                    </div>
                                    @if ($notification->file)
                                        <small class="text-muted">
                                            File: {{ $notification->file->original_name }}
                                        </small>
                                    @endif
                                </div>
                                @empty
                                    <div class="list-group-item text-center text-muted">
                                        <i class="fas fa-bell-slash fa-2x mb-2"></i>
                                        <p>No notifications yet</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
