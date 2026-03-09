<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sona DMS – Enterprise Document Management | secure, scalable, corporate-grade">
    <meta name="author" content="Sona DMS">

    <title>@yield('title', 'Sona DMS · Enterprise Document Management')</title>

    <!-- ===== core CSS ===== -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- DataTables & extensions -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.6.2/css/select.bootstrap4.min.css">
    <!-- sweetalert & animate -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Google Font: Inter (corporate standard) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300..700&display=swap" rel="stylesheet">

    <style>
        /* ----- corporate design tokens (refined) ----- */
        :root {
            --primary-navy: #0b2b4f;
            --secondary-slate: #1e3a5f;
            --sidebar-bg-start: #132b47;
            --sidebar-bg-end: #0a1e32;
            --sidebar-hover: #25496b;
            --sidebar-active: #1f6eaf;
            --accent-blue: #2b7fc2;
            --accent-teal: #3a9bd5;
            --success-emerald: #1f8b4c;
            --danger-coral: #d95555;
            --warning-gold: #e68a2e;
            --info-sky: #4aa3cf;
            --light-neutral: #f4f7fb;
            --border-subtle: #e1e8f0;
            --text-dark: #1b2b40;
            --text-medium: #3e526b;
            --text-soft: #617e9c;
            --shadow-elevation: 0 8px 20px -6px rgba(0, 32, 64, 0.12);
            --font-sans: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;

            /* sidebar dimensions – fluid */
            --sidebar-width: 280px;
            --sidebar-collapsed: 80px;
            --header-height: 70px;
            --transition-default: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: var(--font-sans);
            background: var(--light-neutral);
            color: var(--text-dark);
            font-size: 0.9375rem;
            line-height: 1.6;
            font-weight: 400;
        }

        /* ----- SIDEBAR (wider / premium) ----- */
        .main-sidebar {
            width: var(--sidebar-width) !important;
            background: linear-gradient(180deg, var(--sidebar-bg-start), var(--sidebar-bg-end));
            box-shadow: 6px 0 24px rgba(0, 20, 40, 0.2);
            transition: var(--transition-default);
        }

        .content-wrapper,
        .main-footer {
            margin-left: var(--sidebar-width) !important;
            transition: var(--transition-default);
        }

        /* mini / collapsed */
        .sidebar-mini.sidebar-collapse .main-sidebar {
            width: var(--sidebar-collapsed) !important;
        }

        .sidebar-mini.sidebar-collapse .content-wrapper,
        .sidebar-mini.sidebar-collapse .main-footer {
            margin-left: var(--sidebar-collapsed) !important;
        }

        .brand-link {
            background: rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            padding: 1.2rem 1.5rem;
            height: var(--header-height);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .brand-image {
            width: 40px;
            height: 40px;
            object-fit: contain;
            filter: brightness(1.1);
        }

        .brand-text {
            color: #fff;
            font-weight: 600;
            font-size: 1.3rem;
            letter-spacing: -0.01em;
            white-space: nowrap;
        }

        /* user panel */
        .user-panel {
            padding: 1.8rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            margin-bottom: 1.2rem;
        }

        .user-panel .info a {
            color: #fff;
            font-weight: 600;
            font-size: 1.05rem;
        }

        .user-panel .info small {
            color: #b0c8e0;
        }

        /* nav menu */
        .nav-sidebar {
            padding: 0.5rem 0;
        }

        .nav-sidebar .nav-item {
            margin: 0.2rem 1rem;
        }

        .nav-sidebar .nav-link {
            color: #cbdbe9;
            padding: 0.85rem 1.2rem;
            border-radius: 12px;
            font-weight: 500;
            font-size: 0.95rem;
            transition: var(--transition-default);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .nav-sidebar .nav-link:hover {
            background: var(--sidebar-hover);
            color: #fff;
            transform: translateX(6px);
        }

        .nav-sidebar .nav-link.active {
            background: var(--sidebar-active);
            color: white;
            box-shadow: 0 10px 16px -8px rgba(30, 110, 180, 0.6);
        }

        .nav-sidebar .nav-icon {
            font-size: 1.3rem;
            width: 28px;
            text-align: center;
        }

        .nav-header {
            color: #9bb5d0;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            padding: 1.8rem 1.8rem 0.4rem;
            opacity: 0.7;
        }

        /* treeview */
        .nav-treeview {
            background: rgba(0, 0, 0, 0.25);
            border-radius: 14px;
            margin: 0.3rem 0 0.3rem 0;
        }

        .nav-treeview .nav-link {
            padding: 0.7rem 1.5rem 0.7rem 3.2rem;
            font-size: 0.88rem;
        }

        /* collapsed adjustments */
        .sidebar-mini.sidebar-collapse .brand-text,
        .sidebar-mini.sidebar-collapse .user-panel .info,
        .sidebar-mini.sidebar-collapse .nav-header,
        .sidebar-mini.sidebar-collapse .nav-link p {
            display: none;
        }

        .sidebar-mini.sidebar-collapse .nav-link {
            justify-content: center;
            padding: 1rem 0;
        }

        .sidebar-mini.sidebar-collapse .nav-icon {
            margin: 0;
            font-size: 1.4rem;
        }

        .sidebar-mini.sidebar-collapse .brand-link {
            justify-content: center;
            padding: 1.2rem 0;
        }

        /* ----- HEADER (navbar) ----- */
        .main-header.navbar {
            background: #ffffff;
            border-bottom: 1px solid var(--border-subtle);
            height: var(--header-height);
            padding: 0 2rem;
            box-shadow: 0 4px 10px -8px rgba(0, 0, 0, 0.05);
        }

        .main-header .nav-link {
            color: var(--text-medium) !important;
            font-weight: 500;
            border-radius: 10px;
            padding: 0.7rem 1rem !important;
        }

        .main-header .nav-link:hover {
            color: var(--accent-blue) !important;
            background: rgba(43, 127, 194, 0.04);
        }

        .navbar-badge {
            font-size: 0.6rem;
            padding: 2px 6px;
            top: 6px;
            right: 2px;
        }

        /* ----- content area & breadcrumb ----- */
        .content-header {
            background: white;
            padding: 1.6rem 2.2rem;
            border-bottom: 1px solid var(--border-subtle);
            margin-bottom: 1.8rem;
            box-shadow: var(--shadow-elevation);
        }

        .content-header h1 {
            font-weight: 600;
            font-size: 1.9rem;
            color: var(--primary-navy);
            letter-spacing: -0.02em;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
            font-size: 0.9rem;
        }

        .breadcrumb-item a {
            color: var(--accent-blue);
            font-weight: 500;
        }

        .content {
            padding: 0 2.2rem 2.2rem;
        }

        /* ----- CARDS (elevated) ----- */
        .card {
            border: none;
            border-radius: 1.2rem;
            box-shadow: var(--shadow-elevation);
            transition: transform 0.2s, box-shadow 0.2s;
            background: white;
        }

        .card:hover {
            box-shadow: 0 18px 30px -12px rgba(0, 52, 102, 0.18);
        }

        .card-header {
            background: white;
            border-bottom: 1px solid var(--border-subtle);
            padding: 1.2rem 1.8rem;
            font-weight: 600;
            color: var(--primary-navy);
            font-size: 1.15rem;
            border-radius: 1.2rem 1.2rem 0 0;
        }

        .card-body {
            padding: 1.8rem;
        }

        .card-footer {
            background: white;
            border-top: 1px solid var(--border-subtle);
            padding: 1rem 1.8rem;
        }

        /* ----- BUTTONS (professional / interactive) ----- */
        .btn {
            border-radius: 10px;
            font-weight: 500;
            padding: 0.65rem 1.4rem;
            font-size: 0.9rem;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            border: none;
            box-shadow: 0 4px 8px -4px rgba(0, 40, 80, 0.1);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-sm {
            padding: 0.4rem 1rem;
            font-size: 0.8rem;
        }

        .btn-lg {
            padding: 0.9rem 2rem;
            font-size: 1rem;
        }

        .btn-primary {
            background: var(--accent-blue);
            color: white;
        }

        .btn-primary:hover {
            background: #1e5f96;
            transform: translateY(-2px);
            box-shadow: 0 10px 16px -8px var(--accent-blue);
        }

        .btn-secondary {
            background: white;
            color: var(--text-medium);
            border: 1px solid var(--border-subtle);
        }

        .btn-secondary:hover {
            border-color: var(--accent-blue);
            color: var(--accent-blue);
            background: #f9fcff;
        }

        .btn-success {
            background: var(--success-emerald);
            color: #fff;
        }

        .btn-success:hover {
            background: #16693b;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: var(--danger-coral);
        }

        .btn-danger:hover {
            background: #b94444;
        }

        .btn-warning {
            background: var(--warning-gold);
            color: #fff;
        }

        .btn-info {
            background: var(--info-sky);
        }

        .btn-outline-primary {
            background: transparent;
            border: 1.5px solid var(--accent-blue);
            color: var(--accent-blue);
        }

        .btn-outline-primary:hover {
            background: var(--accent-blue);
            color: white;
        }

        /* action buttons */
        .btn-view {
            background: rgba(43, 127, 194, 0.08);
            color: var(--accent-blue);
        }

        .btn-view:hover {
            background: var(--accent-blue);
            color: white;
        }

        .btn-edit {
            background: rgba(31, 139, 76, 0.08);
            color: var(--success-emerald);
        }

        .btn-edit:hover {
            background: var(--success-emerald);
            color: white;
        }

        .btn-delete {
            background: rgba(217, 85, 85, 0.08);
            color: var(--danger-coral);
        }

        .btn-delete:hover {
            background: var(--danger-coral);
            color: white;
        }

        /* ----- TABLES & DATATABLES (compact, corporate) ----- */
        .table {
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        .table thead th {
            border-bottom: 2px solid var(--border-subtle);
            color: var(--text-dark);
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            background: #fafcff;
            padding: 1rem 0.8rem;
        }

        .table tbody td {
            padding: 1rem 0.8rem;
            border-top: 1px solid var(--border-subtle);
            vertical-align: middle;
            color: var(--text-medium);
        }

        .table-hover tbody tr:hover {
            background: rgba(43, 127, 194, 0.02);
        }

        /* datatables wrapper fine-tune */
        .dataTables_wrapper {
            padding: 1rem 0;
        }

        .dataTables_length select,
        .dataTables_filter input {
            border: 1px solid var(--border-subtle);
            border-radius: 30px;
            padding: 0.45rem 1rem;
            background: white;
        }

        .dataTables_filter input:focus {
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 4px rgba(43, 127, 194, 0.1);
            outline: none;
            width: 260px;
        }

        .dt-buttons {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .dt-button {
            border: 1px solid var(--border-subtle) !important;
            border-radius: 30px !important;
            padding: 0.4rem 1.2rem !important;
            font-size: 0.8rem !important;
            background: white !important;
            color: var(--text-medium) !important;
        }

        .dt-button:hover {
            background: var(--accent-blue) !important;
            color: white !important;
            border-color: var(--accent-blue) !important;
        }

        /* ===== REDUCED PAGINATION SIZE (MORE COMPACT) ===== */
        .dataTables_paginate {
            display: flex;
            gap: 3px;
            justify-content: flex-end;
            padding: 0.5rem 0;
        }

        .dataTables_paginate .paginate_button {
            border: 1px solid var(--border-subtle);
            border-radius: 6px !important;
            /* Smaller border radius for compact look */
            padding: 0.25rem 0.6rem !important;
            /* Reduced padding */
            font-size: 0.75rem !important;
            /* Smaller font size */
            font-weight: 500;
            color: var(--text-medium) !important;
            background: white;
            cursor: pointer;
            transition: all 0.2s ease;
            margin: 0 1px;
            min-width: 28px;
            /* Fixed minimum width for consistency */
            text-align: center;
            line-height: 1.4;
        }

        .dataTables_paginate .paginate_button:hover:not(.disabled) {
            background: var(--accent-blue) !important;
            color: white !important;
            border-color: var(--accent-blue) !important;
            transform: translateY(-1px);
        }

        .dataTables_paginate .paginate_button.current {
            background: var(--accent-blue) !important;
            color: white !important;
            border-color: var(--accent-blue) !important;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(43, 127, 194, 0.2);
        }

        .dataTables_paginate .paginate_button.disabled {
            opacity: 0.4;
            cursor: not-allowed;
            pointer-events: none;
            background: #f8fafc !important;
        }

        /* Previous/Next buttons styling */
        .dataTables_paginate .paginate_button.previous,
        .dataTables_paginate .paginate_button.next {
            padding: 0.25rem 0.8rem !important;
            background: #f8fafc;
        }

        .dataTables_paginate .paginate_button.previous:hover:not(.disabled),
        .dataTables_paginate .paginate_button.next:hover:not(.disabled) {
            background: var(--accent-blue) !important;
        }

        /* First/Last buttons */
        .dataTables_paginate .paginate_button.first,
        .dataTables_paginate .paginate_button.last {
            padding: 0.25rem 0.7rem !important;
        }

        /* Compact pagination for mobile */
        @media (max-width: 768px) {
            .dataTables_paginate {
                flex-wrap: wrap;
                justify-content: center;
            }

            .dataTables_paginate .paginate_button {
                padding: 0.2rem 0.5rem !important;
                font-size: 0.7rem !important;
                min-width: 24px;
            }

            .dataTables_paginate .paginate_button.previous,
            .dataTables_paginate .paginate_button.next {
                padding: 0.2rem 0.6rem !important;
            }
        }

        /* Compact info text */
        .dataTables_info {
            font-size: 0.75rem !important;
            color: var(--text-soft);
            padding: 0.5rem 0 !important;
        }

        /* Compact length menu */
        .dataTables_length {
            margin-bottom: 0.5rem;
        }

        .dataTables_length label {
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .dataTables_length select {
            padding: 0.2rem 1.5rem 0.2rem 0.5rem;
            font-size: 0.8rem;
            background-position: right 0.3rem center;
        }

        /* ----- BADGES, STATS, MISC ----- */
        .badge {
            font-weight: 500;
            padding: 0.45rem 0.9rem;
            border-radius: 30px;
            font-size: 0.7rem;
        }

        .badge-primary {
            background: rgba(43, 127, 194, 0.12);
            color: var(--accent-blue);
        }

        .badge-success {
            background: rgba(31, 139, 76, 0.12);
            color: var(--success-emerald);
        }

        .badge-warning {
            background: rgba(230, 138, 46, 0.12);
            color: #a85d12;
        }

        .stats-card {
            background: white;
            border-radius: 1.5rem;
            padding: 1.5rem;
            box-shadow: var(--shadow-elevation);
        }

        .stats-icon {
            width: 52px;
            height: 52px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }

        /* loading overlay */
        .loading-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(3px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .loading-spinner {
            width: 48px;
            height: 48px;
            border: 4px solid #e0e9f2;
            border-top: 4px solid var(--accent-blue);
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* responsive touches */
        @media (max-width: 768px) {
            .content-header {
                padding: 1.2rem 1.2rem;
            }

            .content {
                padding: 0 1.2rem 1.5rem;
            }

            .brand-text {
                font-size: 1.1rem;
            }

            .main-sidebar {
                width: 260px !important;
            }

            .content-wrapper,
            .main-footer {
                margin-left: 260px !important;
            }

            .sidebar-mini.sidebar-collapse .main-sidebar {
                width: 70px !important;
            }

            .sidebar-mini.sidebar-collapse .content-wrapper,
            .sidebar-mini.sidebar-collapse .main-footer {
                margin-left: 70px !important;
            }
        }

        @media (max-width: 576px) {
            .content-header h1 {
                font-size: 1.5rem;
            }

            .brand-link {
                padding: 1rem;
            }
        }

        /* custom scroll */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #e9eef3;
        }

        ::-webkit-scrollbar-thumb {
            background: #b6c8da;
            border-radius: 20px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--accent-blue);
        }
    </style>
    @stack('styles')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- loading overlay -->
        <div class="loading-overlay" id="loadingOverlay">
            <div class="loading-spinner"></div>
        </div>

        <!-- navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <!-- notifications -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="far fa-bell"></i>
                        @if (isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                            <span
                                class="badge badge-warning navbar-badge animate__animated animate__pulse">{{ $unreadNotificationsCount }}</span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg">
                        <div class="dropdown-header">
                            <h6 class="mb-0">Notifications ({{ $unreadNotificationsCount ?? 0 }})</h6>
                        </div>
                        <div class="dropdown-divider"></div>
                        @if (isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                            <a href="{{ route('notifications.index') }}" class="dropdown-item">
                                <i class="fas fa-bell mr-2 text-primary"></i> You have {{ $unreadNotificationsCount }}
                                new <small class="float-right text-muted">now</small>
                            </a>
                        @else
                            <div class="dropdown-item text-center text-muted py-3">
                                <i class="far fa-bell-slash fa-2x mb-2"></i>
                                <p class="mb-0">No new notifications</p>
                            </div>
                        @endif
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('notifications.index') }}" class="dropdown-item text-center">View All</a>
                    </div>
                </li>
                <!-- user menu -->
                <li class="nav-item dropdown user-menu">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=1f6eaf&color=fff&size=32&bold=true"
                            class="user-image img-circle elevation-1" alt="User">
                        <span class="d-none d-md-inline">{{ auth()->user()->name ?? 'User' }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <div class="user-header text-center">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=1f6eaf&color=fff&size=80&bold=true"
                                class="img-circle elevation-2 mb-2">
                            <p class="mb-0 font-weight-bold">{{ auth()->user()->name ?? 'User' }}</p>
                            <small>{{ auth()->user()->role->name ?? 'Administrator' }}</small>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('profile.index') }}" class="dropdown-item"><i class="fas fa-user mr-2"></i>My
                            Profile</a>
                        <a href="{{ route('admin.settings') }}" class="dropdown-item"><i
                                class="fas fa-cog mr-2"></i>Settings</a>
                        <a href="{{ route('devices.index') }}" class="dropdown-item"><i
                                class="fas fa-laptop mr-2"></i>Devices</a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item text-danger" id="logoutLink"><i
                                class="fas fa-sign-out-alt mr-2"></i>Sign Out</a>
                    </div>
                </li>
            </ul>
        </nav>

        <!-- main sidebar (expanded) -->
        <aside class="main-sidebar elevation-4">
            <a href="{{ route('dashboard') }}" class="brand-link">
                <img src="{{ asset('assets/images/Sona-star-logo.png') }}" alt="Sona DMS" class="brand-image">
                <span class="brand-text">Sona DMS</span>
            </a>
            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                        <!-- dashboard -->
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}"
                                class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-chart-pie"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        @if (auth()->check() &&
                                (auth()->user()->isSuperAdmin() || (auth()->user()->role && auth()->user()->role->slug === 'admin')))
                            <li class="nav-item">
                                <a href="{{ route('admin.dashboard') }}"
                                    class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-shield-alt"></i>
                                    <p>Admin Dashboard</p>
                                </a>
                            </li>
                        @endif

                        <!-- FILE MANAGEMENT -->
                        @canany(['files.view', 'files.upload'])
                            <li class="nav-item has-treeview {{ request()->routeIs('files.*') ? 'menu-open' : '' }}">
                                <a href="#" class="nav-link {{ request()->routeIs('files.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-folder-tree"></i>
                                    <p>Files <i class="right fas fa-angle-left"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    @can('files.view')
                                        <li class="nav-item"><a href="{{ route('files.index') }}"
                                                class="nav-link {{ request()->routeIs('files.index') ? 'active' : '' }}"><i
                                                    class="far fa-circle nav-icon"></i>
                                                <p>All Files</p>
                                            </a></li>
                                    @endcan
                                    @can('files.upload')
                                        <li class="nav-item"><a href="{{ route('files.create') }}"
                                                class="nav-link {{ request()->routeIs('files.create') ? 'active' : '' }}"><i
                                                    class="far fa-circle nav-icon"></i>
                                                <p>Upload File</p>
                                            </a></li>
                                    @endcan
                                </ul>
                            </li>
                        @endcanany

                        <!-- TRANSFERS -->
                        @canany(['transfers.view', 'transfers.create'])
                            <li class="nav-item has-treeview {{ request()->routeIs('transfers.*') ? 'menu-open' : '' }}">
                                <a href="#"
                                    class="nav-link {{ request()->routeIs('transfers.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-exchange-alt"></i>
                                    <p>Transfers <i class="right fas fa-angle-left"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    @can('transfers.view')
                                        <li class="nav-item"><a href="{{ route('transfers.index') }}"
                                                class="nav-link {{ request()->routeIs('transfers.index') ? 'active' : '' }}"><i
                                                    class="far fa-circle nav-icon"></i>
                                                <p>All Transfers</p>
                                            </a></li>
                                    @endcan
                                    @can('transfers.create')
                                        <li class="nav-item"><a href="{{ route('transfers.create') }}"
                                                class="nav-link {{ request()->routeIs('transfers.create') ? 'active' : '' }}"><i
                                                    class="far fa-circle nav-icon"></i>
                                                <p>New Transfer</p>
                                            </a></li>
                                    @endcan
                                </ul>
                            </li>
                        @endcanany

                        <!-- ADMINISTRATION -->
                        @if (auth()->check() &&
                                (auth()->user()->isSuperAdmin() || (auth()->user()->role && auth()->user()->role->slug === 'admin')))
                            <li class="nav-item"><a href="{{ route('admin.users') }}"
                                    class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><i
                                        class="nav-icon fas fa-users-cog"></i>
                                    <p>Users</p>
                                </a></li>
                            <li class="nav-item"><a href="{{ route('admin.departments') }}"
                                    class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}"><i
                                        class="nav-icon fas fa-building"></i>
                                    <p>Departments</p>
                                </a></li>
                            <li class="nav-item"><a href="{{ route('admin.audit-logs') }}"
                                    class="nav-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}"><i
                                        class="nav-icon fas fa-history"></i>
                                    <p>Audit Logs</p>
                                </a></li>
                            <li class="nav-item"><a href="{{ route('admin.stats') }}"
                                    class="nav-link {{ request()->routeIs('admin.stats.*') ? 'active' : '' }}"><i
                                        class="nav-icon fas fa-chart-bar"></i>
                                    <p>Analytics</p>
                                </a></li>
                        @endif

                        <!-- SETTINGS -->
                        <li class="nav-item"><a href="{{ route('profile.index') }}"
                                class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"><i
                                    class="nav-icon fas fa-user-circle"></i>
                                <p>Profile</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('devices.index') }}"
                                class="nav-link {{ request()->routeIs('devices.*') ? 'active' : '' }}"><i
                                    class="nav-icon fas fa-laptop-house"></i>
                                <p>Devices</p>
                            </a></li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- content wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <h1 class="animate__animated animate__fadeIn">@yield('title')</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right bg-transparent">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i
                                            class="fas fa-home mr-1"></i>Home</a></li>
                                <li class="breadcrumb-item active">@yield('title')</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </section>
        </div>

        <footer class="main-footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Sona Document Management System</strong> &copy; {{ date('Y') }}
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- logout form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>

    <!-- scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <!-- datatables -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.6.2/js/dataTables.select.min.js"></script>
    <!-- export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <!-- sweetalert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        (function($) {
            'use strict';

            window.showLoading = function() {
                $('#loadingOverlay').fadeIn(200);
            };
            window.hideLoading = function() {
                $('#loadingOverlay').fadeOut(200);
            };

            window.showNotification = function(type, message, title = null) {
                const titles = {
                    success: 'Success!',
                    error: 'Error!',
                    warning: 'Warning!',
                    info: 'Information'
                };
                Swal.fire({
                    title: title || titles[type],
                    text: message,
                    icon: type,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3500,
                    timerProgressBar: true,
                    showCloseButton: true,
                    background: '#fff',
                    iconColor: type === 'success' ? '#1f8b4c' : type === 'error' ? '#d95555' : type ===
                        'warning' ? '#e68a2e' : '#2b7fc2',
                    customClass: {
                        popup: 'animate__animated animate__fadeInRight'
                    }
                });
            };

            window.confirmAction = function(title, text, callback, confirmText = 'Yes, proceed!') {
                Swal.fire({
                    title: title || 'Are you sure?',
                    text: text || 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#1f6eaf',
                    cancelButtonColor: '#8b9eb0',
                    confirmButtonText: confirmText,
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed && typeof callback === 'function') callback();
                });
            };

            // datatables init (safe)
            function initializeDataTables() {
                $('table').each(function() {
                    const $table = $(this);
                    if ($.fn.DataTable.isDataTable(this)) return;
                    if (!$table.find('thead').length || !$table.find('tbody').length) return;
                    try {
                        $table.DataTable({
                            responsive: true,
                            pageLength: 10,
                            lengthMenu: [
                                [10, 50, 100, -1],
                                [10, 50, 100, "All"]
                            ],
                            dom: '<"row"<"col-md-6"lB><"col-md-6"f>>' +
                                '<"row"<"col-md-12"tr>>' +
                                '<"row"<"col-md-5"i><"col-md-7"p>>',
                            buttons: [{
                                    extend: 'copy',
                                    text: '<i class="fas fa-copy"></i> Copy',
                                    className: 'btn btn-sm btn-outline-secondary'
                                },
                                {
                                    extend: 'excel',
                                    text: '<i class="fas fa-file-excel"></i> Excel',
                                    className: 'btn btn-sm btn-outline-success'
                                },
                                {
                                    extend: 'pdf',
                                    text: '<i class="fas fa-file-pdf"></i> PDF',
                                    className: 'btn btn-sm btn-outline-danger'
                                },
                                {
                                    extend: 'print',
                                    text: '<i class="fas fa-print"></i> Print',
                                    className: 'btn btn-sm btn-outline-primary'
                                }
                            ],
                            language: {
                                search: "Search:",
                                lengthMenu: "Show _MENU_ entries",
                                info: "Showing _START_ to _END_ of _TOTAL_",
                                paginate: {
                                    first: '<i class="fas fa-angle-double-left"></i>',
                                    previous: '<i class="fas fa-angle-left"></i>',
                                    next: '<i class="fas fa-angle-right"></i>',
                                    last: '<i class="fas fa-angle-double-right"></i>'
                                }
                            }
                        });
                    } catch (e) {
                        console.warn('DataTable init skipped', e);
                    }
                });
            }

            $(document).ready(function() {
                initializeDataTables();

                // session flashes
                @if (session('success'))
                    showNotification('success', '{{ session('success') }}');
                @endif
                @if (session('error'))
                    showNotification('error', '{{ session('error') }}');
                @endif
                @if (session('warning'))
                    showNotification('warning', '{{ session('warning') }}');
                @endif
                @if (session('info'))
                    showNotification('info', '{{ session('info') }}');
                @endif
            });

            // logout
            $(document).on('click', '#logoutLink', function(e) {
                e.preventDefault();
                confirmAction('Sign Out', 'End your current session?', function() {
                    $('#logout-form').submit();
                }, 'Yes, sign out');
            });

            // global ajax error
            $(document).ajaxError(function(_, jqxhr) {
                let msg = 'Request failed. Please try again.';
                if (jqxhr.status === 419) msg = 'Session expired. Refresh the page.';
                else if (jqxhr.status === 403) msg = 'Permission denied.';
                else if (jqxhr.status === 500) msg = 'Server error.';
                showNotification('error', msg);
                hideLoading();
            });

        })(jQuery);
    </script>

    <!-- additional compact styling for datatables -->
    <style>
        .dataTables_wrapper .dt-buttons .btn {
            border-radius: 30px !important;
            margin: 0 2px;
        }

        .table td,
        .table th {
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .dataTables_filter input {
                width: 180px;
            }

            .dt-buttons .btn {
                font-size: 0.75rem;
                padding: 0.3rem 0.6rem;
            }
        }
    </style>

    @stack('scripts')
</body>

</html>
