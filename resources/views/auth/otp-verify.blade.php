{{-- resources/views/auth/otp-verify.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - Verify OTP</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .otp-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            max-width: 450px;
            width: 90%;
        }

        .otp-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .otp-body {
            padding: 40px;
            background: white;
        }

        .otp-input {
            width: 50px;
            height: 60px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            margin: 0 5px;
        }

        .otp-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            outline: none;
        }

        .btn-verify {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            color: white;
            font-weight: 600;
            width: 100%;
            transition: transform 0.3s;
        }

        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-verify:disabled {
            opacity: 0.6;
            transform: none;
        }

        .timer {
            font-size: 1.2rem;
            font-weight: 600;
            color: #667eea;
        }

        .email-display {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
            font-size: 0.9rem;
            word-break: break-all;
        }
    </style>
</head>

<body>
    <div class="otp-card">
        <div class="otp-header">
            <h2><i class="bi bi-shield-check"></i> Verify OTP</h2>
            <p class="mb-0">Enter the 6-digit code sent to your email</p>
        </div>
        <div class="otp-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i> {{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="text-center mb-4">
                <div class="email-display mb-3">
                    <i class="bi bi-envelope me-2"></i>
                    {{ session('otp_user_id') ? App\Models\User::find(session('otp_user_id'))->email : 'your email' }}
                </div>

                @if (isset($latestOtp) && config('app.debug'))
                    <div class="alert alert-info small p-2 mb-3">
                        <strong>Debug:</strong> OTP: {{ $latestOtp->otp_code }} (expires:
                        {{ $latestOtp->expires_at->diffForHumans() }})
                    </div>
                @endif

                <div class="timer mb-2" id="timer">05:00</div>
                <small class="text-muted">Time remaining</small>
            </div>

            <form method="POST" action="{{ route('otp.verify') }}" id="otpForm">
                @csrf

                <div class="d-flex justify-content-center mb-4 flex-wrap gap-2">
                    <input type="text" class="otp-input" name="otp1" maxlength="1" pattern="[0-9]"
                        inputmode="numeric" autofocus>
                    <input type="text" class="otp-input" name="otp2" maxlength="1" pattern="[0-9]"
                        inputmode="numeric">
                    <input type="text" class="otp-input" name="otp3" maxlength="1" pattern="[0-9]"
                        inputmode="numeric">
                    <input type="text" class="otp-input" name="otp4" maxlength="1" pattern="[0-9]"
                        inputmode="numeric">
                    <input type="text" class="otp-input" name="otp5" maxlength="1" pattern="[0-9]"
                        inputmode="numeric">
                    <input type="text" class="otp-input" name="otp6" maxlength="1" pattern="[0-9]"
                        inputmode="numeric">
                </div>
                <input type="hidden" name="otp" id="otp_hidden">

                <button type="submit" class="btn btn-verify mb-3" id="verifyBtn">
                    <i class="bi bi-check-circle me-2"></i> Verify OTP
                </button>
            </form>

            <div class="text-center">
                <form method="POST" action="{{ route('otp.resend') }}" class="d-inline" id="resendForm">
                    @csrf
                    <button type="submit" class="btn btn-link" id="resendBtn" disabled>
                        <i class="bi bi-arrow-repeat me-1"></i> Resend OTP
                    </button>
                </form>

                <div class="mt-3">
                    <a href="{{ route('login') }}" class="text-decoration-none small">
                        <i class="bi bi-arrow-left"></i> Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-move to next input
        const inputs = document.querySelectorAll('.otp-input');
        const otpHidden = document.getElementById('otp_hidden');
        const verifyBtn = document.getElementById('verifyBtn');
        const resendBtn = document.getElementById('resendBtn');
        const resendForm = document.getElementById('resendForm');

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

        // Timer (5 minutes = 300 seconds)
        let timeLeft = 300;
        const timerElement = document.getElementById('timer');

        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                timerElement.textContent = '00:00';
                verifyBtn.disabled = true;
                resendBtn.disabled = false;

                // Show expired message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-warning alert-dismissible fade show mt-3';
                alertDiv.setAttribute('role', 'alert');
                alertDiv.innerHTML =
                    '<i class="bi bi-exclamation-triangle me-2"></i> OTP expired. Please request a new one.<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                document.querySelector('.otp-body').insertBefore(alertDiv, document.querySelector('.text-center')
                    .nextSibling);
            }
            timeLeft--;
        }

        const timerInterval = setInterval(updateTimer, 1000);
        updateTimer();

        // Prevent form resubmission on page refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>

</html>
