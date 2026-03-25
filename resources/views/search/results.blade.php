{{-- resources/views/search/results.blade.php --}}
@extends('layouts.app')

@section('title', 'Search Results')

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Search Filters</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('search.advanced') }}" method="GET">
                        <input type="hidden" name="q" value="{{ $query }}">

                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select">
                                <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All</option>
                                <option value="files" {{ $type === 'files' ? 'selected' : '' }}>Files</option>
                                <option value="transfers" {{ $type === 'transfers' ? 'selected' : '' }}>Transfers</option>
                                <option value="users" {{ $type === 'users' ? 'selected' : '' }}>Users</option>
                                <option value="departments" {{ $type === 'departments' ? 'selected' : '' }}>Departments
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date From</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date To</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Apply Filters
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Search Results for "{{ $query }}"</h3>
                </div>
                <div class="card-body">
                    @if ($type === 'all')
                        <!-- Files Results -->
                        @if ($results['files']->isNotEmpty())
                            <h5 class="mb-3">
                                <i class="bi bi-files"></i> Files ({{ $results['files']->count() }})
                            </h5>
                            <div class="list-group mb-4">
                                @foreach ($results['files'] as $file)
                                    <a href="{{ route('files.show', $file) }}"
                                        class="list-group-item list-group-item-action">
                                        <div class="d-flex align-items-center">
                                            <i class="bi {{ $file->icon }} me-3 fs-3 text-primary"></i>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $file->name }}</h6>
                                                <small class="text-muted">
                                                    {{ $file->size_for_humans }} •
                                                    Uploaded {{ $file->created_at->diffForHumans() }} •
                                                    Owner: {{ $file->owner->name }}
                                                </small>
                                            </div>
                                            <span class="badge bg-info">{{ strtoupper($file->extension) }}</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        <!-- Transfers Results -->
                        @if ($results['transfers']->isNotEmpty())
                            <h5 class="mb-3">
                                <i class="bi bi-truck"></i> Transfers ({{ $results['transfers']->count() }})
                            </h5>
                            <div class="list-group mb-4">
                                @foreach ($results['transfers'] as $transfer)
                                    <a href="{{ route('transfers.show', $transfer) }}"
                                        class="list-group-item list-group-item-action">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-truck me-3 fs-3 text-success"></i>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $transfer->transfer_id }} - {{ $transfer->purpose }}
                                                </h6>
                                                <small class="text-muted">
                                                    From: {{ $transfer->sender->name }} •
                                                    To: {{ $transfer->receiver->name ?? $transfer->receiver_name }} •
                                                    Expected: {{ $transfer->expected_delivery_time->format('Y-m-d') }}
                                                </small>
                                            </div>
                                            <span
                                                class="badge bg-{{ $transfer->status === 'delivered' ? 'success' : ($transfer->status === 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($transfer->status) }}
                                            </span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        <!-- Users Results -->
                        @if ($results['users']->isNotEmpty())
                            <h5 class="mb-3">
                                <i class="bi bi-people"></i> Users ({{ $results['users']->count() }})
                            </h5>
                            <div class="list-group mb-4">
                                @foreach ($results['users'] as $user)
                                    <a href="{{ route('users.show', $user) }}"
                                        class="list-group-item list-group-item-action">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @if ($user->avatar)
                                                    <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}"
                                                        class="rounded-circle" width="40" height="40">
                                                @else
                                                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center"
                                                        style="width: 40px; height: 40px;">
                                                        <span
                                                            class="text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $user->name }}</h6>
                                                <small class="text-muted">{{ $user->email }} •
                                                    {{ $user->role->name }}</small>
                                            </div>
                                            <span
                                                class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($user->status) }}
                                            </span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        <!-- Departments Results -->
                        @if ($results['departments']->isNotEmpty())
                            <h5 class="mb-3">
                                <i class="bi bi-building"></i> Departments ({{ $results['departments']->count() }})
                            </h5>
                            <div class="list-group">
                                @foreach ($results['departments'] as $dept)
                                    <a href="{{ route('departments.show', $dept) }}"
                                        class="list-group-item list-group-item-action">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-building me-3 fs-3 text-warning"></i>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $dept->name }}</h6>
                                                <small class="text-muted">Code: {{ $dept->code }}</small>
                                            </div>
                                            <span
                                                class="badge bg-{{ $dept->status === 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($dept->status) }}
                                            </span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        @if (
                            $results['files']->isEmpty() &&
                                $results['transfers']->isEmpty() &&
                                $results['users']->isEmpty() &&
                                $results['departments']->isEmpty())
                            <div class="text-center py-5">
                                <i class="bi bi-search display-1 text-muted"></i>
                                <h5 class="mt-3">No Results Found</h5>
                                <p class="text-muted">No items match your search query "{{ $query }}"</p>
                            </div>
                        @endif
                    @else
                        <!-- Specific Type Results -->
                        @if ($type === 'files')
                            @include('search.partials.files', ['files' => $results])
                        @elseif($type === 'transfers')
                            @include('search.partials.transfers', ['transfers' => $results])
                        @elseif($type === 'users')
                            @include('search.partials.users', ['users' => $results])
                        @elseif($type === 'departments')
                            @include('search.partials.departments', ['departments' => $results])
                        @endif

                        @if ($results->isEmpty())
                            <div class="text-center py-5">
                                <i class="bi bi-search display-1 text-muted"></i>
                                <h5 class="mt-3">No {{ ucfirst($type) }} Found</h5>
                                <p class="text-muted">No {{ $type }} match your search query "{{ $query }}"
                                </p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
