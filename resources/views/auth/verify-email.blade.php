@extends('layouts.guest')

@section('title', 'Verify Email')

@section('content')
    <div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card shadow-lg border-0 rounded-lg">
                        <div class="card-header bg-warning text-white text-center py-4">
                            <h3 class="mb-0">
                                <i class="bi bi-envelope-check"></i> Verify Your Email
                            </h3>
                        </div>
                        <div class="card-body p-4 text-center">
                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <div class="mb-4">
                                <i class="bi bi-envelope display-1 text-warning"></i>
                            </div>

                            <h5 class="mb-3">Email Verification Required</h5>
                            <p class="text-muted mb-4">
                                We've sent a 6-digit verification code to <strong>{{ auth()->user()->email }}</strong>.
                                Please enter the code below to verify your email address.
                            </p>

                            <form method="POST" action="{{ route('verification.verify') }}" id="otpForm">
                                @csrf

                                <div class="mb-4">
                                    <label class="form-label">Enter Verification Code</label>
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
                                </div>

                                @error('otp')
                                    <div class="alert alert-danger py-2">{{ $message }}</div>
                                @enderror

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-warning btn-lg" id="verifyBtn">
                                        <i class="bi bi-check-circle"></i> Verify Email
                                    </button>
                                </div>
                            </form>

                            <div class="mt-4">
                                <div class="timer mb-2">
                                    <span id="timer">05:00</span> remaining
                                </div>

                                <form method="POST" action="{{ route('verification.resend') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-link" id="resendBtn" disabled>
                                        Resend Verification Code
                                    </button>
                                </form>
                            </div>

                            <hr class="my-4">

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="bi bi-box-arrow-right"></i> Logout & Try Again Later
                                </button>
                            </form>
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
            border-color: #ffc107;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
            outline: none;
        }

        .timer {
            font-size: 1.2rem;
            font-weight: 600;
            color: #ffc107;
        }
    </style>
@endpush

@push('scripts')
    <script>
        const inputs = document.querySelectorAll('.otp-input');
        const otpHidden = document.getElementById('otp_hidden');
        const timerElement = document.getElementById('timer');
        const verifyBtn = document.getElementById('verifyBtn');
        const resendBtn = document.getElementById('resendBtn');

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

            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const paste = e.clipboardData.getData('text');
                if (paste.length === 6 && /^\d+$/.test(paste)) {
                    for (let i = 0; i < 6; i++) {
                        if (inputs[i]) inputs[i].value = paste[i];
                    }
                    updateHiddenOtp();
                    inputs[5].focus();
                }
            });
        });

        function updateHiddenOtp() {
            let otp = '';
            inputs.forEach(input => otp += input.value);
            otpHidden.value = otp;

            // Auto-submit when all digits are entered
            if (otp.length === 6) {
                document.getElementById('otpForm').submit();
            }
        }

        // Timer (5 minutes)
        let timeLeft = 300;
        const timerInterval = setInterval(() => {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent =
                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                timerElement.textContent = 'Expired';
                verifyBtn.disabled = true;
                resendBtn.disabled = false;
            }
            timeLeft--;
        }, 1000);

        // Form validation
        document.getElementById('otpForm').addEventListener('submit', function(e) {
            if (otpHidden.value.length !== 6) {
                e.preventDefault();
                alert('Please enter complete 6-digit verification code');
            }
        });
    </script>
@endpush
