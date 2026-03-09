@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <!-- Profile Card -->
                <div class="card card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-circle mr-1"></i>
                            Profile Information
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="user-profile-image mb-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0D8ABC&color=fff&size=150"
                                    class="img-circle elevation-2" alt="User Image" style="width: 150px; height: 150px;">
                            </div>
                            <h4>{{ auth()->user()->name }}</h4>
                            <p class="text-muted mb-1">{{ auth()->user()->email }}</p>
                            <span class="badge badge-{{ auth()->user()->is_active ? 'success' : 'danger' }}">
                                {{ auth()->user()->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <table class="table table-sm">
                            <tr>
                                <th width="40%">Employee ID:</th>
                                <td>{{ auth()->user()->employee_id ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Role:</th>
                                <td>
                                    <span class="badge badge-info">{{ auth()->user()->role->name }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Department:</th>
                                <td>{{ auth()->user()->department->name }}</td>
                            </tr>
                            <tr>
                                <th>Phone:</th>
                                <td>{{ auth()->user()->phone ?? 'Not set' }}</td>
                            </tr>
                            <tr>
                                <th>Joined:</th>
                                <td>{{ auth()->user()->created_at->format('M d, Y') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>


            </div>

            <div class="col-md-8">
                <!-- Update Profile Form -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-edit mr-1"></i>
                            Update Profile
                        </h3>
                    </div>
                    <form action="{{ route('profile.index') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Full Name *</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name', auth()->user()->name) }}"
                                            required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Phone Number</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                            id="phone" name="phone"
                                            value="{{ old('phone', auth()->user()->phone) }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="email">Email Address</label>
                                        <input type="email" class="form-control bg-light" id="email"
                                            value="{{ auth()->user()->email }}" disabled>
                                        <small class="form-text text-muted">
                                            Email address cannot be changed. Contact administrator if needed.
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h5 class="mb-3">
                                <i class="fas fa-lock mr-1"></i>
                                Change Password
                            </h5>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="current_password">Current Password</label>
                                        <input type="password"
                                            class="form-control @error('current_password') is-invalid @enderror"
                                            id="current_password" name="current_password">
                                        @error('current_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="new_password">New Password</label>
                                        <input type="password"
                                            class="form-control @error('new_password') is-invalid @enderror"
                                            id="new_password" name="new_password">
                                        @error('new_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="new_password_confirmation">Confirm New Password</label>
                                        <input type="password" class="form-control" id="new_password_confirmation"
                                            name="new_password_confirmation">
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                Password must be at least 8 characters long and include uppercase, lowercase, numbers, and
                                special characters.
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                            <a href="{{ route('profile.activity-logs') }}" class="btn btn-info">
                                <i class="fas fa-history"></i> View Activity Logs
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Connected Devices -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-laptop mr-1"></i>
                            Connected Devices
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('devices.index') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-cog"></i> Manage
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Device</th>
                                        <th>Browser</th>
                                        <th>IP Address</th>
                                        <th>Last Activity</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($devices as $device)
                                        <tr>
                                            <td>
                                                <i
                                                    class="fas fa-{{ $device->device_type === 'mobile' ? 'mobile-alt' : 'laptop' }}"></i>
                                                {{ $device->device_name }}
                                            </td>
                                            <td>{{ $device->browser }}</td>
                                            <td><code>{{ $device->ip_address }}</code></td>
                                            <td>{{ $device->last_activity_at->diffForHumans() }}</td>
                                            <td>
                                                @if ($device->is_active)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactive</span>
                                                @endif
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
    @endsection
