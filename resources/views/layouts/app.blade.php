{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ config('app.name') }} - Professional Management System">

    <title>{{ config('app.name') }} - @yield('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


    <!-- Font Awesome as fallback -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Chart.js CSS (if needed) -->
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>

<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <!-- Logo Area -->
        <div class="sidebar-header">
            <a href="{{ route('dashboard') }}" class="logo-container">
                <div class="logo-wrapper">
                    @php
                        $logoPath = public_path('/assets/images/Sona-star-logo.png');
                        $logoExists = file_exists($logoPath);
                    @endphp

                    @if ($logoExists)
                        <img src="{{ asset('assets/images/Sona-star-logo.png') }}" alt="{{ config('app.name') }}"
                            class="logo-img" loading="lazy">
                    @else
                        <div class="logo-placeholder">
                            <span class="logo-text">{{ substr(config('app.name'), 0, 2) }}</span>
                        </div>
                    @endif
                </div>
                <div class="app-name-wrapper">
                    <h3 class="app-name">{{ config('app.name') }}</h3>
                </div>
            </a>
        </div>

        <nav class="sidebar-menu">
            @auth
                @php
                    $user = Auth::user();
                    $isSuperAdmin = $user->isSuperAdmin();

                    // Define permissions for each module
                    $permissions = [
                        'dashboard' => true, // Dashboard is always visible
                        'files' => $user->hasPermission('view-files') || $isSuperAdmin,
                        'transfers' => $user->hasPermission('view-transfers') || $isSuperAdmin,
                        'departments' => $user->hasPermission('view-departments') || $isSuperAdmin,
                        'users' => $user->hasPermission('view-users') || $isSuperAdmin,
                        'roles' => $user->hasPermission('view-roles') || $isSuperAdmin,
                        'logs' => $user->hasPermission('view-logs') || $isSuperAdmin,
                        'reports_files' => $user->hasPermission('view-file-reports') || $isSuperAdmin,
                        'reports_transfers' => $user->hasPermission('view-transfer-reports') || $isSuperAdmin,
                        'reports_users' => $user->hasPermission('view-user-reports') || $isSuperAdmin,
                        'profile' => true, // Profile is always visible
                        'settings' => $user->hasPermission('view-settings') || $isSuperAdmin,
                    ];

                    // Check if any report permission exists
                    $hasAnyReportPermission =
                        $permissions['reports_files'] ||
                        $permissions['reports_transfers'] ||
                        $permissions['reports_users'];
                @endphp

                <!-- Main Section -->
                <div class="menu-section">
                    @if ($permissions['dashboard'])
                        <div class="menu-item">
                            <a href="{{ route('dashboard') }}"
                                class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="bi bi-grid-1x2-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </div>
                    @endif

                    @if ($permissions['files'])
                        <div class="menu-item">
                            <a href="{{ route('files.index') }}"
                                class="menu-link {{ request()->routeIs('files.*') ? 'active' : '' }}">
                                <i class="bi bi-folder-fill"></i>
                                <span>Files</span>

                            </a>
                        </div>
                    @endif

                    @if ($permissions['transfers'])
                        <div class="menu-item">
                            <a href="{{ route('transfers.index') }}"
                                class="menu-link {{ request()->routeIs('transfers.*') ? 'active' : '' }}">
                                <i class="bi bi-arrow-left-right"></i>
                                <span>Transfers</span>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Management Section -->
                @if ($permissions['departments'] || $permissions['users'])
                    <div class="menu-section">
                        @if ($permissions['departments'])
                            <div class="menu-item">
                                <a href="{{ route('departments.index') }}"
                                    class="menu-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                                    <i class="bi bi-building"></i>
                                    <span>Departments</span>
                                </a>
                            </div>
                        @endif

                        @if ($permissions['users'])
                            <div class="menu-item">
                                <a href="{{ route('users.index') }}"
                                    class="menu-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                    <i class="bi bi-people-fill"></i>
                                    <span>Users</span>

                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Roles & Permissions -->
                @if ($permissions['roles'])
                    <div class="menu-section">
                        <div class="menu-item">
                            <a href="{{ route('roles.index') }}"
                                class="menu-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                <i class="bi bi-shield"></i>
                                <span>Roles & Permissions</span>
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Analytics & Reports Section -->
                <div class="menu-section">
                    @if ($permissions['logs'])
                        <div class="menu-item">
                            <a href="{{ route('logs.index') }}"
                                class="menu-link {{ request()->routeIs('logs.*') ? 'active' : '' }}">
                                <i class="bi bi-clock-history"></i>
                                <span>Activity Logs</span>
                            </a>
                        </div>
                    @endif

                    @if ($hasAnyReportPermission)
                        <div class="menu-item">
                            <a href="#reportsMenu" class="menu-link" data-bs-toggle="collapse" role="button"
                                aria-expanded="{{ request()->routeIs('reports.*') ? 'true' : 'false' }}">
                                <i class="bi bi-graph-up"></i>
                                <span>Reports</span>
                                <i class="bi bi-chevron-down ms-auto"></i>
                            </a>
                            <div class="collapse {{ request()->routeIs('reports.*') ? 'show' : '' }}" id="reportsMenu">
                                <div class="submenu">
                                    @if ($permissions['reports_files'])
                                        <a href="{{ route('reports.files') }}"
                                            class="submenu-link {{ request()->routeIs('reports.files') ? 'active' : '' }}">
                                            <i class="bi bi-file-text"></i>
                                            <span>File Reports</span>
                                        </a>
                                    @endif

                                    @if ($permissions['reports_transfers'])
                                        <a href="{{ route('reports.transfers') }}"
                                            class="submenu-link {{ request()->routeIs('reports.transfers') ? 'active' : '' }}">
                                            <i class="bi bi-truck"></i>
                                            <span>Transfer Reports</span>
                                        </a>
                                    @endif

                                    @if ($permissions['reports_users'])
                                        <a href="{{ route('reports.users') }}"
                                            class="submenu-link {{ request()->routeIs('reports.users') ? 'active' : '' }}">
                                            <i class="bi bi-people"></i>
                                            <span>User Reports</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Settings Section -->
                <div class="menu-section">
                    <div class="menu-item">
                        <a href="{{ route('profile.show') }}"
                            class="menu-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                            <i class="bi bi-person-circle"></i>
                            <span>Profile</span>
                        </a>
                    </div>

                    @if ($permissions['settings'])
                        <div class="menu-item">
                            <a href="{{ route('settings.security') }}"
                                class="menu-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                                <i class="bi bi-gear-fill"></i>
                                <span>Settings</span>
                            </a>
                        </div>
                    @endif
                </div>

                <div class="menu-divider"></div>

                <!-- Logout -->
                <div class="menu-item logout-item">
                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                        @csrf
                        <a href="#" class="menu-link"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </a>
                    </form>
                </div>
            @endauth
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <!-- Header -->
        <header class="header">
            <div class="header-left">
                <button class="toggle-sidebar" id="toggleSidebar" aria-label="Toggle sidebar">
                    <i class="bi bi-list"></i>
                </button>
            </div>

            <div class="header-right">
                <!-- Profile Dropdown -->
                @auth
                    <div class="dropdown">
                        <div class="profile-trigger" data-bs-toggle="dropdown" aria-expanded="false" role="button">
                            <div class="profile-avatar">
                                @if (Auth::user()->avatar)
                                    <img loading="lazy" src="{{ Storage::url(Auth::user()->avatar) }}"
                                        alt="{{ Auth::user()->name }}">
                                @else
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                @endif
                            </div>
                            <div class="profile-info">
                                <div class="name">{{ Auth::user()->name }}</div>
                                <div class="role">{{ Auth::user()->role->name ?? 'User' }}</div>
                            </div>
                            <i class="bi bi-chevron-down text-muted small"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end py-2">
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('profile.show') }}">
                                    <i class="bi bi-person me-2"></i> Profile
                                </a>
                            </li>

                            @if (Auth::user()->hasPermission('view-settings') || Auth::user()->isSuperAdmin())
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('settings.security') }}">
                                        <i class="bi bi-shield-lock me-2"></i> Security
                                    </a>
                                </li>
                            @endif

                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endauth
            </div>
        </header>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Breadcrumb -->
            <div class="mb-4">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        @yield('breadcrumb')
                    </ol>
                </nav>
            </div>

            <!-- Page Content -->
            @yield('content')
        </div>
    </main>

    <!-- Core Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Your JS -->
    <script src="{{ asset('assets/js/app.js') }}"></script>

    <!-- SESSION TOAST ALERT (TOP RIGHT) -->
    @if (session('success'))
        <script>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: "{{ session('error') }}",
                showConfirmButton: false,
                timer: 3000
            });
        </script>
    @endif
</body>

</html>
