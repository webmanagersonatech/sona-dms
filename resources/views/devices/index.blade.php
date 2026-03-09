@extends('layouts.app')

@section('title', 'Device Management')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex justify-content-end align-items-center">

                    <div class="mb-0">
                        <a href="{{ route('profile.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Back to Profile
                        </a>
                    </div>

                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Connected Devices</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($devices->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Device</th>
                                            <th>Browser</th>
                                            <th>Operating System</th>
                                            <th>IP Address</th>
                                            <th>Last Login</th>
                                            <th>Last Activity</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($devices as $device)
                                            <tr
                                                class="{{ session('device_id') === $device->device_id ? 'table-active' : '' }}">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="mr-3">
                                                            @if ($device->device_type === 'mobile')
                                                                <i class="fas fa-mobile-alt fa-2x text-primary"></i>
                                                            @elseif($device->device_type === 'tablet')
                                                                <i class="fas fa-tablet-alt fa-2x text-info"></i>
                                                            @else
                                                                <i class="fas fa-desktop fa-2x text-success"></i>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <strong>{{ $device->device_name }}</strong>
                                                            @if (session('device_id') === $device->device_id)
                                                                <br>
                                                                <small class="text-success">
                                                                    <i class="fas fa-circle"></i> Current Device
                                                                </small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ $device->browser }}
                                                </td>
                                                <td>
                                                    {{ $device->os }}
                                                </td>
                                                <td>
                                                    <code>{{ $device->ip_address }}</code>
                                                    @if ($device->location)
                                                        <br>
                                                        <small class="text-muted">{{ $device->location }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $device->last_login_at->format('M d, Y') }}<br>
                                                    <small>{{ $device->last_login_at->format('H:i') }}</small>
                                                </td>
                                                <td>
                                                    {{ $device->last_activity_at->diffForHumans() }}
                                                </td>
                                                <td>
                                                    @if ($device->is_active)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-secondary">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($device->is_active && session('device_id') !== $device->device_id)
                                                        <form action="{{ route('devices.revoke', $device) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Are you sure you want to revoke access for this device?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                title="Revoke Access">
                                                                <i class="fas fa-ban"></i>
                                                            </button>
                                                        </form>
                                                    @elseif(session('device_id') === $device->device_id)
                                                        <span class="badge badge-info">Current</span>
                                                    @else
                                                        <span class="text-muted">Revoked</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-laptop fa-4x text-muted mb-3"></i>
                                <h4>No Devices Found</h4>
                                <p class="text-muted">No devices are currently connected to your account.</p>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Device Security Tips:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Regularly review connected devices</li>
                                        <li>Revoke access for devices you no longer use</li>
                                        <li>Report any unfamiliar devices immediately</li>
                                        <li>Use secure networks when accessing the system</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-4 text-right">
                                <div class="small text-muted">
                                    <i class="fas fa-shield-alt"></i>
                                    {{ $devices->where('is_active', true)->count() }} active device(s)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Device Statistics -->
        <div class="row mt-3">
            <div class="col-md-3 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-primary">
                        <i class="fas fa-desktop"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Devices</span>
                        <span class="info-box-number">{{ $devices->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-success">
                        <i class="fas fa-check-circle"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Active Devices</span>
                        <span class="info-box-number">{{ $devices->where('is_active', true)->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-info">
                        <i class="fas fa-mobile-alt"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Mobile Devices</span>
                        <span class="info-box-number">{{ $devices->where('device_type', 'mobile')->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-warning">
                        <i class="fas fa-clock"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Recent Activity</span>
                        <span class="info-box-number">
                            {{ $devices->where('last_activity_at', '>', now()->subDay())->count() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
