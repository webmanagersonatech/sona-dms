<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Device Verification - Secure DMS</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

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

        .device-container {
            max-width: 550px;
            margin: 0 auto;
        }

        .device-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .device-card:hover {
            transform: translateY(-5px);
        }

        .device-header {
            background: linear-gradient(135deg, #1a365d 0%, #2d3748 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .device-header::before {
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

        .device-header h3 {
            font-weight: 600;
            font-size: 24px;
            margin-bottom: 8px;
            position: relative;
        }

        .device-header p {
            opacity: 0.9;
            font-size: 15px;
            margin: 0;
        }

        .device-body {
            padding: 40px 35px;
        }

        .security-alert {
            background: #fffaf0;
            border: 1px solid #feebc8;
            border-left: 4px solid #d69e2e;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .security-alert i {
            color: #d69e2e;
            margin-right: 12px;
        }

        .device-info-card {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
        }

        .device-info-card h5 {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
        }

        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 16px;
            padding: 12px 0;
            border-bottom: 1px solid #edf2f7;
        }

        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .info-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #3182ce 0%, #2b6cb0 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
        }

        .info-label {
            font-weight: 500;
            color: #4a5568;
            font-size: 14px;
        }

        .info-value {
            font-weight: 600;
            color: #2d3748;
            font-size: 15px;
        }

        .otp-section {
            margin: 30px 0;
        }

        .otp-section p {
            text-align: center;
            color: #718096;
            margin-bottom: 20px;
        }

        .otp-inputs {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin: 20px 0;
        }

        .otp-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 20px;
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
            margin-top: 10px;
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

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 25px;
        }

        .btn-resend,
        .btn-cancel {
            background: transparent;
            border: none;
            color: #718096;
            font-weight: 500;
            padding: 10px 20px;
            transition: all 0.3s ease;
            border-radius: 8px;
        }

        .btn-resend:hover {
            color: #3182ce;
            background: #ebf8ff;
        }

        .btn-cancel:hover {
            color: #e53e3e;
            background: #fff5f5;
        }

        /* Animation */
        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .pulse {
            animation: pulse 2s infinite;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="device-container">
                    <div class="device-card">
                        <div class="device-header">
                            <h3><i class="fas fa-laptop-shield"></i> New Device Detected</h3>
                            <p>Verify this device to continue</p>
                        </div>

                        <div class="device-body">
                            <div class="security-alert">
                                <i class="fas fa-exclamation-triangle fa-lg"></i>
                                <span class="fw-bold">Security Alert:</span> We detected a new device trying to access
                                your account.
                            </div>

                            <div class="device-info-card">
                                <h5><i class="fas fa-info-circle me-2"></i> Device Information</h5>

                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-desktop"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="info-label">Device Name</div>
                                        <div class="info-value">{{ $device['device_name'] }}</div>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-globe"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="info-label">Browser</div>
                                        <div class="info-value">{{ $device['browser'] }}</div>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-code-branch"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="info-label">Operating System</div>
                                        <div class="info-value">{{ $device['os'] }}</div>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-network-wired"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="info-label">IP Address</div>
                                        <div class="info-value" class="text-monospace">{{ $device['ip_address'] }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="otp-section">
                                <p class="text-center mb-3">
                                    <i class="fas fa-envelope text-primary me-2"></i>
                                    An OTP has been sent to your registered email for verification.
                                </p>

                                <form action="{{ route('device.verify') }}" method="POST" id="otpForm">
                                    @csrf

                                    <div class="otp-inputs">
                                        @for ($i = 1; $i <= 6; $i++)
                                            <input type="text" class="otp-input form-control" maxlength="1"
                                                data-index="{{ $i }}"
                                                oninput="moveToNext(this, {{ $i }})"
                                                onkeydown="handleBackspace(event, {{ $i }})">
                                        @endfor
                                    </div>

                                    <input type="hidden" name="otp_code" id="otpCode">

                                    <button type="submit" class="btn btn-verify" id="verifyBtn">
                                        <i class="fas fa-shield-check me-2"></i> Verify Device
                                    </button>
                                </form>
                            </div>

                            <div class="action-buttons">
                                <button type="button" class="btn btn-resend" id="resendOtpBtn">
                                    <i class="fas fa-redo me-1"></i> Resend OTP
                                </button>

                                <button type="button" class="btn btn-cancel" id="cancelLoginBtn">
                                    <i class="fas fa-times me-1"></i> Cancel Login
                                </button>
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
        // Check for session messages and show SweetAlert
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Verification Failed',
                text: '{{ session('error') }}',
                timer: 3000,
                showConfirmButton: true,
                confirmButtonColor: '#3182ce',
                background: '#fff',
                customClass: {
                    popup: 'rounded-4'
                }
            });
        @endif

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: true,
                confirmButtonColor: '#3182ce',
                background: '#fff',
                customClass: {
                    popup: 'rounded-4'
                }
            });
        @endif

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
                // Show loading state
                Swal.fire({
                    title: 'Verifying...',
                    text: 'Please wait while we verify your device.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                document.getElementById('otpCode').value = otp;
                this.submit();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete OTP',
                    text: 'Please enter all 6 digits of the OTP',
                    confirmButtonColor: '#3182ce',
                    background: '#fff',
                    customClass: {
                        popup: 'rounded-4'
                    }
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

        // Resend OTP
        document.getElementById('resendOtpBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Resend OTP?',
                text: 'A new verification code will be sent to your email',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3182ce',
                cancelButtonColor: '#718096',
                confirmButtonText: 'Yes, resend',
                cancelButtonText: 'Cancel',
                background: '#fff',
                customClass: {
                    popup: 'rounded-4'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Sending...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit the form
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('device.resend-otp') }}';
                    form.innerHTML = '@csrf';
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });

        // Cancel Login
        document.getElementById('cancelLoginBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Cancel Login?',
                text: 'Are you sure you want to cancel the login process?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e53e3e',
                cancelButtonColor: '#718096',
                confirmButtonText: 'Yes, cancel',
                cancelButtonText: 'No, continue',
                background: '#fff',
                customClass: {
                    popup: 'rounded-4'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit logout form
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('logout') }}';
                    form.innerHTML = '@csrf';
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });

        // Focus on first OTP input
        document.querySelector('.otp-input[data-index="1"]')?.focus();

        // Paste OTP functionality
        document.addEventListener('paste', function(e) {
            const paste = e.clipboardData.getData('text');
            if (paste && paste.length === 6 && /^\d+$/.test(paste)) {
                const inputs = document.querySelectorAll('.otp-input');
                inputs.forEach((input, index) => {
                    input.value = paste[index];
                    input.classList.add('filled');
                });
                updateVerifyButton();
            }
        });
    </script>
</body>

</html>
