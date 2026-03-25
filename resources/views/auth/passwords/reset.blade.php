@extends('layouts.guest')

@section('title', 'Reset Password')

@section('content')
    <div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card shadow-lg border-0 rounded-lg">
                        <div class="card-header bg-success text-white text-center py-4">
                            <h3 class="mb-0">
                                <i class="bi bi-shield-lock"></i> Reset Password
                            </h3>
                        </div>
                        <div class="card-body p-4">
                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <div class="text-center mb-4">
                                <i class="bi bi-shield-check display-1 text-success"></i>
                            </div>

                            <p class="text-muted text-center mb-4">
                                Enter the 6-digit code sent to your email and your new password.
                            </p>

                            <form method="POST" action="{{ route('password.update') }}" id="resetForm">
                                @csrf

                                <div class="mb-4">
                                    <label class="form-label">Reset Code</label>
                                    <div class="d-flex justify-content-between gap-2">
                                        <input type="text" class="form-control otp-input text-center" name="otp1"
                                            maxlength="1" pattern="[0-9]" inputmode="numeric" autofocus>
                                        <input type="text" class="form-control otp-input text-center" name="otp2"
                                            maxlength="1" pattern="[0-9]" inputmode="numeric">
                                        <input type="text" class="form-control otp-input text-center" name="otp3"
                                            maxlength="1" pattern="[0-9]" inputmode="numeric">
                                        <input type="text" class="form-control otp-input text-center" name="otp4"
                                            maxlength="1" pattern="[0-9]" inputmode="numeric">
                                        <input type="text" class="form-control otp-input text-center" name="otp5"
                                            maxlength="1" pattern="[0-9]" inputmode="numeric">
                                        <input type="text" class="form-control otp-input text-center" name="otp6"
                                            maxlength="1" pattern="[0-9]" inputmode="numeric">
                                    </div>
                                    <input type="hidden" name="otp" id="otp_hidden">
                                    @error('otp')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-lock"></i>
                                        </span>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                            id="password" name="password" placeholder="Enter new password" required>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-lock-fill"></i>
                                        </span>
                                        <input type="password" class="form-control" id="password_confirmation"
                                            name="password_confirmation" placeholder="Confirm new password" required>
                                    </div>
                                    <div id="passwordMatch" class="small mt-1"></div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="bi bi-check-circle"></i> Reset Password
                                    </button>
                                </div>
                            </form>

                            <div class="text-center mt-4">
                                <div class="timer mb-2">
                                    <span id="timer">30:00</span> remaining
                                </div>

                                <form method="POST" action="{{ route('password.email') }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="email" value="{{ session('reset_email') }}">
                                    <button type="submit" class="btn btn-link" id="resendBtn">
                                        Resend Code
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .otp-input {
            width: 50px;
            height: 60px;
            font-size: 24px;
            font-weight: bold;
            border: 2px solid #dee2e6;
            border-radius: 8px;
        }

        .otp-input:focus {
            border-color: #198754;
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
            outline: none;
        }

        .timer {
            font-size: 1rem;
            color: #6c757d;
        }
    </style>
@endpush

@push('scripts')
    <script>
        const inputs = document.querySelectorAll('.otp-input');
        const otpHidden = document.getElementById('otp_hidden');
        const timerElement = document.getElementById('timer');

        // Auto-move to next input
        inputs.forEach((input, index) => {
            input.addEventListener('input', function() {
                if (this.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                updateHiddenOtp();
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });

        function updateHiddenOtp() {
            let otp = '';
            inputs.forEach(input => otp += input.value);
            otpHidden.value = otp;
        }

        // Timer (30 minutes)
        let timeLeft = 1800;
        const timerInterval = setInterval(() => {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent =
                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                timerElement.textContent = 'Expired';
            }
            timeLeft--;
        }, 1000);

        // Password match checker
        document.getElementById('password_confirmation').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirm = this.value;
            const matchDiv = document.getElementById('passwordMatch');

            if (confirm.length > 0) {
                if (password === confirm) {
                    matchDiv.innerHTML =
                        '<span class="text-success"><i class="bi bi-check-circle"></i> Passwords match</span>';
                } else {
                    matchDiv.innerHTML =
                        '<span class="text-danger"><i class="bi bi-exclamation-circle"></i> Passwords do not match</span>';
                }
            } else {
                matchDiv.innerHTML = '';
            }
        });

        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');

            if (password.type === 'password') {
                password.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                password.type = 'password';
                icon.className = 'bi bi-eye';
            }
        });

        // Form validation
        document.getElementById('resetForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirmation').value;
            const otp = otpHidden.value;

            if (otp.length !== 6) {
                e.preventDefault();
                alert('Please enter the complete 6-digit reset code');
            } else if (password !== confirm) {
                e.preventDefault();
                alert('Passwords do not match!');
            }
        });
    </script>
@endpush
