{{-- resources/views/settings/notifications.blade.php --}}
@extends('layouts.app')

@section('title', 'Notification Settings')

@section('content')
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Notification Settings</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.notifications') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <h6 class="fw-bold">General Notifications</h6>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="email_notifications"
                                    name="email_notifications" value="1"
                                    {{ $settings['email_notifications'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_notifications">
                                    Email Notifications
                                </label>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="push_notifications"
                                    name="push_notifications" value="1"
                                    {{ $settings['push_notifications'] ?? false ? 'checked' : '' }}>
                                <label class="form-check-label" for="push_notifications">
                                    Push Notifications
                                </label>
                            </div>
                            <small class="text-muted">
                                Choose how you want to receive notifications
                            </small>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold">File Notifications</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="file_shared_notification"
                                    name="file_shared_notification" value="1"
                                    {{ $settings['file_shared_notification'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="file_shared_notification">
                                    When someone shares a file with me
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="file_accessed_notification"
                                    name="file_accessed_notification" value="1"
                                    {{ $settings['file_accessed_notification'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="file_accessed_notification">
                                    When my files are accessed
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="file_downloaded_notification"
                                    name="file_downloaded_notification" value="1"
                                    {{ $settings['file_downloaded_notification'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="file_downloaded_notification">
                                    When my files are downloaded
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold">Transfer Notifications</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="transfer_created_notification"
                                    name="transfer_created_notification" value="1"
                                    {{ $settings['transfer_created_notification'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="transfer_created_notification">
                                    When a transfer is created for me
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="transfer_delivered_notification"
                                    name="transfer_delivered_notification" value="1"
                                    {{ $settings['transfer_delivered_notification'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="transfer_delivered_notification">
                                    When a transfer is delivered
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="transfer_overdue_notification"
                                    name="transfer_overdue_notification" value="1"
                                    {{ $settings['transfer_overdue_notification'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="transfer_overdue_notification">
                                    When a transfer is overdue
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold">Security Notifications</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="login_notification"
                                    name="login_notification" value="1"
                                    {{ $settings['login_notification'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="login_notification">
                                    New login to my account
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="password_change_notification"
                                    name="password_change_notification" value="1"
                                    {{ $settings['password_change_notification'] ?? true ? 'checked' : '' }}>
                                <label class="form-check-label" for="password_change_notification">
                                    Password changes
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold">Notification Frequency</h6>
                            <select class="form-select" name="notification_frequency">
                                <option value="immediate"
                                    {{ ($settings['notification_frequency'] ?? 'immediate') == 'immediate' ? 'selected' : '' }}>
                                    Immediate
                                </option>
                                <option value="daily"
                                    {{ ($settings['notification_frequency'] ?? '') == 'daily' ? 'selected' : '' }}>
                                    Daily Digest
                                </option>
                                <option value="weekly"
                                    {{ ($settings['notification_frequency'] ?? '') == 'weekly' ? 'selected' : '' }}>
                                    Weekly Digest
                                </option>
                            </select>
                            <small class="text-muted">
                                Choose how often you want to receive notifications
                            </small>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
