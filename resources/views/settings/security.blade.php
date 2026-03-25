{{-- resources/views/settings/security.blade.php --}}
@extends('layouts.app')

@section('title', 'Security Settings')

@section('content')
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Security Settings</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.security') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <h6 class="fw-bold">Two-Factor Authentication</h6>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="two_factor_enabled"
                                    name="two_factor_enabled" value="1"
                                    {{ $user->settings['two_factor_enabled'] ?? false ? 'checked' : '' }}>
                                <label class="form-check-label" for="two_factor_enabled">
                                    Enable Two-Factor Authentication
                                </label>
                            </div>
                            <small class="text-muted">
                                When enabled, you'll need to enter an OTP code sent to your email for sensitive actions.
                            </small>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold">Session Timeout</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <select class="form-select" name="session_timeout">
                                        <option value="15"
                                            {{ ($user->settings['session_timeout'] ?? 30) == 15 ? 'selected' : '' }}>15
                                            minutes</option>
                                        <option value="30"
                                            {{ ($user->settings['session_timeout'] ?? 30) == 30 ? 'selected' : '' }}>30
                                            minutes</option>
                                        <option value="60"
                                            {{ ($user->settings['session_timeout'] ?? 30) == 60 ? 'selected' : '' }}>1 hour
                                        </option>
                                        <option value="120"
                                            {{ ($user->settings['session_timeout'] ?? 30) == 120 ? 'selected' : '' }}>2
                                            hours</option>
                                        <option value="240"
                                            {{ ($user->settings['session_timeout'] ?? 30) == 240 ? 'selected' : '' }}>4
                                            hours</option>
                                        <option value="480"
                                            {{ ($user->settings['session_timeout'] ?? 30) == 480 ? 'selected' : '' }}>8
                                            hours</option>
                                    </select>
                                </div>
                            </div>
                            <small class="text-muted">
                                Automatically log out after period of inactivity.
                            </small>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold">Login Notifications</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="email_on_login" name="email_on_login"
                                    value="1" {{ $user->settings['email_on_login'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_on_login">
                                    Send email notification on new login
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="email_on_device" name="email_on_device"
                                    value="1" {{ $user->settings['email_on_device'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_on_device">
                                    Send alert when logging in from new device
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold">File Access</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="require_otp_download"
                                    name="require_otp_download" value="1"
                                    {{ $user->settings['require_otp_download'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="require_otp_download">
                                    Require OTP approval for file downloads
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="notify_file_access"
                                    name="notify_file_access" value="1"
                                    {{ $user->settings['notify_file_access'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="notify_file_access">
                                    Notify me when my files are accessed
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Settings
                        </button>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Active Sessions</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Device</th>
                                    <th>IP Address</th>
                                    <th>Last Activity</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sessions as $session)
                                    <tr>
                                        <td>
                                            @php
                                                $agent = new Jenssegers\Agent\Agent();
                                                $agent->setUserAgent($session->user_agent);
                                            @endphp
                                            <i
                                                class="bi bi-{{ $agent->isMobile() ? 'phone' : ($agent->isTablet() ? 'tablet' : 'laptop') }} me-2"></i>
                                            {{ $agent->browser() }} on {{ $agent->platform() }}
                                        </td>
                                        <td>{{ $session->ip_address }}</td>
                                        <td>{{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}
                                        </td>
                                        <td>
                                            @if (session()->getId() !== $session->id)
                                                <form method="POST"
                                                    action="{{ route('settings.sessions.revoke', $session->id) }}"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Revoke this session?')">
                                                        Revoke
                                                    </button>
                                                </form>
                                            @else
                                                <span class="badge bg-success">Current</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Security Logs</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach ($user->activityLogs()->whereIn('action', ['login', 'logout', 'change_password'])->latest()->take(5)->get() as $log)
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ ucfirst($log->action) }}</h6>
                                    <small>{{ $log->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">{{ $log->description }}</p>
                                <small class="text-muted">IP: {{ $log->ip_address }}</small>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('logs.index') }}" class="btn btn-sm btn-primary">View All Logs</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
