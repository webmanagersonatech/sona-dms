@extends('layouts.guest')

@section('title', 'Forgot Password')
@section('subtitle', 'Enter your email')

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('password.email') }}">
@csrf

<div class="mb-3">
    <label class="form-label">Email</label>
    <div class="input-group">
        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
        <input type="email" name="email" class="form-control" required>
    </div>
</div>

<button class="btn btn-auth w-100">Send Reset Code</button>

</form>

<div class="text-center mt-3">
    <a href="{{ route('login') }}" class="link">← Back to Login</a>
</div>

@endsection