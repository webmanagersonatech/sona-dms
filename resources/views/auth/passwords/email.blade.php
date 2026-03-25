@extends('layouts.guest')

@section('title', 'Forgot Password')

@section('content')
    <div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card shadow-lg border-0 rounded-lg">
                        <div class="card-header bg-info text-white text-center py-4">
                            <h3 class="mb-0">
                                <i class="bi bi-key"></i> Reset Password
                            </h3>
                        </div>
                        <div class="card-body p-4">
                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <div class="text-center mb-4">
                                <i class="bi bi-envelope-paper display-1 text-info"></i>
                            </div>

                            <p class="text-muted text-center mb-4">
                                Enter your email address and we'll send you a password reset code.
                            </p>

                            <form method="POST" action="{{ route('password.email') }}">
                                @csrf

                                <div class="mb-4">
                                    <label for="email" class="form-label">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-envelope"></i>
                                        </span>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="email" name="email" value="{{ old('email') }}"
                                            placeholder="Enter your email" required autofocus>
                                    </div>
                                    @error('email')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-info btn-lg text-white">
                                        <i class="bi bi-send"></i> Send Reset Code
                                    </button>
                                </div>

                                <div class="text-center mt-4">
                                    <a href="{{ route('login') }}" class="text-decoration-none">
                                        <i class="bi bi-arrow-left"></i> Back to Login
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
