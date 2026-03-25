{{-- resources/views/partials/notifications-dropdown.blade.php --}}
<li class="nav-item dropdown">
    <a class="nav-link" data-bs-toggle="dropdown" href="#">
        <i class="bi bi-bell"></i>
        @if ($unreadNotifications > 0)
            <span class="badge badge-danger navbar-badge">{{ $unreadNotifications }}</span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
        <span class="dropdown-item dropdown-header">{{ $unreadNotifications }} Unread Notifications</span>

        <div class="dropdown-divider"></div>

        @forelse($recentNotifications as $notification)
            <a href="{{ $notification['link'] ?? '#' }}" class="dropdown-item">
                <i class="bi bi-{{ $notification['icon'] }} me-2"></i>
                {{ $notification['message'] }}
                <span class="float-end text-muted text-sm">{{ $notification['time'] }}</span>
            </a>
            <div class="dropdown-divider"></div>
        @empty
            <span class="dropdown-item text-center text-muted">
                <i class="bi bi-bell-slash"></i> No notifications
            </span>
        @endforelse

        <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
    </div>
</li>
