@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">System Settings</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Application Settings</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="app_name">Application Name</label>
                            <input type="text" class="form-control" id="app_name" name="app_name" 
                                   value="{{ $settings['app_name'] ?? '' }}" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="app_url">Application URL</label>
                            <input type="url" class="form-control" id="app_url" name="app_url" 
                                   value="{{ $settings['app_url'] ?? '' }}" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="file_upload_max_size">Max File Upload Size (MB)</label>
                            <input type="number" class="form-control" id="file_upload_max_size" 
                                   name="file_upload_max_size" 
                                   value="{{ $settings['file_upload_max_size'] ?? 50 }}" min="1" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="file_expiry_days">File Expiry (Days)</label>
                            <input type="number" class="form-control" id="file_expiry_days" 
                                   name="file_expiry_days" 
                                   value="{{ $settings['file_expiry_days'] ?? 30 }}" min="1" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="transfer_expiry_hours">Transfer Expiry (Hours)</label>
                            <input type="number" class="form-control" id="transfer_expiry_hours" 
                                   name="transfer_expiry_hours" 
                                   value="{{ $settings['transfer_expiry_hours'] ?? 72 }}" min="1" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="otp_expiry_minutes">OTP Expiry (Minutes)</label>
                            <input type="number" class="form-control" id="otp_expiry_minutes" 
                                   name="otp_expiry_minutes" 
                                   value="{{ $settings['otp_expiry_minutes'] ?? 10 }}" min="1" required>
                        </div>
                        
                        {{-- <div class="form-group">
                            <label for="backup_interval">Backup Interval</label>
                            <select class="form-control" id="backup_interval" name="backup_interval" required>
                                <option value="daily" {{ ($settings['backup_interval'] ?? 'daily') == 'daily' ? 'selected' : '' }}>Daily</option>
                                <option value="weekly" {{ ($settings['backup_interval'] ?? 'daily') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="monthly" {{ ($settings['backup_interval'] ?? 'daily') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            </select>
                        </div> --}}
                        
                        <div class="form-check mt-4">
                            <input type="checkbox" class="form-check-input" id="maintenance_mode" 
                                   name="maintenance_mode" value="1" 
                                   {{ ($settings['maintenance_mode'] ?? 0) ? 'checked' : '' }}>
                            <label class="form-check-label" for="maintenance_mode">Maintenance Mode</label>
                        </div>
                        
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="enable_registration" 
                                   name="enable_registration" value="1" 
                                   {{ ($settings['enable_registration'] ?? 1) ? 'checked' : '' }}>
                            <label class="form-check-label" for="enable_registration">Enable User Registration</label>
                        </div>
                        
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="enable_two_factor" 
                                   name="enable_two_factor" value="1" 
                                   {{ ($settings['enable_two_factor'] ?? 1) ? 'checked' : '' }}>
                            <label class="form-check-label" for="enable_two_factor">Enable Two-Factor Authentication</label>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary mt-3">Save Settings</button>
            </form>
        </div>
    </div>
</div>
@endsection