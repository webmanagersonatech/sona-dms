@extends('layouts.guest')

@section('title', 'Login')
@section('subtitle', 'Login to your account')

@section('content')

    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        <div class="mb-3">
            <label class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            @error('email')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" id="password" name="password" class="form-control" required>
                <span class="input-group-text" onclick="togglePassword()" style="cursor: pointer;">
                    <i class="bi bi-eye" id="eyeIcon"></i>
                </span>
            </div>
            @error('password')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="d-flex justify-content-between mb-3">
            <div class="form-check">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label small" for="remember">Remember me</label>
            </div>

            <a href="{{ route('password.request') }}" class="link">Forgot Password?</a>
        </div>

        <button class="btn btn-auth w-100" id="loginBtn">Login</button>
    </form>

@endsection

@push('scripts')
    <script>
        function togglePassword() {
            let p = document.getElementById('password');
            let i = document.getElementById('eyeIcon');

            if (p.type === 'password') {
                p.type = 'text';
                i.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                p.type = 'password';
                i.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }

        // Add loading state to login button
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('loginBtn');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Logging in...';
        });
    </script>
@endpush
