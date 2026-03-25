@extends('layouts.guest')

@section('title', 'Reset Password')
@section('subtitle', 'Enter OTP & new password')

@section('content')

    <form method="POST" action="{{ route('password.update') }}" id="resetForm">
        @csrf

        {{-- OTP --}}
        <div class="d-flex justify-content-center gap-2 mb-3">
            @for ($i = 0; $i < 6; $i++)
                <input type="text" class="otp-input" maxlength="1">
            @endfor
        </div>

        <input type="hidden" name="otp" id="otp_hidden">

        {{-- Password --}}
        <div class="mb-3">
            <label class="form-label">New Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        {{-- Confirm --}}
        <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" id="confirm" name="password_confirmation" class="form-control" required>
            <div id="match" class="small mt-1"></div>
        </div>

        <button class="btn btn-auth w-100 mb-2">Reset Password</button>

    </form>

    <div class="text-center">
        <div class="timer" id="timer">05:00</div>
    </div>

@endsection

@push('scripts')
    <script>
        // ================= OTP INPUT =================
        const inputs = document.querySelectorAll('.otp-input');
        const hidden = document.getElementById('otp_hidden');

        inputs.forEach((input, i) => {

            input.addEventListener('input', () => {
                if (input.value && i < inputs.length - 1) {
                    inputs[i + 1].focus();
                }
                updateOTP();
            });

            // backspace support 🔥
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
                msg.innerHTML = '<span class="text-success">✔ Password Matched</span>';
            } else {
                msg.innerHTML = '<span class="text-danger">✖ Password Not Match</span>';
            }
        });

        // ================= 5 MIN TIMER =================
        let totalSeconds = 5 * 60; // 5 minutes
        const timerEl = document.getElementById('timer');

        const countdown = setInterval(() => {

            let minutes = Math.floor(totalSeconds / 60);
            let seconds = totalSeconds % 60;

            minutes = minutes < 10 ? '0' + minutes : minutes;
            seconds = seconds < 10 ? '0' + seconds : seconds;

            timerEl.innerHTML = `${minutes}:${seconds}`;

            if (totalSeconds <= 0) {
                clearInterval(countdown);
                timerEl.innerHTML = "Expired";

                // disable button 🔒
                document.querySelector('#resetForm button').disabled = true;

                // SweetAlert 🔥
                Swal.fire({
                    icon: 'error',
                    title: 'OTP Expired',
                    text: 'Please request a new OTP',
                    confirmButtonColor: '#4c6ef5'
                });
            }

            totalSeconds--;

        }, 1000);
    </script>
@endpush
