{{-- resources/views/logs/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Activity Logs</h3>
                    <div class="card-tools">
                        <div class="btn-group">
                            <a href="{{ route('logs.stats') }}" class="btn btn-info btn-sm">
                                <i class="bi bi-graph-up"></i> Statistics
                            </a>
                            <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-download"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('logs.export', ['format' => 'excel']) }}">
                                        <i class="bi bi-file-excel"></i> Excel
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logs.export', ['format' => 'pdf']) }}">
                                        <i class="bi bi-file-pdf"></i> PDF
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" action="{{ route('logs.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-2">
                                <input type="text" name="search" class="form-control" placeholder="Search..."
                                    value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="action" class="form-select">
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
                                <select name="module" class="form-select">
                                    <option value="">All Modules</option>
                                    @foreach ($modules as $module)
                                        <option value="{{ $module }}"
                                            {{ request('module') == $module ? 'selected' : '' }}>
                                            {{ ucfirst($module) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @if (Auth::user()->isSuperAdmin())
                                <div class="col-md-2">
                                    <select name="user_id" class="form-select">
                                        <option value="">All Users</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="col-md-2">
                                <input type="date" name="date_from" class="form-control" placeholder="From"
                                    value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="date_to" class="form-control" placeholder="To"
                                    value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-12 mt-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-filter"></i> Filter
                                </button>
                                <a href="{{ route('logs.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-eraser"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Logs Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th>Date/Time</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Module</th>
                                    <th>Description</th>
                                    <th>IP Address</th>
                                    <th>Device</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td>
                                            <a href="{{ route('users.show', $log->user) }}">
                                                {{ $log->user->name ?? 'System' }}
                                            </a>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $log->action === 'login' ? 'success' : ($log->action === 'logout' ? 'secondary' : 'info') }}">
                                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                            </span>
                                        </td>
                                        <td>{{ ucfirst($log->module) }}</td>
                                        <td>{{ $log->description }}</td>
                                        <td>{{ $log->ip_address ?? 'N/A' }}</td>
                                        <td>
                                            @if ($log->device_type)
                                                <i
                                                    class="bi bi-{{ $log->device_type === 'mobile' ? 'phone' : ($log->device_type === 'tablet' ? 'tablet' : 'laptop') }}"></i>
                                                {{ ucfirst($log->device_type) }}
                                                @if ($log->browser)
                                                    <br><small>{{ $log->browser }}</small>
                                                @endif
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('logs.show', $log) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No logs found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="row mt-3">
                        <div class="col-sm-12 col-md-5">
                            <div class="dataTables_info">
                                Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }}
                                of {{ $logs->total() }} entries
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-7">
                            <div class="dataTables_paginate">
                                {{ $logs->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.datatable').DataTable({
                paging: false,
                searching: false,
                ordering: true,
                info: false
            });
        });
    </script>
@endpush
