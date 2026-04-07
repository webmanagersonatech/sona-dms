{{-- resources/views/files/verify-access.blade.php --}}
@extends('layouts.app')

@section('title', 'Verify File Access')

@section('content')
    <div class="row min-vh-75 align-items-center">
        <div class="col-md-5 mx-auto">
            <div class="card border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
                <div class="card-header bg-white border-0 pt-4 pb-0 text-center">
                    <div class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 rounded-circle mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-shield-lock text-warning fs-1"></i>
                    </div>
                    <h4 class="fw-bold mb-1">Verify File Access</h4>
                    <p class="text-muted small">Please enter the security code for approval</p>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="text-center mb-4">
                        <div class="p-3 bg-light rounded-3 mb-3 d-inline-block w-100">
                            <i class="bi bi-file-earmark-text text-primary me-2"></i>
                            <span class="fw-medium">{{ Str::limit($file->name, 40) }}</span>
                        </div>
                    </div>

                    <div class="alert alert-info border-0 shadow-sm mb-4" style="border-radius: 12px; background: #eef2ff;">
                        <div class="d-flex">
                            <i class="bi bi-info-circle-fill text-primary me-2 mt-1"></i>
                            <div class="small text-primary">
                                An OTP has been sent to the file owner ({{ $file->owner->email }}). 
                                Please obtain the code to proceed.
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('files.confirm', $file->uuid) }}" id="otpForm">
                        @csrf

                        <div class="mb-4">
                            <label for="access_reason" class="form-label small fw-bold text-uppercase text-muted">Reason for Access</label>
                            <textarea name="access_reason" id="access_reason" class="form-control" rows="2" 
                                placeholder="Enter your reason..." style="border-radius: 12px;" required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase text-muted d-block text-center mb-3">Verification Code</label>
                            <div class="d-flex justify-content-center gap-2">
                                <input type="text" class="form-control otp-input-field" maxlength="1" pattern="[0-9]" inputmode="numeric" autofocus>
                                <input type="text" class="form-control otp-input-field" maxlength="1" pattern="[0-9]" inputmode="numeric">
                                <input type="text" class="form-control otp-input-field" maxlength="1" pattern="[0-9]" inputmode="numeric">
                                <input type="text" class="form-control otp-input-field" maxlength="1" pattern="[0-9]" inputmode="numeric">
                                <input type="text" class="form-control otp-input-field" maxlength="1" pattern="[0-9]" inputmode="numeric">
                                <input type="text" class="form-control otp-input-field" maxlength="1" pattern="[0-9]" inputmode="numeric">
                            </div>
                            <input type="hidden" name="otp" id="otp_hidden">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-3 fw-bold" id="verifyBtn" 
                                style="border-radius: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                                <i class="bi bi-check-circle me-2"></i> Verify & Access
                            </button>
                            <a href="{{ route('files.show', $file) }}" class="btn btn-link text-muted small">
                                <i class="bi bi-arrow-left me-1"></i> Cancel & Return
                            </a>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2 fw-bold">
                            <i class="bi bi-clock me-1"></i> <span id="timer">05:00</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .otp-input-field {
            width: 48px;
            height: 58px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 700;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.2s;
        }

        .otp-input-field:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        .min-vh-75 { min-height: 75vh; }
    </style>
@endpush

@push('scripts')
    <script>
        const inputs = document.querySelectorAll('.otp-input-field');
        const otpHidden = document.getElementById('otp_hidden');
        const timerElement = document.getElementById('timer');
        const verifyBtn = document.getElementById('verifyBtn');

        inputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                if (e.inputType === 'deleteContentBackward') return;
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
                const paste = e.clipboardData.getData('text').slice(0, 6);
                if (/^\d+$/.test(paste)) {
                    paste.split('').forEach((char, i) => {
                        if (inputs[i]) inputs[i].value = char;
                    });
                    updateHiddenOtp();
                    inputs[Math.min(paste.length, inputs.length - 1)].focus();
                }
            });
        });

        function updateHiddenOtp() {
            let otp = '';
            inputs.forEach(input => otp += input.value);
            otpHidden.value = otp;
        }

        let timeLeft = 300;
        const timerInterval = setInterval(() => {
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                timerElement.textContent = 'Expired';
                verifyBtn.disabled = true;
                return;
            }
            timeLeft--;
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }, 1000);

        document.getElementById('otpForm').addEventListener('submit', function(e) {
            if (otpHidden.value.length !== 6) {
                e.preventDefault();
                alert('Please enter the complete 6-digit OTP');
            }
        });
    </script>
@endpush
