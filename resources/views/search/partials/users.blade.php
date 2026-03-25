{{-- resources/views/search/partials/users.blade.php --}}
<div class="list-group">
    @foreach ($users as $user)
        <a href="{{ route('users.show', $user) }}" class="list-group-item list-group-item-action">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    @if ($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="rounded-circle"
                            width="50" height="50">
                    @else
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px;">
                            <span class="text-white fs-5">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                    @endif
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">{{ $user->name }}</h6>
                            <p class="mb-1 text-muted">{{ $user->email }}</p>
                        </div>
                        <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <small class="text-muted">
                            <i class="bi bi-briefcase"></i> {{ $user->role->name }}
                        </small>
                        @if ($user->department)
                            <small class="text-muted">
                                <i class="bi bi-building"></i> {{ $user->department->name }}
                            </small>
                        @endif
                        @if ($user->phone)
                            <small class="text-muted">
                                <i class="bi bi-telephone"></i> {{ $user->phone }}
                            </small>
                        @endif
                        <small class="text-muted">
                            <i class="bi bi-calendar"></i> Joined {{ $user->created_at->format('M Y') }}
                        </small>
                    </div>
                </div>
            </div>
        </a>
    @endforeach
</div>
