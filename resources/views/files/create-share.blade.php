@extends('layouts.app')

@section('title', 'Share File - ' . $file->original_name)

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                <i class="fas fa-share-alt"></i>
                                Share File: {{ $file->original_name }}
                            </h4>
                            <p class="text-muted mb-0">
                                Create a secure share link for this file
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('files.shares', $file) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Shares
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cog"></i> Share Settings
                    </h3>
                </div>
                <form action="{{ route('files.shares.store', $file) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <!-- Share With -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shared_with">Share With User (Internal)</label>
                                    <select class="form-control @error('shared_with') is-invalid @enderror" 
                                            id="shared_with" name="shared_with">
                                        <option value="">Select User</option>
                                        @foreach($departmentUsers as $user)
                                        <option value="{{ $user->id }}" {{ old('shared_with') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }}) - {{ $user->role->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('shared_with')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shared_email">Or External Email</label>
                                    <input type="email" 
                                           class="form-control @error('shared_email') is-invalid @enderror" 
                                           id="shared_email" 
                                           name="shared_email" 
                                           value="{{ old('shared_email') }}" 
                                           placeholder="external@example.com">
                                    @error('shared_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Leave both empty to create a public share link
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note:</strong> If you select a user, they will be notified. If you enter an email, 
                            the link will be sent to that email. If both are empty, you'll get a shareable link.
                        </div>

                        <!-- Permissions -->
                        <div class="form-group">
                            <label>Permissions</label>
                            <div class="row">
                                @foreach(['view' => 'fas fa-eye', 'download' => 'fas fa-download', 'edit' => 'fas fa-edit', 'print' => 'fas fa-print'] as $permission => $icon)
                                <div class="col-md-3">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="permission_{{ $permission }}" 
                                               name="permissions[]" 
                                               value="{{ $permission }}"
                                               {{ in_array($permission, old('permissions', ['view', 'download'])) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="permission_{{ $permission }}">
                                            <i class="{{ $icon }} mr-1"></i>
                                            {{ ucfirst($permission) }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @error('permissions')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Validity Period -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="valid_from">Valid From (Optional)</label>
                                    <input type="datetime-local" 
                                           class="form-control @error('valid_from') is-invalid @enderror" 
                                           id="valid_from" 
                                           name="valid_from" 
                                           value="{{ old('valid_from') }}">
                                    @error('valid_from')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Leave empty for immediate access
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="valid_until">Valid Until *</label>
                                    <input type="datetime-local" 
                                           class="form-control @error('valid_until') is-invalid @enderror" 
                                           id="valid_until" 
                                           name="valid_until" 
                                           value="{{ old('valid_until') ?? now()->addDays(7)->format('Y-m-d\TH:i') }}"
                                           required>
                                    @error('valid_until')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Share link will expire after this date
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Access Limits -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="max_access_count">Max Access Count (Optional)</label>
                                    <input type="number" 
                                           class="form-control @error('max_access_count') is-invalid @enderror" 
                                           id="max_access_count" 
                                           name="max_access_count" 
                                           value="{{ old('max_access_count') }}"
                                           min="1" 
                                           placeholder="Unlimited">
                                    @error('max_access_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Share link will expire after this many accesses
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox mt-4">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="requires_otp_approval" 
                                               name="requires_otp_approval" 
                                               value="1"
                                               {{ old('requires_otp_approval', true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="requires_otp_approval">
                                            <i class="fas fa-shield-alt"></i>
                                            Require OTP Approval
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        You will receive an OTP when someone accesses this file
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- File Information -->
                        <div class="card mt-4">
                            <div class="card-body bg-light">
                                <h6 class="card-title">
                                    <i class="fas fa-file"></i> File Information
                                </h6>
                                <table class="table table-sm">
                                    <tr>
                                        <th>File Name:</th>
                                        <td>{{ $file->original_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Type:</th>
                                        <td>{{ strtoupper($file->extension) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Size:</th>
                                        <td>{{ $file->formatted_size }}</td>
                                    </tr>
                                    <tr>
                                        <th>Owner:</th>
                                        <td>{{ $file->owner->name }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-share-alt"></i> Create Share Link
                        </button>
                        <a href="{{ route('files.shares', $file) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Set default time for datetime inputs
    document.addEventListener('DOMContentLoaded', function() {
        const now = new Date();
        const weekLater = new Date(now.getTime() + 7 * 24 * 60 * 60 * 1000);
        
        // Format for datetime-local input (YYYY-MM-DDTHH:MM)
        function formatDateTime(date) {
            return date.toISOString().slice(0, 16);
        }
        
        // Set default values if not already set
        if (!document.getElementById('valid_from').value) {
            document.getElementById('valid_from').value = formatDateTime(now);
        }
        
        if (!document.getElementById('valid_until').value) {
            document.getElementById('valid_until').value = formatDateTime(weekLater);
        }
        
        // Ensure valid_until is after valid_from
        document.getElementById('valid_from').addEventListener('change', function() {
            const validFrom = new Date(this.value);
            const validUntil = new Date(document.getElementById('valid_until').value);
            
            if (validUntil <= validFrom) {
                const newValidUntil = new Date(validFrom.getTime() + 24 * 60 * 60 * 1000);
                document.getElementById('valid_until').value = formatDateTime(newValidUntil);
            }
        });
    });
</script>
@endpush