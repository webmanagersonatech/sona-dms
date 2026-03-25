@extends('layouts.app')

@section('title', 'Recovery Codes')

@section('content')
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-key"></i> Two-Factor Recovery Codes
                    </h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Important!</strong> Store these recovery codes in a safe place.
                        Each code can only be used once. If you lose access to your authenticator app,
                        you can use these codes to log in.
                    </div>

                    <div class="recovery-codes p-4 bg-light rounded mb-4">
                        <div class="row">
                            @foreach ($recoveryCodes as $index => $code)
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-secondary me-2">{{ $index + 1 }}</span>
                                        <code class="fs-5">{{ substr($code, 0, 4) }}-{{ substr($code, 4, 4) }}</code>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <form method="POST" action="{{ route('twofactor.recovery-codes.regenerate') }}"
                            onsubmit="return confirm('Regenerating codes will invalidate your old codes. Continue?')">
                            @csrf
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-arrow-repeat"></i> Regenerate Codes
                            </button>
                        </form>

                        <button class="btn btn-primary" onclick="printCodes()">
                            <i class="bi bi-printer"></i> Print Codes
                        </button>

                        <button class="btn btn-success" onclick="downloadCodes()">
                            <i class="bi bi-download"></i> Download Codes
                        </button>
                    </div>

                    <hr>

                    <div class="text-center">
                        <a href="{{ route('settings.security') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Security Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden printable version -->
    <div id="printable" style="display: none;">
        <div style="text-align: center; padding: 40px;">
            <h1>{{ config('app.name') }} - Recovery Codes</h1>
            <p>Generated on: {{ now()->format('F d, Y H:i:s') }}</p>
            <p>User: {{ auth()->user()->name }} ({{ auth()->user()->email }})</p>

            <div style="margin-top: 40px;">
                @foreach ($recoveryCodes as $index => $code)
                    <div style="margin: 10px; font-family: monospace; font-size: 18px;">
                        {{ $index + 1 }}. {{ substr($code, 0, 4) }}-{{ substr($code, 4, 4) }}
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 60px; font-style: italic;">
                <p>Store these codes securely. Each code can only be used once.</p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .recovery-codes code {
            background: white;
            padding: 8px 15px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function printCodes() {
            const printContent = document.getElementById('printable').innerHTML;
            const originalContent = document.body.innerHTML;

            document.body.innerHTML = printContent;
            window.print();
            document.body.innerHTML = originalContent;
            location.reload();
        }

        function downloadCodes() {
            let content = "{{ config('app.name') }} - Recovery Codes\n";
            content += "Generated on: {{ now()->format('F d, Y H:i:s') }}\n";
            content += "User: {{ auth()->user()->name }} ({{ auth()->user()->email }})\n\n";

            @foreach ($recoveryCodes as $index => $code)
                content += "{{ $index + 1 }}. {{ substr($code, 0, 4) }}-{{ substr($code, 4, 4) }}\n";
            @endforeach

            const blob = new Blob([content], {
                type: 'text/plain'
            });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'recovery-codes.txt';
            a.click();
            window.URL.revokeObjectURL(url);
        }
    </script>
@endpush
