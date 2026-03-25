@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
    <div class="container-fluid">

        <!-- Back Button -->
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-end">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Statistics -->
        <div class="row mb-3">

            <div class="col-md-3 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-info">
                        <i class="fas fa-history"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Logs</span>
                        <span class="info-box-number">{{ number_format($logs->total()) }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-success">
                        <i class="fas fa-users"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Active Users</span>
                        <span class="info-box-number">{{ $users->count() }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-warning">
                        <i class="fas fa-file-alt"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">File Activities</span>
                        <span class="info-box-number">
                            {{ \App\Models\ActivityLog::whereIn('action', ['file_upload', 'file_download', 'file_view', 'file_share'])->count() }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Security Events</span>
                        <span class="info-box-number">
                            {{ \App\Models\ActivityLog::whereIn('action', ['login_failed', 'otp_failed', 'unauthorized_access'])->count() }}
                        </span>
                    </div>
                </div>
            </div>

        </div>


        <!-- Logs Table -->
        <div class="row">
            <div class="col-12">

                <div class="card">

                    <div class="card-header">
                        <h3 class="card-title">Activity Logs</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>


                    <div class="card-body">

                        <!-- Filters -->
                        <form method="GET" action="{{ route('admin.audit-logs') }}" class="mb-4">
                            <div class="row">

                                <div class="col-md-3">
                                    <label>User</label>
                                    <select name="user_id" class="form-control">
                                        <option value="">All Users</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label>Action</label>
                                    <select name="action" class="form-control">
                                        <option value="">All Actions</option>
                                        @foreach ($actions as $action)
                                            <option value="{{ $action }}"
                                                {{ request('action') == $action ? 'selected' : '' }}>
                                                {{ ucfirst(str_replace('_', ' ', $action)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label>Date From</label>
                                    <input type="date" name="date_from" class="form-control"
                                        value="{{ request('date_from') }}">
                                </div>

                                <div class="col-md-2">
                                    <label>Date To</label>
                                    <input type="date" name="date_to" class="form-control"
                                        value="{{ request('date_to') }}">
                                </div>

                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary mr-2">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>

                                    <a href="{{ route('admin.audit-logs') }}" class="btn btn-secondary">
                                        <i class="fas fa-redo"></i>
                                    </a>
                                </div>

                            </div>
                        </form>


                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-hover datatable">

                                <thead>
                                    <tr>
                                        <th>Timestamp</th>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Description</th>
                                        <th>Resource</th>
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
                                                @if ($log->user)
                                                    <div class="d-flex align-items-center">

                                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($log->user->name) }}&background=0D8ABC&color=fff&size=24"
                                                            class="img-circle mr-2" width="24">

                                                        <div>
                                                            <strong>{{ $log->user->name }}</strong><br>
                                                            <small class="text-muted">
                                                                {{ $log->user->role->name }}
                                                            </small>
                                                        </div>

                                                    </div>
                                                @else
                                                    <span class="text-muted">System</span>
                                                @endif
                                            </td>

                                            <td>
                                                <span class="badge badge-{{ getActionBadgeColor($log->action) }}">
                                                    {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                                </span>
                                            </td>

                                            <td>{{ $log->description }}</td>

                                            <td>

                                                @if ($log->file)
                                                    <a href="{{ route('files.show', $log->file) }}" class="text-primary">
                                                        <i class="fas fa-file"></i>
                                                        {{ Str::limit($log->file->original_name, 20) }}
                                                    </a>
                                                @elseif($log->transfer)
                                                    <a href="{{ route('transfers.show', $log->transfer) }}"
                                                        class="text-info">
                                                        <i class="fas fa-paper-plane"></i>
                                                        Transfer {{ substr($log->transfer->transfer_uuid, 0, 8) }}...
                                                    </a>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif

                                            </td>

                                            <td>
                                                <code>{{ $log->ip_address }}</code>
                                            </td>

                                            <td>
                                                <small class="text-muted">
                                                    {{ $log->location ?? 'Unknown' }}
                                                </small>
                                            </td>

                                        </tr>

                                    @empty

                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                                <h5>No audit logs found</h5>
                                                <p class="text-muted">Try changing your filter criteria</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>

                    </div>


                    <div class="card-footer">
                        <small class="text-muted">
                            Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }}
                            of {{ $logs->total() }} records
                        </small>
                    </div>

                </div>

            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            function exportLogs() {
                // Collect filter parameters
                const params = new URLSearchParams({
                    user_id: document.querySelector('select[name="user_id"]').value,
                    action: document.querySelector('select[name="action"]').value,
                    date_from: document.querySelector('input[name="date_from"]').value,
                    date_to: document.querySelector('input[name="date_to"]').value,
                    export: 'csv'
                });

                // Create download URL
                const url = '{{ route('admin.audit-logs') }}?' + params.toString();
                window.open(url, '_blank');
            }
        </script>
    @endpush
@endsection
