@extends('layouts.guest')

@section('title', 'Verify OTP')
@section('subtitle', 'Enter OTP sent to your email')

@section('content')

    <form method="POST" action="{{ route('otp.verify') }}" id="otpForm">
        @csrf

        <div class="text-center mb-3">
            <div class="timer" id="timer" style="font-size: 1.5rem; font-weight: bold; color: #4c6ef5;">05:00</div>
        </div>

        <div class="d-flex justify-content-center gap-2 mb-3">
            @for ($i = 0; $i < 6; $i++)
                <input type="text" class="otp-input" maxlength="1"
                    style="width: 50px; height: 50px; text-align: center; font-size: 1.5rem; border: 2px solid #ddd; border-radius: 8px;">
            @endfor
        </div>

        <input type="hidden" name="otp" id="otp_hidden">

        <button class="btn btn-auth w-100 mb-2" id="verifyBtn">Verify OTP</button>

    </form>

    <div class="text-center">
        <form method="POST" action="{{ route('otp.resend') }}" id="resendForm">
            @csrf
            <button type="submit" class="btn btn-link link" id="resendBtn">Resend OTP</button>
        </form>

        <a href="{{ route('login') }}" class="link d-block mt-2">← Back to Login</a>
    </div>

@endsection

@push('scripts')
    <script>
        const inputs = document.querySelectorAll('.otp-input');
        const hidden = document.getElementById('otp_hidden');
        const verifyBtn = document.getElementById('verifyBtn');

        inputs.forEach((input, i) => {
            input.addEventListener('input', () => {
                if (input.value && i < 5) inputs[i + 1].focus();
                updateOTP();

                // Auto-submit when all 6 digits are entered
                let val = '';
                inputs.forEach(i => val += i.value);
                if (val.length === 6) {
                    verifyBtn.disabled = true;
                    verifyBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2"></span>Verifying...';
                    document.getElementById('otpForm').submit();
                }
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

        // Timer countdown
        let time = 300;
        const timerEl = document.getElementById('timer');
        const resendBtn = document.getElementById('resendBtn');

        const countdown = setInterval(() => {
            let minutes = Math.floor(time / 60);
            let seconds = time % 60;
            timerEl.innerText = `${minutes.toString().padStart(2,'0')}:${seconds.toString().padStart(2,'0')}`;

            if (time <= 0) {
                clearInterval(countdown);
                timerEl.innerHTML = "Expired";
                timerEl.style.color = "#dc3545";
                resendBtn.disabled = false;

                Swal.fire({
                    icon: 'warning',
                    title: 'OTP Expired',
                    text: 'Your OTP has expired. Please request a new one.',
                    confirmButtonColor: '#4c6ef5'
                });
            }
            time--;
        }, 1000);

        resendBtn.disabled = true;

        // Resend OTP with SweetAlert confirmation
        document.getElementById('resendForm').addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Resend OTP?',
                text: 'A new verification code will be sent to your email.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4c6ef5',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, resend it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    </script>
@endpush
