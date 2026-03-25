@extends('layouts.guest')

@section('title', 'Reset Password')
@section('subtitle', 'Enter OTP & new password')

@section('content')

    <form method="POST" action="{{ route('password.update') }}" id="resetForm">
        @csrf

        {{-- OTP Input --}}
        <div class="mb-3">
            <label class="form-label">OTP Code</label>
            <div class="d-flex justify-content-center gap-2">
                @for ($i = 0; $i < 6; $i++)
                    <input type="text" class="otp-input" maxlength="1"
                        style="width: 50px; height: 50px; text-align: center; font-size: 1.5rem; border: 2px solid #ddd; border-radius: 8px;">
                @endfor
            </div>
        </div>

        <input type="hidden" name="otp" id="otp_hidden">
        <input type="hidden" name="email" value="{{ request()->email }}">

        {{-- Password --}}
        <div class="mb-3">
            <label class="form-label">New Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" id="password" name="password" class="form-control" required>
                <span class="input-group-text" onclick="togglePassword()" style="cursor: pointer;">
                    <i class="bi bi-eye" id="eyeIcon"></i>
                </span>
            </div>
        </div>

        {{-- Confirm Password --}}
        <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-check-circle"></i></span>
                <input type="password" id="confirm" name="password_confirmation" class="form-control" required>
            </div>
            <div id="match" class="small mt-1"></div>
        </div>

        <button class="btn btn-auth w-100 mb-2" id="resetBtn">Reset Password</button>

    </form>

    <div class="text-center">
        <div class="timer" id="timer" style="font-size: 1rem; color: #6c757d;">05:00 remaining</div>
        <a href="{{ route('password.request') }}" class="link d-block mt-2">← Request New OTP</a>
    </div>

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

        // ================= OTP INPUT =================
        const inputs = document.querySelectorAll('.otp-input');
        const hidden = document.getElementById('otp_hidden');
        const resetBtn = document.getElementById('resetBtn');

        inputs.forEach((input, i) => {
            input.addEventListener('input', () => {
                if (input.value && i < inputs.length - 1) {
                    inputs[i + 1].focus();
                }
                updateOTP();
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === "Backspace" && !input.value && i > 0) {
                    inputs[i - 1].focus();
                }
            });
        });

        function updateOTP() {
            let val = '';
            inputs.forEach(i => val += i.value);
            hidden.value = val;
        }

        // ================= PASSWORD MATCH =================
        document.getElementById('confirm').addEventListener('input', function() {
            let p = document.getElementById('password').value;
            let c = this.value;
            let msg = document.getElementById('match');

            if (c === '') {
                msg.innerHTML = '';
            } else if (p === c) {
                msg.innerHTML = '<span class="text-success">✔ Password matched</span>';
            } else {
                msg.innerHTML = '<span class="text-danger">✖ Password does not match</span>';
            }
        });

        // ================= 5 MIN TIMER =================
        let totalSeconds = 5 * 60;
        const timerEl = document.getElementById('timer');

        const countdown = setInterval(() => {
            let minutes = Math.floor(totalSeconds / 60);
            let seconds = totalSeconds % 60;

            minutes = minutes < 10 ? '0' + minutes : minutes;
            seconds = seconds < 10 ? '0' + seconds : seconds;

            timerEl.innerHTML = `${minutes}:${seconds} remaining`;

            if (totalSeconds <= 0) {
                clearInterval(countdown);
                timerEl.innerHTML = "OTP expired!";
                timerEl.style.color = "#dc3545";
                resetBtn.disabled = true;
                resetBtn.style.opacity = "0.5";

                Swal.fire({
                    icon: 'error',
                    title: 'OTP Expired',
                    text: 'Your OTP has expired. Please request a new one.',
                    confirmButtonColor: '#4c6ef5',
                    confirmButtonText: 'Request New OTP'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('password.request') }}";
                    }
                });
            }

            totalSeconds--;
        }, 1000);

        // Form validation before submit
        document.getElementById('resetForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm').value;

            if (password !== confirm) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Password Mismatch',
                    text: 'Password and confirm password do not match.',
                    confirmButtonColor: '#dc3545'
                });
                return false;
            }

            if (password.length < 8) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Weak Password',
                    text: 'Password must be at least 8 characters long.',
                    confirmButtonColor: '#dc3545'
                });
                return false;
            }

            const otp = hidden.value;
            if (otp.length !== 6) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid OTP',
                    text: 'Please enter the 6-digit OTP code.',
                    confirmButtonColor: '#dc3545'
                });
                return false;
            }

            // Show loading state
            resetBtn.disabled = true;
            resetBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Resetting...';
        });
    </script>
@endpush
