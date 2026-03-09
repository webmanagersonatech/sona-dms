@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0">
                                    <i class="fas fa-history"></i>
                                    Activity Logs
                                </h4>
                                <p class="text-muted mb-0">
                                    Track all your activities in the system
                                </p>
                            </div>
                            <div>
                                <a href="{{ route('profile.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Activity History</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <form method="GET" action="{{ route('profile.activity-logs') }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Action Type</label>
                                        <select name="action" class="form-control">
                                            <option value="">All Actions</option>
                                            @foreach (['login', 'logout', 'file_upload', 'file_download', 'file_view', 'transfer_create', 'transfer_send', 'transfer_receive'] as $action)
                                                <option value="{{ $action }}"
                                                    {{ request('action') == $action ? 'selected' : '' }}>
                                                    {{ ucfirst(str_replace('_', ' ', $action)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Date From</label>
                                        <input type="date" name="date_from" class="form-control"
                                            value="{{ request('date_from') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Date To</label>
                                        <input type="date" name="date_to" class="form-control"
                                            value="{{ request('date_to') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div class="d-flex">
                                            <button type="submit" class="btn btn-primary mr-2">
                                                <i class="fas fa-filter"></i> Filter
                                            </button>
                                            <a href="{{ route('profile.activity-logs') }}" class="btn btn-secondary">
                                                <i class="fas fa-redo"></i> Reset
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Activity Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Action</th>
                                        <th>Description</th>
                                        <th>File</th>
                                        <th>IP Address</th>
                                        <th>Location</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $log)
                                        <tr>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $log->performed_at->format('Y-m-d') }}<br>
                                                    {{ $log->performed_at->format('H:i:s') }}
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ getActionBadgeColor($log->action) }}">
                                                    {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                                </span>
                                            </td>

                                            <td>
                                                {{ $log->description }}
                                                @if ($log->transfer)
                                                    <br>
                                                    <small class="text-muted">
                                                        Transfer ID: {{ substr($log->transfer->transfer_uuid, 0, 8) }}...
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($log->file)
                                                    <a href="{{ route('files.show', $log->file) }}" class="text-primary">
                                                        <i class="fas fa-file"></i>
                                                        {{ Str::limit($log->file->original_name, 20) }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <code>{{ $log->ip_address }}</code>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $log->location ?? 'Unknown' }}</small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                                <h5>No activity logs found</h5>
                                                <p class="text-muted">Your activity history will appear here</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>


                        <!-- Statistics -->
                        <div class="row mt-4">
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info">
                                        <i class="fas fa-history"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Activities</span>
                                        <span class="info-box-number">{{ $logs->total() }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success">
                                        <i class="fas fa-file-upload"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">File Uploads</span>
                                        <span class="info-box-number">
                                            {{ auth()->user()->activityLogs()->where('action', 'file_upload')->count() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-primary">
                                        <i class="fas fa-download"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">File Downloads</span>
                                        <span class="info-box-number">
                                            {{ auth()->user()->activityLogs()->where('action', 'file_download')->count() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning">
                                        <i class="fas fa-paper-plane"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Transfers</span>
                                        <span class="info-box-number">
                                            {{ auth()->user()->activityLogs()->where('action', 'like', 'transfer%')->count() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
