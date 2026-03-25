@extends('layouts.app')

@section('title', 'Setup Two-Factor Authentication')

@section('content')
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-shield-lock"></i> Setup Two-Factor Authentication
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        Two-factor authentication adds an extra layer of security to your account.
                        Once enabled, you'll need to enter a verification code from your authenticator app when logging in.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Step 1: Install an Authenticator App</h6>
                            <p class="text-muted">Download and install one of these authenticator apps on your phone:</p>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="bi bi-google text-primary"></i>
                                    <a href="#" target="_blank">Google Authenticator</a>
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-microsoft text-info"></i>
                                    <a href="#" target="_blank">Microsoft Authenticator</a>
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-shield-check text-success"></i>
                                    <a href="#" target="_blank">Authy</a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Step 2: Scan QR Code</h6>
                            <div class="text-center mb-3">
                                <div id="qrcode" class="d-inline-block p-3 border rounded"></div>
                            </div>
                            <p class="text-muted small">
                                Scan this QR code with your authenticator app, or manually enter the secret key below.
                            </p>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="mb-3">Step 3: Manual Setup (If you can't scan QR code)</h6>
                            <div class="bg-light p-3 rounded">
                                <p class="mb-2"><strong>Account:</strong> {{ auth()->user()->email }}</p>
                                <p class="mb-2"><strong>Secret Key:</strong></p>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="secret" value="{{ $secret }}"
                                        readonly>
                                    <button class="btn btn-outline-secondary" type="button" onclick="copySecret()">
                                        <i class="bi bi-clipboard"></i> Copy
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6 mx-auto">
                            <h6 class="mb-3">Step 4: Verify Setup</h6>
                            <form method="POST" action="{{ route('twofactor.enable') }}" id="verifyForm">
                                @csrf
                                <input type="hidden" name="secret" value="{{ $secret }}">

                                <div class="mb-3">
                                    <label for="otp" class="form-label">Enter 6-digit code from authenticator
                                        app</label>
                                    <input type="text" class="form-control text-center" id="otp" name="otp"
                                        maxlength="6" pattern="[0-9]{6}" inputmode="numeric" placeholder="000000" required
                                        autofocus>
                                </div>

                                @error('otp')
                                    <div class="alert alert-danger py-2">{{ $message }}</div>
                                @enderror

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-shield-check"></i> Enable Two-Factor Authentication
                                    </button>
                                    <a href="{{ route('settings.security') }}" class="btn btn-secondary">
                                        Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script>
        // Generate QR Code
        new QRCode(document.getElementById("qrcode"), {
            text: "{{ $qrCodeUrl }}",
            width: 200,
            height: 200
        });

        function copySecret() {
            const secretInput = document.getElementById('secret');
            secretInput.select();
            secretInput.setSelectionRange(0, 99999);
            document.execCommand('copy');

            // Show tooltip or alert
            alert('Secret key copied to clipboard!');
        }

        // Auto-submit when 6 digits are entered
        document.getElementById('otp').addEventListener('input', function() {
            if (this.value.length === 6) {
                document.getElementById('verifyForm').submit();
            }
        });
    </script>
@endpush
