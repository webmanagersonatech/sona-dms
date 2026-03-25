{{-- resources/views/departments/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Department')

@section('content')
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Create New Department</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">Departments</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Create Form Card -->
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Department Information</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('departments.store') }}" id="createForm">
                        @csrf

                        <div class="row g-4">
                            <!-- Department Name -->
                            <div class="col-md-6">
                                <label class="form-label">Department Name <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    placeholder="e.g., Human Resources" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Department Code -->
                            <div class="col-md-6">
                                <label class="form-label">Department Code <span class="text-danger">*</span></label>
                                <input type="text" name="code"
                                    class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}"
                                    placeholder="e.g., HR" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4"
                                    placeholder="Enter department description...">{{ old('description') }}</textarea>
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
                                            value="active" {{ old('status', 'active') == 'active' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="statusActive">
                                            Active
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="statusInactive"
                                            value="inactive" {{ old('status') == 'inactive' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="statusInactive">
                                            Inactive
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('departments.index') }}" class="btn btn-light px-4">
                                <i class="bi bi-x me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="bi bi-check-circle me-2"></i>Create Department
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help Card -->
            <div class="card mt-4 bg-light">
                <div class="card-body">
                    <h6><i class="bi bi-info-circle me-2"></i> Department Creation Guidelines</h6>
                    <ul class="small text-muted mb-0">
                        <li>Department name should be unique and descriptive</li>
                        <li>Department code should be a short, unique identifier (e.g., HR, IT, FIN)</li>
                        <li>Provide a clear description of the department's purpose</li>
                        <li>Inactive departments won't be available for user assignment</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Auto-uppercase code
        document.querySelector('input[name="code"]').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });

        // Form validation
        document.getElementById('createForm').addEventListener('submit', function(e) {
            const name = document.querySelector('input[name="name"]').value.trim();
            const code = document.querySelector('input[name="code"]').value.trim();

            if (!name || !code) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please fill in all required fields.',
                    confirmButtonColor: '#4361ee'
                });
            }
        });
    </script>
@endpush
