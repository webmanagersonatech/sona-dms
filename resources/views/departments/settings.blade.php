{{-- resources/views/departments/settings.blade.php --}}
@extends('layouts.app')

@section('title', 'Department Settings - ' . $department->name)

@section('content')
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Department Settings</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">Departments</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('departments.show', $department) }}">{{ $department->code }}</a></li>
                    <li class="breadcrumb-item active">Settings</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('departments.show', $department) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Details
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-gear-fill me-2"></i>Configure {{ $department->name }}
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('departments.settings.update', $department) }}">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="max_storage_gb" class="form-label fw-bold">Maximum Storage Limit (GB)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="max_storage_gb" name="max_storage_gb" 
                                        value="{{ $settings['max_storage_gb'] ?? 10 }}" min="1" max="1000" required>
                                    <span class="input-group-text">GB</span>
                                </div>
                                <small class="text-muted">Total storage quota allocated to this department.</small>
                            </div>
                            <div class="col-md-6">
                                <label for="auto_purge_days" class="form-label fw-bold">Auto-Purge Inactive Files</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="auto_purge_days" name="auto_purge_days" 
                                        value="{{ $settings['auto_purge_days'] ?? 0 }}" min="0" required>
                                    <span class="input-group-text">Days</span>
                                </div>
                                <small class="text-muted">Delete files after X days of inactivity (0 to disable).</small>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="fw-bold mb-3">Security & Sharing Policy</h6>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="allow_external_sharing" 
                                    name="allow_external_sharing" value="1"
                                    {{ ($settings['allow_external_sharing'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="allow_external_sharing">
                                    Allow external file sharing (outside department)
                                </label>
                            </div>
                            <small class="text-muted d-block ms-1">If disabled, users can only share files with colleagues in the same department.</small>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="require_otp_for_all" 
                                    name="require_otp_for_all" value="1"
                                    {{ ($settings['require_otp_for_all'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label text-danger fw-bold" for="require_otp_for_all">
                                    Mandate OTP verification for EVERY file access
                                </label>
                            </div>
                            <small class="text-muted d-block ms-1">When enabled, every view or download request requires a fresh OTP, even for the department admin.</small>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-2"></i>Save Department Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Recent Changes Log for Department -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0 text-muted">Recent Configuration Changes</h5>
                </div>
                <div class="card-body p-0">
                    @php
                        $logs = App\Models\ActivityLog::where('module', 'department')
                            ->where('action', 'update_settings')
                            ->where('description', 'like', '%' . $department->name . '%')
                            ->latest()
                            ->take(5)
                            ->get();
                    @endphp

                    @if($logs->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($logs as $log)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-primary fw-bold">{{ $log->user->name }}</small>
                                        <div class="small text-muted">{{ $log->created_at->format('M d, Y h:i A') }}</div>
                                    </div>
                                    <span class="badge bg-light text-dark">IP: {{ $log->ip_address }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 text-center text-muted">
                            No settings changes logged yet.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
