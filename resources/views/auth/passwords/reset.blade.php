@extends('layouts.guest')

@section('title', 'Reset Password')
@section('subtitle', 'Enter OTP & new password')

@section('content')

<form method="POST" action="{{ route('password.update') }}" id="resetForm">
@csrf

{{-- OTP --}}
<div class="d-flex justify-content-center gap-2 mb-3">
    @for($i=0;$i<6;$i++)
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
    <div id="match" class="small"></div>
</div>

<button class="btn btn-auth w-100 mb-2">Reset Password</button>

</form>

<div class="text-center">
    <div class="timer" id="timer">30:00</div>
</div>

@endsection

@push('scripts')
<script>
    const inputs = document.querySelectorAll('.otp-input');
    const hidden = document.getElementById('otp_hidden');

    inputs.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            if (e.inputType === 'deleteContentBackward') return;
            if (input.value && index < inputs.length - 1) inputs[index + 1].focus();
            update();
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !input.value && index > 0) inputs[index - 1].focus();
        });

        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const pasteData = e.clipboardData.getData('text').slice(0, inputs.length);
            if (!/^\d+$/.test(pasteData)) return;
            pasteData.split('').forEach((char, i) => { if (inputs[i]) inputs[i].value = char; });
            update();
            inputs[Math.min(pasteData.length, inputs.length - 1)].focus();
        });
    });

    function update() {
        let val = '';
        inputs.forEach(i => val += i.value);
        hidden.value = val;
    }

    // password match logic
    const password = document.getElementById('password');
    const confirm = document.getElementById('confirm');
    const matchMsg = document.getElementById('match');

    confirm.addEventListener('input', () => {
        if (confirm.value === '') {
            matchMsg.innerHTML = '';
            return;
        }
        if (password.value === confirm.value) {
            matchMsg.innerHTML = '<span class="text-success fw-bold"><i class="bi bi-check-circle-fill"></i> Passwords Match</span>';
            confirm.style.borderColor = "#10b981";
        } else {
            matchMsg.innerHTML = '<span class="text-danger fw-bold"><i class="bi bi-x-circle-fill"></i> Passwords Do Not Match</span>';
            confirm.style.borderColor = "#ef4444";
        }
    });

    // Initial focus
    inputs[0].focus();
</script>
@endpush