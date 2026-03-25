@extends('layouts.app')

@section('title', 'Notification Details')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex justify-content-end align-items-center">

                    <div class="mb-0">
                        <a href="{{ route('notifications.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Notifications
                        </a>
                    </div>

                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            @switch($notification->type)
                                @case('file_shared')
                                    <i class="fas fa-share-alt text-primary"></i>
                                @break

                                @case('transfer_created')
                                    <i class="fas fa-paper-plane text-info"></i>
                                @break

                                @case('otp_sent')
                                    <i class="fas fa-shield-alt text-success"></i>
                                @break

                                @case('security_alert')
                                    <i class="fas fa-exclamation-triangle text-danger"></i>
                                @break

                                @default
                                    <i class="fas fa-bell text-secondary"></i>
                            @endswitch
                            {{ $notification->title }}
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-{{ $notification->is_read ? 'success' : 'warning' }}">
                                {{ $notification->is_read ? 'Read' : 'Unread' }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Notification Content -->
                        <div class="mb-4">
                            <h5>Message</h5>
                            <div class="alert alert-info">
                                {{ $notification->message }}
                            </div>
                        </div>

                        <!-- Notification Data -->
                        @if ($notification->data)
                            @php
                                $data = json_decode($notification->data, true);
                            @endphp
                            <div class="mb-4">
                                <h5>Details</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <tbody>
                                            @foreach ($data as $key => $value)
                                                <tr>
                                                    <th width="30%">{{ ucwords(str_replace('_', ' ', $key)) }}</th>
                                                    <td>
                                                        @if (is_array($value))
                                                            <pre class="mb-0">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                        @else
                                                            {{ $value }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        <!-- Timestamps -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-clock"></i> Timestamps
                                        </h6>
                                        <table class="table table-sm">
                                            <tr>
                                                <th>Created:</th>
                                                <td>{{ $notification->created_at->format('Y-m-d H:i:s') }}</td>
                                            </tr>
                                            @if ($notification->read_at)
                                                <tr>
                                                    <th>Read:</th>
                                                    <td>{{ $notification->read_at->format('Y-m-d H:i:s') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Time to Read:</th>
                                                    <td>{{ $notification->created_at->diffForHumans($notification->read_at, true) }}
                                                    </td>
                                                </tr>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-info-circle"></i> Information
                                        </h6>
                                        <table class="table table-sm">
                                            <tr>
                                                <th>Type:</th>
                                                <td>
                                                    <span class="badge badge-info">
                                                        {{ ucwords(str_replace('_', ' ', $notification->type)) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Status:</th>
                                                <td>
                                                    @if ($notification->is_read)
                                                        <span class="badge badge-success">Read</span>
                                                    @else
                                                        <span class="badge badge-warning">Unread</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Notification ID:</th>
                                                <td><small class="text-muted">{{ $notification->id }}</small></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            @if (!$notification->is_read)
                                <form action="{{ route('notifications.mark-as-read', $notification) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check"></i> Mark as Read
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('notifications.clear-all') }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Are you sure you want to delete this notification?')">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="notification_id" value="{{ $notification->id }}">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Delete Notification
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
