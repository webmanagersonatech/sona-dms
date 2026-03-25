{{-- resources/views/users/show.blade.php --}}
@extends('layouts.app')

@section('title', $user->name)

@section('content')
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">User Profile</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                    <li class="breadcrumb-item active">{{ $user->name }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            @can('update', $user)
                <a href="{{ route('users.edit', $user) }}" class="btn btn-info">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
            @endcan
            @can('delete', $user)
                @if ($user->status === 'suspended')
                    <button type="button" class="btn btn-success" onclick="activateUser()">
                        <i class="bi bi-check-circle me-2"></i>Activate
                    </button>
                @else
                    <button type="button" class="btn btn-danger" onclick="suspendUser()">
                        <i class="bi bi-person-slash me-2"></i>Suspend
                    </button>
                @endif
            @endcan
            @can('update', $user)
                <button type="button" class="btn btn-warning" onclick="resetPassword()">
                    <i class="bi bi-key me-2"></i>Reset Password
                </button>
            @endcan
        </div>
    </div>

    <!-- User Profile Card -->
    <div class="row g-4">
        <!-- Left Column - Profile Info -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center p-4">
                    <!-- Avatar -->
                    <div class="position-relative d-inline-block mb-3">
                        @if ($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}"
                                class="rounded-circle img-thumbnail" width="120" height="120"
                                style="object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto"
                                style="width: 120px; height: 120px;">
                                <span class="text-white" style="font-size: 3rem;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif

                        @if ($user->status === 'active')
                            <span
                                class="position-absolute bottom-0 end-0 bg-success rounded-circle p-2 border border-white"></span>
                        @elseif($user->status === 'inactive')
                            <span
                                class="position-absolute bottom-0 end-0 bg-warning rounded-circle p-2 border border-white"></span>
                        @else
                            <span
                                class="position-absolute bottom-0 end-0 bg-danger rounded-circle p-2 border border-white"></span>
                        @endif
                    </div>

                    <h4 class="mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-2">{{ $user->email }}</p>

                    <div class="mb-3">
                        <span class="badge bg-info">{{ $user->role->name ?? 'No Role' }}</span>
                        @if ($user->department)
                            <span class="badge bg-light text-dark">{{ $user->department->name }}</span>
                        @endif
                    </div>

                    <div class="d-flex justify-content-center gap-2 mb-3">
                        @if ($user->two_factor_enabled)
                            <span class="badge bg-success">
                                <i class="bi bi-shield-check me-1"></i>2FA Enabled
                            </span>
                        @endif
                        @if ($user->email_verified)
                            <span class="badge bg-success">
                                <i class="bi bi-envelope-check me-1"></i>Email Verified
                            </span>
                        @endif
                    </div>

                    <hr>

                    <!-- Contact Info -->
                    <div class="text-start">
                        <h6 class="fw-bold mb-3">Contact Information</h6>
                        <div class="mb-2">
                            <i class="bi bi-envelope me-2 text-muted"></i>
                            <span>{{ $user->email }}</span>
                        </div>
                        @if ($user->phone)
                            <div class="mb-2">
                                <i class="bi bi-telephone me-2 text-muted"></i>
                                <span>{{ $user->phone }}</span>
                            </div>
                        @endif
                    </div>

                    <hr>

                    <!-- Account Info -->
                    <div class="text-start">
                        <h6 class="fw-bold mb-3">Account Information</h6>
                        <div class="mb-2">
                            <i class="bi bi-calendar me-2 text-muted"></i>
                            <span>Joined: {{ $user->created_at->format('F d, Y') }}</span>
                        </div>
                        @if ($user->last_login_at)
                            <div class="mb-2">
                                <i class="bi bi-clock-history me-2 text-muted"></i>
                                <span>Last Login: {{ $user->last_login_at->diffForHumans() }}</span>
                            </div>
                            <div class="mb-2">
                                <i class="bi bi-geo-alt me-2 text-muted"></i>
                                <span>Last IP: {{ $user->last_login_ip ?? 'Unknown' }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Statistics and Activity -->
        <div class="col-lg-8">
            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>{{ number_format($stats['total_files']) }}</h3>
                            <p>Files</p>
                        </div>
                        <div class="stat-icon primary">
                            <i class="bi bi-files"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>{{ number_format($stats['total_transfers_sent']) }}</h3>
                            <p>Sent</p>
                        </div>
                        <div class="stat-icon success">
                            <i class="bi bi-arrow-up"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>{{ number_format($stats['total_transfers_received']) }}</h3>
                            <p>Received</p>
                        </div>
                        <div class="stat-icon warning">
                            <i class="bi bi-arrow-down"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>{{ number_format($stats['total_downloads']) }}</h3>
                            <p>Downloads</p>
                        </div>
                        <div class="stat-icon info">
                            <i class="bi bi-download"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Files -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Files</h5>
                    <a href="{{ route('files.index', ['owner_id' => $user->id]) }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body p-0">
                    @if ($user->files->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>File</th>
                                        <th>Size</th>
                                        <th>Downloads</th>
                                        <th>Uploaded</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($user->files as $file)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi {{ $file->icon }} me-2"
                                                        style="color: var(--primary);"></i>
                                                    <a href="{{ route('files.show', $file) }}"
                                                        class="text-decoration-none">
                                                        {{ Str::limit($file->name, 30) }}
                                                    </a>
                                                </div>
                                            </td>
                                            <td>{{ $file->size_for_humans }}</td>
                                            <td>{{ number_format($file->download_count) }}</td>
                                            <td>{{ $file->created_at->diffForHumans() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-files text-muted" style="font-size: 2rem;"></i>
                            <p class="mb-0">No files uploaded yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Activity</h5>
                    <a href="{{ route('logs.index', ['user_id' => $user->id]) }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body p-0">
                    @if ($recentActivities->isNotEmpty())
                        <div class="list-group list-group-flush">
                            @foreach ($recentActivities as $activity)
                                <div class="list-group-item">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="stat-icon primary" style="width: 35px; height: 35px;">
                                                <i
                                                    class="bi bi-{{ $activity->action === 'login' ? 'box-arrow-in-right' : 'activity' }}"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <strong>{{ ucfirst($activity->action) }}</strong>
                                                    <span class="text-muted">•</span>
                                                    <span>{{ $activity->description }}</span>
                                                </div>
                                                <small
                                                    class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                            </div>
                                            <small class="text-muted">
                                                <i class="bi bi-geo-alt"></i> {{ $activity->ip_address ?? 'N/A' }}
                                                @if ($activity->device_type)
                                                    • <i
                                                        class="bi bi-{{ $activity->device_type === 'mobile' ? 'phone' : 'laptop' }}"></i>
                                                    {{ ucfirst($activity->device_type) }}
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-clock-history text-muted" style="font-size: 2rem;"></i>
                            <p class="mb-0">No recent activity</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Forms -->
    <form id="activate-form" method="POST" action="{{ route('users.activate', $user) }}" style="display: none;">
        @csrf
        @method('POST')
    </form>

    <form id="suspend-form" method="POST" action="{{ route('users.destroy', $user) }}" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <form id="reset-password-form" method="POST" action="{{ route('users.reset-password', $user) }}"
        style="display: none;">
        @csrf
        @method('POST')
    </form>
@endsection

@push('scripts')
    <script>
        function activateUser() {
            Swal.fire({
                title: 'Activate User',
                text: 'Are you sure you want to activate this user?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, activate',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('activate-form').submit();
                }
            });
        }

        function suspendUser() {
            Swal.fire({
                title: 'Suspend User',
                text: 'Are you sure you want to suspend this user? They will not be able to login.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f72585',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, suspend',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('suspend-form').submit();
                }
            });
        }

        function resetPassword() {
            Swal.fire({
                title: 'Reset Password',
                text: 'Are you sure you want to reset this user\'s password? A new password will be sent to their email.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, reset',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('reset-password-form').submit();
                }
            });
        }
    </script>
@endpush
