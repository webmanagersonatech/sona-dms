{{-- resources/views/departments/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Department')

@section('content')
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Edit Department</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">Departments</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('departments.show', $department) }}">{{ $department->code }}</a></li>
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
                    <h5 class="card-title mb-0">Edit Department Information</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('departments.update', $department) }}" id="editForm">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <!-- Department Name -->
                            <div class="col-md-6">
                                <label class="form-label">Department Name <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $department->name) }}" placeholder="e.g., Human Resources"
                                    required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Department Code -->
                            <div class="col-md-6">
                                <label class="form-label">Department Code <span class="text-danger">*</span></label>
                                <input type="text" name="code"
                                    class="form-control @error('code') is-invalid @enderror"
                                    value="{{ old('code', $department->code) }}" placeholder="e.g., HR" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4"
                                    placeholder="Enter department description...">{{ old('description', $department->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="statusActive"
                                            value="active"
                                            {{ old('status', $department->status) == 'active' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="statusActive">
                                            Active
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="statusInactive"
                                            value="inactive"
                                            {{ old('status', $department->status) == 'inactive' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="statusInactive">
                                            Inactive
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Created Info (Read Only) -->
                            <div class="col-md-6">
                                <label class="form-label">Created</label>
                                <input type="text" class="form-control"
                                    value="{{ $department->created_at->format('F d, Y') }}" readonly disabled>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('departments.show', $department) }}" class="btn btn-light px-4">
                                <i class="bi bi-x me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="bi bi-check-circle me-2"></i>Update Department
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Danger Zone -->
            @can('delete', $department)
                <div class="card mt-4 border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="card-title mb-0">Danger Zone</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="mb-1">Delete this department</h6>
                                <p class="text-muted small mb-0">Once deleted, all data will be permanently removed.</p>
                            </div>
                            <button type="button" class="btn btn-danger" onclick="deleteDepartment()">
                                <i class="bi bi-trash me-2"></i>Delete Department
                            </button>
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>

    <!-- Delete Form -->
    <form id="delete-form" method="POST" action="{{ route('departments.destroy', $department) }}" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
    <script>
        // Auto-uppercase code
        document.querySelector('input[name="code"]').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });

        function deleteDepartment() {
            Swal.fire({
                title: 'Delete Department',
                text: 'Are you sure you want to delete this department? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f72585',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form').submit();
                }
            });
        }
    </script>
@endpush
