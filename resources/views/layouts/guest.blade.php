{{-- resources/views/layouts/guest.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ config('app.name') }} - Management System">

    <title>{{ config('app.name') }} - @yield('title')</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --glass-bg: rgba(255, 255, 255, 0.95);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        body {
            font-family: 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: url('{{ asset('assets/images/background.webp') }}') no-repeat center center fixed;
            background-size: cover;
            position: relative;
            overflow: hidden;
            margin: 0;
        }

        /* Overlay for opacity effect on background */
        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.6); /* Adjust opacity here */
            backdrop-filter: blur(5px);
            z-index: 1;
        }

        .auth-card {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 440px;
            border-radius: 28px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            box-shadow: 0 50px 100px -20px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: cardEntrance 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes cardEntrance {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-header {
            text-align: center;
            padding: 45px 30px 25px;
        }

        .logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-bottom: 20px;
            filter: drop-shadow(0 8px 16px rgba(0,0,0,0.15));
        }

        .auth-header h5 {
            font-size: 1.6rem;
            font-weight: 800;
            color: #111827;
            letter-spacing: -0.7px;
            margin-bottom: 8px;
        }

        .auth-header small {
            color: #6b7280;
            font-size: 1rem;
            font-weight: 500;
        }

        .auth-body {
            padding: 0 45px 45px;
        }

        .form-label {
            font-size: 0.8rem;
            font-weight: 700;
            color: #374151;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-control {
            border-radius: 14px;
            padding: 14px 18px;
            font-size: 0.95rem;
            border: 2px solid #f3f4f6;
            background: #f9fafb;
            transition: all 0.25s ease;
        }

        .form-control:focus {
            background: #fff;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
        }

        .input-group-text {
            background: #f9fafb;
            border: 2px solid #f3f4f6;
            border-radius: 14px;
            color: #9ca3af;
            padding: 0 18px;
        }

        .btn-auth {
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: 14px;
            padding: 16px;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px -5px rgba(102, 126, 234, 0.4);
            margin-top: 10px;
        }

        .btn-auth:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 30px -8px rgba(102, 126, 234, 0.5);
            color: white;
        }

        .otp-input {
            width: 52px;
            height: 64px;
            text-align: center;
            font-size: 1.7rem;
            font-weight: 800;
            border-radius: 14px;
            border: 2px solid #f3f4f6;
            background: #f9fafb;
            transition: all 0.2s;
            margin: 0 5px;
        }

        .otp-input:focus {
            background: #fff;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
            outline: none;
        }

        .link {
            font-size: 0.95rem;
            color: #4f46e5;
            font-weight: 600;
            text-decoration: none;
        }

        .link:hover {
            color: #3730a3;
            text-decoration: underline;
        }

        .timer {
            font-size: 1.3rem;
            font-weight: 800;
            color: #ef4444;
            background: #fee2e2;
            padding: 10px 24px;
            border-radius: 100px;
            display: inline-block;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.1);
        }

        @media (max-width: 576px) {
            .auth-card {
                max-width: 92%;
            }
            .auth-header { padding: 35px 25px 15px; }
            .auth-body { padding: 0 30px 35px; }
            .otp-input { width: 45px; height: 58px; font-size: 1.4rem; margin: 0 2px; }
        }
    </style>

    @stack('styles')
</head>

<body>

    <div class="auth-card">

        {{-- HEADER --}}
        <div class="auth-header">
            <img src="{{ asset('assets/images/Sona-star-logo.png') }}" class="logo">
            <h5>{{ config('app.name') }}</h5>
            <small>@yield('subtitle')</small>
        </div>

        {{-- BODY --}}
        <div class="auth-body">
            @yield('content')
        </div>

    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- SweetAlert Messages --}}
    <script>
        const commonToast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        @if(session('success'))
            commonToast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        @endif

        @if(session('error'))
            commonToast.fire({
                icon: 'error',
                title: "{{ session('error') }}",
                timer: 4000
            });
        @endif

        @if(session('warning'))
            commonToast.fire({
                icon: 'warning',
                title: "{{ session('warning') }}"
            });
        @endif

        @if(session('info'))
            commonToast.fire({
                icon: 'info',
                title: "{{ session('info') }}"
            });
        @endif

        @if ($errors->any())
            commonToast.fire({
                icon: 'error',
                title: 'Validation Error',
                html: `<div class="text-start small">{!! implode('<br>', $errors->all()) !!}</div>`,
                timer: 6000
            });
        @endif
    </script>

    @stack('scripts')
</body>

</html>