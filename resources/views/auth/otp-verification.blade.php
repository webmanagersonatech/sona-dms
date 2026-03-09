<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OTP Verification - Secure DMS</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background: linear-gradient(rgba(26, 54, 93, 0.9), rgba(26, 54, 93, 0.9)),
                url('assets/images/background.webp');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .otp-container {
            max-width: 500px;
            margin: 0 auto;
        }

        .otp-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .otp-card:hover {
            transform: translateY(-5px);
        }

        .otp-header {
            background: linear-gradient(135deg, #1a365d 0%, #2d3748 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .otp-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none"><path d="M1200 120L0 16.48 0 0 1200 0 1200 120z" fill="%23ffffff" opacity="0.1"/></svg>');
            background-size: cover;
            opacity: 0.1;
        }

        .otp-header h3 {
            font-weight: 600;
            font-size: 24px;
            margin-bottom: 8px;
            position: relative;
        }

        .otp-header p {
            opacity: 0.9;
            font-size: 15px;
            margin: 0;
        }

        .otp-body {
            padding: 40px 35px;
        }

        .otp-email-display {
            background: #f0fff4;
            border: 1px solid #c6f6d5;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin: 25px 0;
        }

        .otp-email-display i {
            color: #38a169;
            font-size: 32px;
            margin-bottom: 12px;
        }

        .email-address {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
            margin: 8px 0;
            word-break: break-all;
        }

        .timer-display {
            font-size: 14px;
            color: #718096;
        }

        .timer {
            font-weight: 700;
            color: #3182ce;
        }

        .otp-inputs {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin: 30px 0;
        }

        .otp-input {
            width: 56px;
            height: 56px;
            text-align: center;
            font-size: 22px;
            font-weight: 600;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            transition: all 0.3s ease;
            color: #2d3748;
        }

        .otp-input:focus {
            border-color: #3182ce;
            box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
            outline: none;
        }

        .otp-input.filled {
            border-color: #3182ce;
            background: #ebf8ff;
        }

        .btn-verify {
            background: linear-gradient(135deg, #3182ce 0%, #2b6cb0 100%);
            border: none;
            color: white;
            padding: 14px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-verify:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(49, 130, 206, 0.3);
            background: linear-gradient(135deg, #2b6cb0 0%, #2c5282 100%);
        }

        .btn-verify:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .resend-section {
            text-align: center;
            margin-top: 25px;
        }

        .btn-resend {
            background: transparent;
            border: none;
            color: #3182ce;
            font-weight: 500;
            padding: 8px 16px;
            transition: all 0.3s ease;
        }

        .btn-resend:hover:not(:disabled) {
            color: #2b6cb0;
            text-decoration: underline;
        }

        .btn-resend:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            color: #718096;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
            margin-top: 20px;
        }

        .back-link:hover {
            color: #3182ce;
        }

        .alert {
            border-radius: 10px;
            border: none;
            padding: 16px 20px;
            font-size: 14px;
            margin-bottom: 24px;
        }

        .alert-danger {
            background: #fff5f5;
            color: #c53030;
            border-left: 4px solid #fc8181;
        }

        .alert-warning {
            background: #fffaf0;
            color: #b7791f;
            border-left: 4px solid #feebc8;
        }

        /* Animation */
        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        .shake {
            animation: shake 0.3s ease-in-out;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="otp-container">
                    <div class="otp-card">
                        <div class="otp-header">
                            <h3><i class="fas fa-shield-check"></i> OTP Verification</h3>
                            <p>Enter the 6-digit code sent to your email</p>
                        </div>

                        <div class="otp-body">
                            <div class="otp-email-display">
                                <i class="fas fa-envelope-circle-check"></i>
                                <p class="mb-2">Verification code sent to:</p>
                                <div class="email-address">{{ $email }}</div>
                                <p class="timer-display mt-2">Valid for <span id="timer" class="timer">1</span>
                                    minutes</p>
                            </div>

                            <form action="{{ route('otp.verify.submit') }}" method="POST" id="otpForm">
                                @csrf
                                <input type="hidden" name="purpose" value="{{ $purpose }}">
                                <input type="hidden" name="otp" id="otpCode">

                                <div class="otp-inputs">
                                    @for ($i = 1; $i <= 6; $i++)
                                        <input type="text" class="otp-input form-control" maxlength="1"
                                            data-index="{{ $i }}"
                                            oninput="moveToNext(this, {{ $i }})"
                                            onkeydown="handleBackspace(event, {{ $i }})">
                                    @endfor
                                </div>

                                <button type="submit" class="btn btn-verify" id="verifyBtn">
                                    <i class="fas fa-check-circle me-2"></i> Verify OTP
                                </button>
                            </form>

                            <div class="resend-section">
                                <p class="text-muted mb-2">Didn't receive the code?</p>
                                <form action="{{ route('otp.resend') }}" method="POST" id="resendForm">
                                    @csrf
                                    <input type="hidden" name="purpose" value="{{ $purpose }}">
                                    <button type="submit" class="btn btn-resend" id="resendBtn" disabled>
                                        Resend OTP <span id="resendTimer">(60)</span>
                                    </button>
                                </form>
                            </div>

                            <div class="text-center">
                                <a href="{{ route('login') }}" class="back-link">
                                    <i class="fas fa-arrow-left me-2"></i> Back to Login
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Combine OTP inputs
        document.getElementById('otpForm').addEventListener('submit', function(e) {
            e.preventDefault();

            let otp = '';
            const inputs = document.querySelectorAll('.otp-input');
            inputs.forEach(input => {
                otp += input.value;
                input.classList.add('filled');
            });

            if (otp.length === 6) {
                // Show loading alert
                Swal.fire({
                    title: 'Verifying...',
                    html: 'Please wait while we verify your OTP',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                document.getElementById('otpCode').value = otp;
                this.submit();
            } else {
                // Shake animation for incomplete OTP
                document.querySelector('.otp-inputs').classList.add('shake');
                setTimeout(() => {
                    document.querySelector('.otp-inputs').classList.remove('shake');
                }, 300);

                // Show error alert
                Swal.fire({
                    icon: 'error',
                    title: 'Incomplete OTP',
                    text: 'Please enter all 6 digits of the verification code',
                    confirmButtonColor: '#3182ce',
                    timer: 3000,
                    timerProgressBar: true
                });
            }
        });

        // Auto-move to next input
        function moveToNext(input, index) {
            input.value = input.value.replace(/[^0-9]/g, '');

            if (input.value.length === 1) {
                input.classList.add('filled');
                if (index < 6) {
                    const nextInput = document.querySelector(`.otp-input[data-index="${index + 1}"]`);
                    nextInput.focus();
                }
            } else {
                input.classList.remove('filled');
            }

            updateVerifyButton();
        }

        // Handle backspace
        function handleBackspace(e, index) {
            if (e.key === 'Backspace' && !e.target.value && index > 1) {
                const prevInput = document.querySelector(`.otp-input[data-index="${index - 1}"]`);
                prevInput.focus();
                prevInput.value = '';
                prevInput.classList.remove('filled');
            }
        }

        // Update verify button state
        function updateVerifyButton() {
            const inputs = document.querySelectorAll('.otp-input');
            const filled = Array.from(inputs).every(input => input.value.length === 1);
            document.getElementById('verifyBtn').disabled = !filled;
        }

        // Timer for OTP expiry
        let totalSeconds = 60; // 1 minute
        const timerElement = document.getElementById('timer');
        const verifyBtn = document.getElementById('verifyBtn');

        function updateTimer() {
            if (totalSeconds <= 0) {
                timerElement.textContent = "Expired!";
                verifyBtn.disabled = true;
                verifyBtn.innerHTML = '<i class="fas fa-clock me-2"></i> OTP Expired';

                // Show expiry alert
                Swal.fire({
                    icon: 'warning',
                    title: 'OTP Expired',
                    text: 'Your verification code has expired. Please request a new one.',
                    confirmButtonColor: '#f59e0b',
                    timer: 5000,
                    timerProgressBar: true
                });
                return;
            }

            const minutes = Math.floor(totalSeconds / 60);
            const seconds = totalSeconds % 60;
            timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            totalSeconds--;
        }

        setInterval(updateTimer, 1000);

        // Resend timer
        let resendSeconds = 60;
        const resendBtn = document.getElementById('resendBtn');
        const resendTimer = document.getElementById('resendTimer');

        function updateResendTimer() {
            if (resendSeconds <= 0) {
                resendBtn.disabled = false;
                resendBtn.innerHTML = 'Resend OTP';
                resendTimer.textContent = '';
                return;
            }

            resendTimer.textContent = `(${resendSeconds})`;
            resendSeconds--;
        }

        setInterval(updateResendTimer, 1000);

        // Focus on first OTP input
        document.querySelector('.otp-input[data-index="1"]')?.focus();

        // Handle resend form submission
        document.getElementById('resendForm').addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Resending Code',
                text: 'Please wait while we send a new verification code',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            this.submit();
        });
    </script>

    <script>
        // SweetAlert notifications for session messages
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#3182ce',
                confirmButtonText: 'Continue',
                timer: 5000,
                timerProgressBar: true,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Verification Failed',
                text: "{{ session('error') }}",
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Try Again',
                timer: 5000,
                timerProgressBar: true,
                showClass: {
                    popup: 'animate__animated animate__shake'
                }
            });
        @endif

        @if (session('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Notice',
                text: "{{ session('warning') }}",
                confirmButtonColor: '#f59e0b',
                confirmButtonText: 'OK',
                timer: 5000,
                timerProgressBar: true,
                showClass: {
                    popup: 'animate__animated animate__pulse'
                }
            });
        @endif

        @if (session('info'))
            Swal.fire({
                icon: 'info',
                title: 'Information',
                text: "{{ session('info') }}",
                confirmButtonColor: '#17a2b8',
                confirmButtonText: 'Got it',
                timer: 5000,
                timerProgressBar: true
            });
        @endif

        // Handle any validation errors
        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                html: '<ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'OK'
            });
        @endif
    </script>

    <!-- Add Animate.css for better animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
</body>

</html>
