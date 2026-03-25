@extends('layouts.app')

@section('title', 'Department Admin Dashboard')

@section('content')

    {{-- ================= SAFE STATS ================= --}}
    <div class="row g-4 mb-4">

        <div class="col-xl-3 col-md-6">
            <div class="stat-card nav-card" data-url="{{ route('users.index') }}">
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_users']) }}</h3>
                    <p>Department Users</p>
                </div>
                <div class="stat-icon primary">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ number_format($stats['active_users']) }}</h3>
                    <p>Active Users</p>
                </div>
                <div class="stat-icon success">
                    <i class="bi bi-person-check"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card nav-card" data-url="{{ route('files.index') }}">
                <div class="stat-info">
                    <h3>{{ number_format($stats['total_files']) }}</h3>
                    <p>Department Files</p>
                </div>
                <div class="stat-icon warning">
                    <i class="bi bi-files"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card nav-card" data-url="{{ route('transfers.index', ['status' => 'pending']) }}">
                <div class="stat-info">
                    <h3>{{ number_format($stats['pending_transfers']) }}</h3>
                    <p>Pending Transfers</p>
                </div>
                <div class="stat-icon danger">
                    <i class="bi bi-truck"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- ================= STORAGE ================= --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Department Storage Usage</h5>
                </div>
                <div class="card-body">
                    @php
                        $storageGB = $stats['storage_used'] / 1073741824;
                        $storagePercentage = min(($stats['storage_used'] / (1073741824 * 10)) * 100, 100);
                    @endphp
                    <div class="progress" style="height:25px;">
                        <div class="progress-bar bg-primary" style="width: {{ $storagePercentage }}%">
                            {{ number_format($storageGB, 2) }} GB
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= CONTENT ================= --}}
    <div class="row g-4">

        {{-- Recent Files --}}
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h5>Recent Files</h5>
                </div>
                <div class="card-body">
                    @forelse($recentFiles as $file)
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <span>{{ $file->name }}</span>
                            <small>{{ $file->created_at->diffForHumans() }}</small>
                        </div>
                    @empty
                        <p>No files</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Transfers --}}
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h5>Pending Transfers</h5>
                </div>
                <div class="card-body">
                    @forelse($pendingTransfers as $t)
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <span>{{ $t->transfer_id }}</span>
                            <small>{{ $t->expected_delivery_time }}</small>
                        </div>
                    @empty
                        <p>No transfers</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

@endsection


{{-- ================= STYLE ================= --}}
@push('styles')
    <style>
        .stat-card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            position: relative;
            cursor: pointer;
            transition: 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 2rem;
            opacity: .2;
        }
    </style>
@endpush


{{-- ================= SCRIPT ================= --}}
@push('scripts')
    <script>
        // 🔥 STOP GLOBAL CLICK BUG
        document.addEventListener('click', function(e) {
            if (e.target.closest('.nav-card')) {
                e.stopImmediatePropagation();
            }
        }, true);

        // ✅ SAFE NAVIGATION
        document.querySelectorAll('.nav-card').forEach(card => {
            card.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                window.location.href = this.dataset.url;
            });
        });
    </script>
@endpush
