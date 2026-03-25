@extends('layouts.guest')

@section('title', 'Two-Factor Authentication')

@section('content')
    <div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card shadow-lg border-0 rounded-lg">
                        <div class="card-header bg-primary text-white text-center py-4">
                            <h3 class="mb-0">
                                <i class="bi bi-shield-lock"></i> Two-Factor Authentication
                            </h3>
                        </div>
                        <div class="card-body p-4">
                            <div class="text-center mb-4">
                                <i class="bi bi-shield-check display-1 text-primary"></i>
                            </div>

                            <p class="text-muted text-center mb-4">
                                Please enter the 6-digit code from your authenticator app or a recovery code.
                            </p>

                            <form method="POST" action="{{ route('twofactor.verify') }}" id="verifyForm">
                                @csrf

                                <div class="mb-4">
                                    <label for="otp" class="form-label">Authentication Code</label>
                                    <div class="d-flex justify-content-between gap-2">
                                        <input type="text" class="form-control otp-input text-center" name="otp1"
                                            maxlength="1" pattern="[0-9a-zA-Z]" autofocus>
                                        <input type="text" class="form-control otp-input text-center" name="otp2"
                                            maxlength="1" pattern="[0-9a-zA-Z]">
                                        <input type="text" class="form-control otp-input text-center" name="otp3"
                                            maxlength="1" pattern="[0-9a-zA-Z]">
                                        <input type="text" class="form-control otp-input text-center" name="otp4"
                                            maxlength="1" pattern="[0-9a-zA-Z]">
                                        <input type="text" class="form-control otp-input text-center" name="otp5"
                                            maxlength="1" pattern="[0-9a-zA-Z]">
                                        <input type="text" class="form-control otp-input text-center" name="otp6"
                                            maxlength="1" pattern="[0-9a-zA-Z]">
                                        <input type="text" class="form-control otp-input text-center" name="otp7"
                                            maxlength="1" pattern="[0-9a-zA-Z]">
                                        <input type="text" class="form-control otp-input text-center" name="otp8"
                                            maxlength="1" pattern="[0-9a-zA-Z]">
                                    </div>
                                    <input type="hidden" name="otp" id="otp_hidden">
                                    <small class="text-muted">
                                        Enter 6-digit TOTP or 8-character recovery code
                                    </small>
                                </div>

                                @error('otp')
                                    <div class="alert alert-danger py-2">{{ $message }}</div>
                                @enderror

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg" id="verifyBtn">
                                        <i class="bi bi-shield-check"></i> Verify
                                    </button>
                                </div>
                            </form>

                            <div class="text-center mt-4">
                                <p class="mb-2">
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#recoveryModal">
                                        Use a recovery code instead
                                    </a>
                                </p>
                                <p class="mb-0">
                                    <a href="{{ route('login') }}" class="text-decoration-none">
                                        <i class="bi bi-arrow-left"></i> Back to Login
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recovery Code Modal -->
    <div class="modal fade" id="recoveryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enter Recovery Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">
                        Enter one of your 8-character recovery codes to access your account.
                    </p>
                    <form method="POST" action="{{ route('twofactor.verify') }}">
                        @csrf
                        <div class="mb-3">
                            <input type="text" class="form-control text-center" name="otp" placeholder="XXXX-XXXX"
                                maxlength="9" pattern="[A-Z0-9]{4}-?[A-Z0-9]{4}" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            Verify Recovery Code
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .otp-input {
            width: 45px;
            height: 55px;
            font-size: 20px;
            font-weight: bold;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            text-transform: uppercase;
        }

        .otp-input:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
            outline: none;
        }
    </style>
@endpush

@push('scripts')
    <script>
        const inputs = document.querySelectorAll('.otp-input');
        const otpHidden = document.getElementById('otp_hidden');
        const verifyBtn = document.getElementById('verifyBtn');

        // Auto-move to next input
        inputs.forEach((input, index) => {
            input.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
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

            // Auto-submit when all digits are entered
            if (otp.length === 8) {
                document.getElementById('verifyForm').submit();
            }
        }
    </script>
@endpush
