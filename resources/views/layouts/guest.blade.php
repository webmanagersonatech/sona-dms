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
        body {
            font-family: 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;

            background: url('{{ asset('assets/images/background.webp') }}') no-repeat center center;
            background-size: cover;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
        }

        .auth-card {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 420px;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            overflow: hidden;
        }

        .auth-header {
            text-align: center;
            padding: 25px;
            border-bottom: 1px solid #eee;
        }

        .logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
            margin-bottom: 10px;
        }

        .auth-header h5 {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .auth-header small {
            color: #6c757d;
        }

        .auth-body {
            padding: 30px;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 500;
        }

        .form-control {
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .input-group-text {
            background: #f1f3f5;
        }

        .btn-auth {
            background: #4c6ef5;
            border: none;
            border-radius: 8px;
            padding: 10px;
            font-weight: 500;
        }

        .btn-auth:hover {
            background: #364fc7;
        }

        .otp-input {
            width: 45px;
            height: 55px;
            text-align: center;
            font-size: 20px;
            font-weight: 600;
            border-radius: 8px;
            border: 1px solid #ced4da;
        }

        .otp-input:focus {
            border-color: #4c6ef5;
            outline: none;
        }

        .link {
            font-size: 0.85rem;
            text-decoration: none;
        }

        .link:hover {
            text-decoration: underline;
        }

        .timer {
            font-weight: 600;
            color: #4c6ef5;
        }

        @media (max-width: 768px) {
            .auth-body {
                padding: 25px;
            }
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
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ session('success') }}",
                timer: 2500,
                showConfirmButton: false,
                confirmButtonColor: '#4c6ef5'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "{{ session('error') }}",
                confirmButtonColor: '#4c6ef5'
            });
        @endif

        @if(session('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: "{{ session('warning') }}",
                confirmButtonColor: '#4c6ef5'
            });
        @endif

        @if(session('info'))
            Swal.fire({
                icon: 'info',
                title: 'Info',
                text: "{{ session('info') }}",
                confirmButtonColor: '#4c6ef5'
            });
        @endif

        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                confirmButtonColor: '#4c6ef5'
            });
        @endif
    </script>

    @stack('scripts')
</body>

</html>