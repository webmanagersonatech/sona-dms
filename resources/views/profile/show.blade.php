{{-- resources/views/profile/show.blade.php --}}
@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="position-relative d-inline-block">
                        @if ($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}"
                                class="rounded-circle img-thumbnail" width="150" height="150">
                        @else
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto"
                                style="width: 150px; height: 150px;">
                                <span class="text-white" style="font-size: 3rem;">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </span>
                            </div>
                        @endif

                        <button class="btn btn-sm btn-primary position-absolute bottom-0 end-0" data-bs-toggle="modal"
                            data-bs-target="#avatarModal">
                            <i class="bi bi-camera"></i>
                        </button>
                    </div>

                    <h4 class="mt-3">{{ $user->name }}</h4>
                    <p class="text-muted">{{ $user->role->name }}</p>

                    <div class="mt-3">
                        <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'warning' }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </div>

                    <hr>

                    <div class="text-start">
                        <p><i class="bi bi-envelope me-2"></i> {{ $user->email }}</p>
                        <p><i class="bi bi-telephone me-2"></i> {{ $user->phone ?? 'Not provided' }}</p>
                        <p><i class="bi bi-building me-2"></i> {{ $user->department->name ?? 'No department' }}</p>
                        <p><i class="bi bi-calendar me-2"></i> Joined {{ $user->created_at->format('M d, Y') }}</p>
                        <p><i class="bi bi-clock me-2"></i> Last login
                            {{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</p>
                    </div>

                    <hr>

                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="bi bi-pencil"></i> Edit Profile
                        </button>
                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            <i class="bi bi-key"></i> Change Password
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Activities</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach ($recentActivities as $activity)
                            <div class="timeline-item">
                                <div class="timeline-badge bg-{{ $activity->action === 'login' ? 'success' : 'info' }}">
                                    <i
                                        class="bi bi-{{ $activity->action === 'login' ? 'box-arrow-in-right' : 'activity' }}"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>{{ ucfirst($activity->action) }}</h6>
                                    <p>{{ $activity->description }}</p>
                                    <small class="text-muted">
                                        <i class="bi bi-clock"></i> {{ $activity->created_at->diffForHumans() }}
                                        <br>
                                        <i class="bi bi-geo-alt"></i> {{ $activity->ip_address }}
                                        @if ($activity->device_type)
                                            <br>
                                            <i
                                                class="bi bi-{{ $activity->device_type === 'mobile' ? 'phone' : 'laptop' }}"></i>
                                            {{ ucfirst($activity->device_type) }} - {{ $activity->browser }}
                                        @endif
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Profile</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ $user->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ $user->email }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone"
                                value="{{ $user->phone }}">
                        </div>

                        <div class="mb-3">
                            <label for="avatar" class="form-label">Profile Picture</label>
                            <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                            <small class="text-muted">Max size: 2MB</small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">Change Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                            <small class="text-muted">Minimum 8 characters</small>
                        </div>

                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="new_password_confirmation"
                                name="new_password_confirmation" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Avatar Modal -->
    <div class="modal fade" id="avatarModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">Update Profile Picture</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="avatar_single" class="form-label">Choose Image</label>
                            <input type="file" class="form-control" id="avatar_single" name="avatar"
                                accept="image/*" required>
                        </div>

                        <div class="text-center">
                            <img id="preview" src="#" alt="Preview" class="img-thumbnail d-none"
                                style="max-width: 200px; max-height: 200px;">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
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
            padding: 20px 0;
        }

        .timeline-item {
            position: relative;
            padding-left: 60px;
            margin-bottom: 30px;
        }

        .timeline-badge {
            position: absolute;
            left: 0;
            top: 0;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .timeline-content {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.getElementById('avatar_single').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                    document.getElementById('preview').classList.remove('d-none');
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
@endpush
