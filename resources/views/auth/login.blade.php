@extends('layouts.guest')

@section('title', 'Login')
@section('subtitle', 'Login to your account')

@section('content')

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="mb-3">
        <label class="form-label">Email</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" name="email" class="form-control" required>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" id="password" name="password" class="form-control" required>
            <span class="input-group-text" onclick="togglePassword()">
                <i class="bi bi-eye" id="eyeIcon"></i>
            </span>
        </div>
    </div>

    <div class="d-flex justify-content-between mb-3">
        <div class="form-check">
            <input type="checkbox" name="remember" class="form-check-input">
            <label class="form-check-label small">Remember</label>
        </div>

        <a href="{{ route('password.request') }}" class="link">Forgot?</a>
    </div>

    <button class="btn btn-auth w-100">Login</button>
</form>

@endsection

@push('scripts')
<script>
function togglePassword(){
    let p = document.getElementById('password');
    let i = document.getElementById('eyeIcon');

    if(p.type === 'password'){
        p.type = 'text';
        i.classList.replace('bi-eye','bi-eye-slash');
    } else {
        p.type = 'password';
        i.classList.replace('bi-eye-slash','bi-eye');
    }
}
</script>
@endpush