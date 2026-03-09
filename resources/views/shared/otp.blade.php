<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OTP Approval - Secure DMS</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        
        body {
            background: linear-gradient(rgba(26, 54, 93, 0.9), rgba(26, 54, 93, 0.9)), 
                        url('https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .otp-container {
            max-width: 520px;
            margin: 0 auto;
        }
        
        .otp-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }
        
        .otp-header {
            background: linear-gradient(135deg, #1a365d 0%, #2d3748 100%);
            color: white;
            padding: 35px 30px;
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
        
        .otp-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #ebf8ff 0%, #bee3f8 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            border: 3px solid #3182ce;
        }
        
        .otp-icon i {
            color: #3182ce;
            font-size: 36px;
        }
        
        .file-info-card {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
            border-left: 4px solid #3182ce;
        }
        
        .file-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #3182ce 0%, #2b6cb0 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            margin-right: 15px;
        }
        
        .file-details h6 {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 5px;
        }
        
        .file-details small {
            color: #718096;
            font-size: 14px;
        }
        
        .otp-inputs {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin: 25px 0;
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
        
        .btn-resend {
            background: transparent;
            border: 1px solid #e2e8f0;
            color: #3182ce;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-resend:hover {
            background: #ebf8ff;
            border-color: #3182ce;
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
        
        .alert-success {
            background: #f0fff4;
            color: #38a169;
            border-left: 4px solid #68d391;
        }
        
        .alert-warning {
            background: #fffaf0;
            color: #b7791f;
            border-left: 4px solid #feebc8;
        }
        
        .alert-info {
            background: #ebf8ff;
            color: #3182ce;
            border-left: 4px solid #bee3f8;
        }
        
        .timer {
            font-weight: 700;
            color: #3182ce;
        }
        
        .shake {
            animation: shake 0.3s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
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
                            <h3><i class="fas fa-shield-check"></i> OTP Approval Required</h3>
                            <p>File owner approval needed to access this file</p>
                        </div>
                        
                        <div class="otp-body">
                            @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            @endif

                            @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            @endif

                            @if(session('warning'))
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ session('warning') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            @endif

                            <div class="otp-icon">
                                <i class="fas fa-lock"></i>
                            </div>
                            
                            <h5 class="text-center mb-3">Secure File Access</h5>
                            <p class="text-center text-muted mb-4">
                                The file owner will receive an OTP to approve your access request
                            </p>

                            <div class="file-info-card">
                                <div class="d-flex align-items-center">
                                    <div class="file-icon">
                                        @if($share->file->extension === 'pdf')
                                        <i class="fas fa-file-pdf"></i>
                                        @elseif(in_array($share->file->extension, ['doc', 'docx']))
                                        <i class="fas fa-file-word"></i>
                                        @elseif(in_array($share->file->extension, ['xls', 'xlsx']))
                                        <i class="fas fa-file-excel"></i>
                                        @else
                                        <i class="fas fa-file"></i>
                                        @endif
                                    </div>
                                    <div class="file-details">
                                        <h6>{{ $share->file->original_name }}</h6>
                                        <small>
                                            <i class="fas fa-user me-1"></i> Shared by: {{ $share->sharedBy->name }}
                                            <br>
                                            <i class="fas fa-clock me-1"></i> OTP valid for: <span id="timer" class="timer">15:00</span>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <form action="{{ route('shared.otp.verify', $share->share_token) }}" method="POST" id="otpForm">
                                @csrf
                                
                                <label class="form-label fw-medium mb-3">Enter OTP sent to file owner:</label>
                                <div class="otp-inputs">
                                    @for($i = 0; $i < 6; $i++)
                                    <input type="text"
                                           class="otp-input form-control"
                                           maxlength="1"
                                           name="otp[]"
                                           id="otp{{ $i }}"
                                           data-index="{{ $i }}"
                                           oninput="moveToNext(this, {{ $i }})"
                                           onkeydown="handleBackspace(event, {{ $i }})">
                                    @endfor
                                </div>

                                <button type="submit" class="btn btn-verify" id="verifyBtn">
                                    <i class="fas fa-check-circle me-2"></i> Verify OTP & Access File
                                </button>
                            </form>

                            <div class="text-center mt-4">
                                <form action="{{ route('shared.otp.request', $share->share_token) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-resend">
                                        <i class="fas fa-redo me-2"></i> Resend OTP to Owner
                                    </button>
                                </form>
                            </div>

                            <div class="alert alert-info mt-4">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Note:</strong> The OTP is sent to the file owner's registered email. 
                                You cannot access the file without their approval.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-move to next input
        function moveToNext(input, index) {
            // Only allow numbers
            input.value = input.value.replace(/[^0-9]/g, '');
            
            if (input.value.length === 1) {
                input.classList.add('filled');
                if (index < 5) {
                    const nextInput = document.querySelector(`#otp${index + 1}`);
                    nextInput.focus();
                }
            } else {
                input.classList.remove('filled');
            }
            
            updateVerifyButton();
        }

        // Handle backspace
        function handleBackspace(e, index) {
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                e.preventDefault();
                const prevInput = document.querySelector(`#otp${index - 1}`);
                prevInput.focus();
                prevInput.value = '';
                prevInput.classList.remove('filled');
            }
        }

        // Update verify button state
        function updateVerifyButton() {
            const inputs = document.querySelectorAll('.otp-input');
            let allFilled = true;
            
            inputs.forEach(input => {
                if (input.value.length !== 1) {
                    allFilled = false;
                }
            });
            
            document.getElementById('verifyBtn').disabled = !allFilled;
        }

        // Handle form submission validation
        document.getElementById('otpForm').addEventListener('submit', function(e) {
            const inputs = document.querySelectorAll('.otp-input');
            let otpComplete = true;
            
            inputs.forEach(input => {
                if (input.value.length !== 1) {
                    otpComplete = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });
            
            if (!otpComplete) {
                e.preventDefault();
                document.querySelector('.otp-inputs').classList.add('shake');
                setTimeout(() => {
                    document.querySelector('.otp-inputs').classList.remove('shake');
                }, 300);
                
                // Show error message
                const existingError = document.querySelector('.otp-error');
                if (existingError) {
                    existingError.remove();
                }
                
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger mt-3 otp-error';
                errorDiv.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>Please enter all 6 digits of the OTP';
                document.querySelector('.otp-inputs').parentNode.insertBefore(errorDiv, document.querySelector('.otp-inputs').nextSibling);
                
                // Auto-remove error after 5 seconds
                setTimeout(() => {
                    errorDiv.remove();
                }, 5000);
            }
        });

        // Timer for OTP expiry
        let totalSeconds = 900; // 15 minutes
        const timerElement = document.getElementById('timer');
        const verifyBtn = document.getElementById('verifyBtn');
        
        function updateTimer() {
            if (totalSeconds <= 0) {
                timerElement.textContent = "Expired!";
                verifyBtn.disabled = true;
                verifyBtn.innerHTML = '<i class="fas fa-clock me-2"></i> OTP Expired';
                verifyBtn.classList.add('disabled');
                return;
            }
            
            const minutes = Math.floor(totalSeconds / 60);
            const seconds = totalSeconds % 60;
            timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            totalSeconds--;
        }
        
        setInterval(updateTimer, 1000);

        // Auto-dismiss alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert:not(.otp-error)');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
        // Focus on first OTP input on page load
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('otp0')?.focus();
        });
        
        // Auto-move on paste
        document.addEventListener('paste', function(e) {
            if (e.target.classList.contains('otp-input')) {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text').trim();
                if (/^\d{6}$/.test(pastedData)) {
                    const inputs = document.querySelectorAll('.otp-input');
                    pastedData.split('').forEach((digit, index) => {
                        if (inputs[index]) {
                            inputs[index].value = digit;
                            inputs[index].classList.add('filled');
                        }
                    });
                    updateVerifyButton();
                }
            }
        });
        
        // Validate each input on blur
        document.querySelectorAll('.otp-input').forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value && !/^\d$/.test(this.value)) {
                    this.value = '';
                    this.classList.remove('filled');
                    updateVerifyButton();
                }
            });
        });
    </script>
</body>
</html>