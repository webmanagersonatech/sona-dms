{{-- resources/views/users/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Edit User</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('users.show', $user) }}">{{ $user->name }}</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Edit Form Card -->
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit User Information</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('users.update', $user) }}" id="editForm"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <!-- Avatar Upload -->
                            <div class="col-12">
                                <label class="form-label">Profile Picture</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="position-relative">
                                        @if ($user->avatar)
                                            <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}"
                                                class="rounded-circle" width="80" height="80"
                                                style="object-fit: cover;" id="avatarPreview">
                                        @else
                                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center"
                                                style="width: 80px; height: 80px;" id="avatarPreview">
                                                <span
                                                    class="text-white fs-2">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <input type="file" name="avatar"
                                            class="form-control @error('avatar') is-invalid @enderror" accept="image/*"
                                            onchange="previewAvatar(this)">
                                        <small class="text-muted">Allowed: JPG, PNG, GIF. Max 2MB</small>
                                        @error('avatar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    @if ($user->avatar)
                                        <button type="button" class="btn btn-sm btn-danger" onclick="removeAvatar()">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <!-- Full Name -->
                            <div class="col-md-6">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $user->name) }}" placeholder="Enter full name" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email Address -->
                            <div class="col-md-6">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $user->email) }}" placeholder="Enter email address" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Role -->
                            <div class="col-md-6">
                                <label class="form-label">Role <span class="text-danger">*</span></label>
                                <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                                    <option value="">Select Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Department -->
                            <div class="col-md-6">
                                <label class="form-label">Department</label>
                                <select name="department_id"
                                    class="form-select @error('department_id') is-invalid @enderror">
                                    <option value="">Select Department</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ old('department_id', $user->department_id) == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Phone Number -->
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" name="phone"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone', $user->phone) }}" placeholder="Enter phone number">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="active"
                                        {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive"
                                        {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive
                                    </option>
                                    <option value="suspended"
                                        {{ old('status', $user->status) == 'suspended' ? 'selected' : '' }}>Suspended
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Created Info (Read Only) -->
                            <div class="col-md-6">
                                <label class="form-label">Member Since</label>
                                <input type="text" class="form-control"
                                    value="{{ $user->created_at->format('F d, Y') }}" readonly disabled>
                            </div>

                            <!-- Last Login (Read Only) -->
                            <div class="col-md-6">
                                <label class="form-label">Last Login</label>
                                <input type="text" class="form-control"
                                    value="{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}"
                                    readonly disabled>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('users.show', $user) }}" class="btn btn-light px-4">
                                <i class="bi bi-x me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="bi bi-check-circle me-2"></i>Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Danger Zone -->
            @can('delete', $user)
                <div class="card mt-4 border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="card-title mb-0">Danger Zone</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="mb-1">Delete this user</h6>
                                <p class="text-muted small mb-0">Once deleted, all user data will be permanently removed.</p>
                            </div>
                            <button type="button" class="btn btn-danger" onclick="deleteUser()">
                                <i class="bi bi-trash me-2"></i>Delete User
                            </button>
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>

    <!-- Delete Form -->
    <form id="delete-form" method="POST" action="{{ route('users.destroy', $user) }}" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <!-- Remove Avatar Form -->
    <form id="remove-avatar-form" method="POST" action="{{ route('profile.avatar.remove') }}" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
    <script>
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    const preview = document.getElementById('avatarPreview');
                    if (preview.tagName === 'IMG') {
                        preview.src = e.target.result;
                    } else {
                        // Replace div with img
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'rounded-circle';
                        img.style.width = '80px';
                        img.style.height = '80px';
                        img.style.objectFit = 'cover';
                        img.id = 'avatarPreview';
                        preview.parentNode.replaceChild(img, preview);
                    }
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        function removeAvatar() {
            Swal.fire({
                title: 'Remove Avatar',
                text: 'Are you sure you want to remove the profile picture?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f72585',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, remove',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('remove-avatar-form').submit();
                }
            });
        }

        function deleteUser() {
            Swal.fire({
                title: 'Delete User',
                text: 'Are you sure you want to delete this user? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f72585',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form').submit();
                }
            });
        }
    </script>
@endpush
