<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access · Sona DMS (enterprise)</title>

    <!-- Bootstrap 5 (clean, minimal) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 Pro (professional icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 (elegant feedback) -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Google Font: Inter only – crisp, corporate -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300..600&display=swap" rel="stylesheet">

    <style>
        /* ---------- global reset / professional base ---------- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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

        .login-container {
            max-width: 432px;
            width: 100%;
            margin: 0 auto;
            position: relative;
            z-index: 10;
        }

        /* ---------- main card – premium, quiet, precise ---------- */
        .login-card {
            background: #ffffff;
            border-radius: 28px;
            box-shadow:
                0 30px 60px -12px rgba(0, 0, 0, 0.4),
                0 4px 24px rgba(0, 30, 60, 0.3);
            transition: all 0.2s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.98);
        }

        /* header – deep navy, minimal, strong */
        .login-header {
            background: #0c2a44;
            padding: 2.2rem 2.2rem 1.8rem;
            border-bottom: 1px solid #203956;
            position: relative;
            overflow: hidden;
        }

        /* Subtle header pattern */
        .login-header::after {
            content: "";
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.03) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        /* logo as refined wordmark + emblem */
        .logo-emblem {
            display: flex;
            align-items: center;
            gap: 14px;
            position: relative;
            z-index: 2;
        }

        .logo-icon {
            background: linear-gradient(135deg, #1f3f5f 0%, #143250 100%);
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 16px -8px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logo-icon i {
            font-size: 2.2rem;
            color: #ffffff;
            opacity: 0.95;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
        }

        .logo-text h1 {
            font-weight: 550;
            font-size: 1.9rem;
            letter-spacing: -0.02em;
            color: white;
            margin: 0;
            line-height: 1.2;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .logo-text span {
            font-size: 0.78rem;
            font-weight: 400;
            color: #9fb5d4;
            display: block;
            margin-top: 2px;
            letter-spacing: 0.3px;
        }

        .login-header p {
            font-size: 0.9rem;
            color: #cbd9ec;
            margin: 1.2rem 0 0;
            padding-left: 0;
            font-weight: 350;
            border-left: 2px solid #3085d6;
            padding-left: 1rem;
            position: relative;
            z-index: 2;
        }

        /* ---------- body – clean, spacious ---------- */
        .login-body {
            padding: 2.2rem;
            background: white;
            position: relative;
            z-index: 2;
        }

        .form-label {
            font-weight: 500;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #3a5670;
            margin-bottom: 0.5rem;
        }

        /* input group – sharp, no nonsense */
        .input-group {
            border-radius: 14px;
            overflow: hidden;
            border: 1.5px solid #dae3ec;
            background: #ffffff;
            transition: border 0.15s ease, box-shadow 0.15s ease;
        }

        .input-group:focus-within {
            border-color: #2b6e9c;
            box-shadow: 0 0 0 4px rgba(38, 97, 156, 0.15);
        }

        .input-group-text {
            background: #f0f5fc;
            border: none;
            color: #2f5575;
            font-size: 1.2rem;
            padding: 0 1.2rem;
            border-right: 1.5px solid #dae3ec;
        }

        .form-control {
            border: none;
            padding: 0.9rem 1rem;
            font-size: 1rem;
            background: #ffffff;
            font-weight: 400;
            color: #142a40;
        }

        .form-control::placeholder {
            color: #9cb1c9;
            font-weight: 300;
            font-size: 0.95rem;
        }

        .form-control:focus {
            box-shadow: none;
            background: #ffffff;
        }

        /* primary CTA – solid, trustworthy */
        .btn-login {
            background: linear-gradient(145deg, #1b4c7c 0%, #0f3d63 100%);
            border: none;
            color: white;
            padding: 0.95rem 1.2rem;
            border-radius: 14px;
            font-weight: 550;
            font-size: 1rem;
            width: 100%;
            transition: all 0.2s;
            margin: 0.7rem 0 0.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            letter-spacing: 0.2px;
            box-shadow: 0 8px 18px -8px #1b4c7c;
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover:not(:disabled) {
            background: linear-gradient(145deg, #0f3d63 0%, #0a3150 100%);
            box-shadow: 0 12px 24px -10px #0a3150;
            transform: translateY(-1px);
        }

        .btn-login:active:not(:disabled) {
            transform: translateY(1px);
            box-shadow: 0 4px 12px -6px #0a3150;
        }

        .btn-login:disabled {
            opacity: 0.8;
            cursor: not-allowed;
        }

        .security-badge {
            background: #f0f7ff;
            border-radius: 40px;
            padding: 0.55rem 1.2rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #1d4e7c;
            font-size: 0.8rem;
            font-weight: 500;
            border: 1px solid #caddf0;
            margin: 0.5rem 0 0.2rem;
        }

        .security-badge i {
            color: #2a6b9e;
            font-size: 1rem;
        }

        .login-footer {
            padding: 1.2rem 2.2rem 1.8rem;
            background: #f9fcff;
            border-top: 1px solid #e1eaf2;
            color: #456f94;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 2;
        }

        .login-footer i {
            color: #3d74a0;
            width: 1.2rem;
        }

        /* help text */
        small.form-text {
            color: #5a7797 !important;
            font-size: 0.78rem;
            margin-top: 0.6rem;
        }

        /* hide standard flash alerts – we use swal */
        .alert {
            display: none;
        }

        /* subtle fade */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-card {
            animation: fadeUp 0.5s ease-out;
        }

        @media (max-width: 460px) {
            .login-header {
                padding: 1.8rem 1.5rem;
            }

            .login-body {
                padding: 1.8rem 1.5rem;
            }

            .login-footer {
                padding: 1.2rem 1.5rem;
                flex-wrap: wrap;
                gap: 8px;
            }
        }
    </style>
</head>

<body class="has-pattern">
    <div class="login-container">
        <div class="login-card">

            <!-- HEADER – strong, professional -->
            <div class="login-header">
                <div class="logo-emblem">
                    <div class="logo-icon">
                        <!-- Option 1: If you have the actual image file -->
                        <!-- <img src="{{ asset('assets/images/Sona-star-logo.png') }}" alt="Sona DMS Logo" style="width: 32px; height: 32px; object-fit: contain;"> -->

                        <!-- Option 2: Font Awesome star as fallback/alternative -->
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="logo-text">
                        <h1>Sona DMS</h1>
                    </div>
                </div>

            </div>

            <!-- BODY – only essential form, feedback via swal -->
            <div class="login-body">
                <!-- hidden flash containers (server messages) -->
                @if (session('error'))
                    <div id="flash-error" data-message="{{ session('error') }}" style="display: none;"></div>
                @endif
                @if (session('success'))
                    <div id="flash-success" data-message="{{ session('success') }}" style="display: none;"></div>
                @endif

                <form action="{{ route('login') }}" method="POST" id="loginForm">
                    @csrf
                    <div class="mb-4">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" name="email" value="{{ old('email') }}"
                                placeholder="your.email@company.com" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text text-muted mt-2 d-block">
                            <i class="fas fa-info-circle me-1"></i>
                            We'll send a secure OTP to your registered email
                        </small>
                    </div>

                    <button type="submit" class="btn btn-login" id="submitBtn">
                        <i class="fas fa-paper-plane me-2"></i> Send OTP
                    </button>


                </form>
            </div>

            <!-- FOOTER – corporate, subtle -->
            <div class="login-footer">
                <div>
                    <i class="fa-regular fa-copyright"></i>
                    2026 Sona DMS
                </div>

            </div>
        </div>
    </div>

    <!-- scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        (function() {
            "use strict";

            // Utility function to show SweetAlert notifications
            function showAlert(icon, title, message, timer = 5000) {
                Swal.fire({
                    icon: icon,
                    title: title,
                    text: message,
                    confirmButtonText: 'Dismiss',
                    confirmButtonColor: '#1b4c7c',
                    background: '#ffffff',
                    customClass: {
                        popup: 'rounded-4',
                        confirmButton: 'btn btn-primary px-4 py-2 rounded-3',
                    },
                    buttonsStyling: false,
                    timer: timer,
                    timerProgressBar: true,
                });
            }

            // 1) Show flash messages from server
            const errorFlash = document.getElementById('flash-error');
            const successFlash = document.getElementById('flash-success');

            if (errorFlash) {
                const msg = errorFlash.getAttribute('data-message');
                showAlert('error', 'Unable to proceed', msg);
            }

            if (successFlash) {
                const msg = successFlash.getAttribute('data-message');
                showAlert('success', 'Success', msg);
            }

            // 2) Form handling with validation
            const form = document.getElementById('loginForm');
            const emailInput = document.getElementById('email');
            const submitBtn = document.getElementById('submitBtn');

            // Focus email field if no flash messages are showing
            if (!errorFlash && !successFlash && emailInput) {
                emailInput.focus();
            }

            // Email validation pattern
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            // Prevent multiple form submissions
            let isSubmitting = false;

            if (form) {
                form.addEventListener('submit', async function(e) {
                    // Prevent double submission
                    if (isSubmitting) {
                        e.preventDefault();
                        return;
                    }

                    const email = emailInput.value.trim();

                    // Client-side email validation
                    if (!email || !emailPattern.test(email)) {
                        e.preventDefault();

                        await Swal.fire({
                            icon: 'warning',
                            title: 'Invalid email address',
                            text: 'Please enter a valid business email (e.g., name@company.com)',
                            confirmButtonText: 'Got it',
                            confirmButtonColor: '#1b4c7c',
                            background: '#ffffff',
                            customClass: {
                                popup: 'rounded-4',
                                confirmButton: 'btn btn-primary px-4 py-2 rounded-3',
                            },
                            buttonsStyling: false,
                        });

                        emailInput.focus();
                        return;
                    }

                    // Set submitting flag
                    isSubmitting = true;

                    // Show loading alert
                    await Swal.fire({
                        title: 'Processing request',
                        text: 'Please wait while we generate your secure OTP',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                        customClass: {
                            popup: 'rounded-4',
                        },
                        background: '#ffffff',
                        showConfirmButton: false,
                    });

                    // Disable submit button
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `
                        <i class="fa-regular fa-spinner fa-spin"></i> 
                        <span>Sending OTP...</span>
                    `;

                    // Form will continue submitting (POST)
                    // Note: isSubmitting flag will reset on page reload
                });
            }

            // Re-enable submit button if form submission fails (page doesn't reload)
            window.addEventListener('pageshow', function() {
                isSubmitting = false;
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = `
                        <i class="fas fa-paper-plane me-2"></i> Send OTP
                    `;
                }
            });

        })();
    </script>

    <!-- SweetAlert2 theme tweaks – corporate, refined -->
    <style>
        .swal2-popup {
            font-family: 'Inter', sans-serif;
            border-radius: 24px !important;
            padding: 1.2rem 1rem !important;
            box-shadow: 0 30px 60px rgba(0, 20, 40, 0.3) !important;
            border: 1px solid #e7edf5 !important;
        }

        .swal2-title {
            font-weight: 550;
            color: #0c2a44;
            font-size: 1.4rem;
        }

        .swal2-html-container {
            color: #365a7c;
            font-size: 1rem;
        }

        .swal2-confirm {
            border-radius: 40px !important;
            padding: 0.6rem 2rem !important;
            font-weight: 500;
            font-size: 0.9rem;
            background: #1b4c7c !important;
            border: none !important;
            box-shadow: 0 6px 12px -6px #0f3d63 !important;
            transition: background 0.2s;
        }

        .swal2-confirm:hover {
            background: #0f3d63 !important;
        }

        .swal2-timer-progress-bar {
            background: #3085d6 !important;
        }

        .swal2-loading {
            border-color: #acc9e5 !important;
        }

        /* small adjustment for loader */
        .swal2-popup.swal2-loading {
            border: 1px solid #dbe6f0;
        }

        /* Bootstrap validation styling */
        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .form-control.is-invalid:focus {
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: 0.8rem;
            margin-top: 0.5rem;
        }
    </style>
</body>

</html>
