{{-- resources/views/logs/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Log Details')

@section('content')
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Activity Log Details</h5>
                    <div class="card-tools">
                        <a href="{{ route('logs.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 120px;">ID</th>
                                    <td>{{ $log->id }}</td>
                                </tr>
                                <tr>
                                    <th>Date/Time</th>
                                    <td>{{ $log->created_at->format('F d, Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>User</th>
                                    <td>
                                        @if ($log->user)
                                            <a href="{{ route('users.show', $log->user) }}">
                                                {{ $log->user->name }}
                                            </a>
                                            <br>
                                            <small>{{ $log->user->email }}</small>
                                        @else
                                            System
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Action</th>
                                    <td>
                                        <span class="badge bg-{{ $log->action === 'login' ? 'success' : 'info' }}">
                                            {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Module</th>
                                    <td>{{ ucfirst($log->module) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 120px;">IP Address</th>
                                    <td>{{ $log->ip_address ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Device Type</th>
                                    <td>{{ ucfirst($log->device_type ?? 'N/A') }}</td>
                                </tr>
                                <tr>
                                    <th>Browser</th>
                                    <td>{{ $log->browser ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Platform</th>
                                    <td>{{ $log->platform ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Location</th>
                                    <td>{{ $log->location ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="mt-3">
                        <h6>Description</h6>
                        <div class="p-3 bg-light rounded">
                            {{ $log->description }}
                        </div>
                    </div>

                    @if ($log->old_data || $log->new_data)
                        <div class="mt-3">
                            <h6>Data Changes</h6>
                            <div class="row">
                                @if ($log->old_data)
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header bg-danger text-white">
                                                Old Data
                                            </div>
                                            <div class="card-body">
                                                <pre class="mb-0">{{ json_encode(json_decode($log->old_data), JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if ($log->new_data)
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header bg-success text-white">
                                                New Data
                                            </div>
                                            <div class="card-body">
                                                <pre class="mb-0">{{ json_encode(json_decode($log->new_data), JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if ($log->file || $log->transfer)
                        <div class="mt-3">
                            <h6>Related Items</h6>
                            <div class="list-group">
                                @if ($log->file)
                                    <a href="{{ route('files.show', $log->file) }}"
                                        class="list-group-item list-group-item-action">
                                        <i class="bi bi-file"></i> File: {{ $log->file->name }}
                                    </a>
                                @endif
                                @if ($log->transfer)
                                    <a href="{{ route('transfers.show', $log->transfer) }}"
                                        class="list-group-item list-group-item-action">
                                        <i class="bi bi-truck"></i> Transfer: {{ $log->transfer->transfer_id }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- User Agent Details -->
                    @if ($log->user_agent)
                        <div class="mt-3">
                            <h6>User Agent</h6>
                            <div class="p-3 bg-light rounded">
                                <small>{{ $log->user_agent }}</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
