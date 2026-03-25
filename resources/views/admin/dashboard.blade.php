@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

    <div class="container-fluid">

        <!-- ================= INFO BOXES ================= -->
        <div class="row mb-4">

            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-info elevation-1">
                        <i class="fas fa-file"></i>
                    </span>

                    <div class="info-box-content">
                        <span class="info-box-text">Total Files</span>
                        <span class="info-box-number">{{ $stats['total_files'] }}</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-success elevation-1">
                        <i class="fas fa-users"></i>
                    </span>

                    <div class="info-box-content">
                        <span class="info-box-text">Total Users</span>
                        <span class="info-box-number">{{ $stats['total_users'] }}</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-warning elevation-1">
                        <i class="fas fa-paper-plane"></i>
                    </span>

                    <div class="info-box-content">
                        <span class="info-box-text">Active Transfers</span>
                        <span class="info-box-number">{{ $stats['active_transfers'] }}</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-danger elevation-1">
                        <i class="fas fa-shield-alt"></i>
                    </span>

                    <div class="info-box-content">
                        <span class="info-box-text">OTP Approvals Today</span>
                        <span class="info-box-number">{{ $stats['otp_approvals_today'] }}</span>
                    </div>
                </div>
            </div>

        </div>


        <!-- ================= MAIN ROW ================= -->
        <div class="row">

            <!-- LEFT SIDE -->
            <div class="col-lg-8">

                <!-- Recent Activity -->
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-history mr-1"></i> Recent Activity
                        </h3>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-hover">

                                <thead class="bg-light">
                                    <tr>
                                        <th>Time</th>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Description</th>
                                        <th>IP Address</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @foreach ($recentActivity as $activity)
                                        <tr>

                                            <td>
                                                {{ $activity->performed_at->diffForHumans() }}
                                            </td>

                                            <td>
                                                {{ $activity->user->name ?? 'System' }}
                                            </td>

                                            <td>
                                                <span
                                                    class="badge badge-
                                        {{ $activity->action == 'login' ? 'success' : ($activity->action == 'logout' ? 'danger' : 'info') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $activity->action)) }}
                                                </span>
                                            </td>

                                            <td>
                                                {{ Str::limit($activity->description, 50) }}
                                            </td>

                                            <td>
                                                <small class="text-muted">
                                                    {{ $activity->ip_address }}
                                                </small>
                                            </td>

                                        </tr>
                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    </div>
                </div>

            </div>


            <!-- RIGHT SIDE -->
            <div class="col-lg-4">

                <!-- Recent Files -->
                <div class="card shadow-sm">

                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file mr-1"></i> Recent Files
                        </h3>
                    </div>

                    <div class="card-body p-0">

                        <ul class="products-list product-list-in-card pl-2 pr-2">

                            @foreach ($recentFiles as $file)
                                <li class="item">

                                    <div class="product-img">

                                        @if ($file->extension === 'pdf')
                                            <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                        @elseif(in_array($file->extension, ['doc', 'docx']))
                                            <i class="fas fa-file-word fa-2x text-primary"></i>
                                        @elseif(in_array($file->extension, ['jpg', 'jpeg', 'png', 'gif']))
                                            <i class="fas fa-file-image fa-2x text-success"></i>
                                        @else
                                            <i class="fas fa-file fa-2x text-secondary"></i>
                                        @endif

                                    </div>

                                    <div class="product-info">

                                        <a href="{{ route('files.show', $file) }}" class="product-title">

                                            {{ Str::limit($file->original_name, 30) }}

                                            <span
                                                class="badge badge-
                                    {{ $file->is_archived ? 'secondary' : 'success' }} float-right">

                                                {{ $file->formatted_size }}

                                            </span>

                                        </a>

                                        <span class="product-description">

                                            {{ $file->owner->name }} • {{ $file->department->name }}

                                        </span>

                                    </div>

                                </li>
                            @endforeach

                        </ul>

                    </div>

                </div>


                <!-- Quick Links -->
                <div class="card mt-3 shadow-sm">

                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-link mr-1"></i> Quick Links
                        </h3>
                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-6">
                                <a href="{{ route('admin.users') }}" class="btn btn-primary btn-block mb-2">
                                    <i class="fas fa-users"></i> Users
                                </a>
                            </div>

                            <div class="col-6">
                                <a href="{{ route('admin.departments') }}" class="btn btn-info btn-block mb-2">
                                    <i class="fas fa-building"></i> Departments
                                </a>
                            </div>

                            <div class="col-6">
                                <a href="{{ route('admin.audit-logs') }}" class="btn btn-warning btn-block mb-2">
                                    <i class="fas fa-history"></i> Audit Logs
                                </a>
                            </div>

                            <div class="col-6">
                                <a href="{{ route('admin.stats') }}" class="btn btn-success btn-block mb-2">
                                    <i class="fas fa-chart-bar"></i> Statistics
                                </a>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
