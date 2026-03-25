{{-- resources/views/files/verify-access.blade.php --}}
@extends('layouts.app')

@section('title', 'Verify File Access')

@section('content')
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="card-title mb-0 text-white">
                        <i class="bi bi-shield-lock"></i> File Access Verification
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="bi bi-file-earmark-text text-primary" style="font-size: 4rem;"></i>
                        <h5 class="mt-3">{{ $file->name }}</h5>
                        <p class="text-muted">Size: {{ $file->size_for_humans }}</p>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        An OTP has been sent to the file owner ({{ $file->owner->email }}) for approval.
                        Please ask them to share the OTP with you.
                    </div>

                    <form method="POST" action="{{ route('files.access.confirm', $file->uuid) }}" id="otpForm">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label">Enter OTP Code</label>
                            <div class="d-flex justify-content-between">
                                <input type="text" class="form-control otp-input" name="otp1" maxlength="1"
                                    pattern="[0-9]" inputmode="numeric" autofocus>
                                <input type="text" class="form-control otp-input" name="otp2" maxlength="1"
                                    pattern="[0-9]" inputmode="numeric">
                                <input type="text" class="form-control otp-input" name="otp3" maxlength="1"
                                    pattern="[0-9]" inputmode="numeric">
                                <input type="text" class="form-control otp-input" name="otp4" maxlength="1"
                                    pattern="[0-9]" inputmode="numeric">
                                <input type="text" class="form-control otp-input" name="otp5" maxlength="1"
                                    pattern="[0-9]" inputmode="numeric">
                                <input type="text" class="form-control otp-input" name="otp6" maxlength="1"
                                    pattern="[0-9]" inputmode="numeric">
                            </div>
                            <input type="hidden" name="otp" id="otp_hidden">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" id="verifyBtn">
                                <i class="bi bi-check-circle"></i> Verify & Download
                            </button>
                            <a href="{{ route('files.show', $file) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to File
                            </a>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <small class="text-muted">
                            OTP expires in <span id="timer">05:00</span>
                        </small>
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
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            margin: 0 3px;
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
        const timerElement = document.getElementById('timer');
        const verifyBtn = document.getElementById('verifyBtn');

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
                window.location.href = '{{ route('files.show', $file) }}';
            }
            timeLeft--;
        }, 1000);

        // Form validation
        document.getElementById('otpForm').addEventListener('submit', function(e) {
            if (otpHidden.value.length !== 6) {
                e.preventDefault();
                alert('Please enter complete 6-digit OTP');
            }
        });
    </script>
@endpush
