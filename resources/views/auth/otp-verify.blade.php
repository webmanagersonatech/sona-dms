@extends('layouts.guest')

@section('title', 'Verify OTP')
@section('subtitle', 'Enter OTP sent to your email')

@section('content')

<form method="POST" action="{{ route('otp.verify') }}" id="otpForm">
@csrf

<div class="text-center mb-3">
    <div class="timer" id="timer">05:00</div>
</div>

<div class="d-flex justify-content-center gap-2 mb-3">
    @for($i=0;$i<6;$i++)
        <input type="text" class="otp-input" maxlength="1">
    @endfor
</div>

<input type="hidden" name="otp" id="otp_hidden">

<button class="btn btn-auth w-100 mb-2">Verify OTP</button>

</form>

<div class="text-center">
    <form method="POST" action="{{ route('otp.resend') }}">
        @csrf
        <button class="btn btn-link link">Resend OTP</button>
    </form>

    <a href="{{ route('login') }}" class="link d-block mt-2">← Back</a>
</div>

@endsection

@push('scripts')
<script>
    const inputs = document.querySelectorAll('.otp-input');
    const hidden = document.getElementById('otp_hidden');

    inputs.forEach((input, index) => {
        // Handle input
        input.addEventListener('input', (e) => {
            if (e.inputType === 'deleteContentBackward') return;
            
            if (input.value && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
            update();
        });

        // Handle backspace
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !input.value && index > 0) {
                inputs[index - 1].focus();
            }
        });

        // Handle paste
        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const pasteData = e.clipboardData.getData('text').slice(0, inputs.length);
            if (!/^\d+$/.test(pasteData)) return;

            pasteData.split('').forEach((char, i) => {
                if (inputs[i]) inputs[i].value = char;
            });
            update();
            inputs[Math.min(pasteData.length, inputs.length - 1)].focus();
        });
    });

    function update() {
        let val = '';
        inputs.forEach(i => val += i.value);
        hidden.value = val;
    }

    // Timer Logic
    let time = 300; // 5 minutes
    const timerDisplay = document.getElementById('timer');
    
    const countdown = setInterval(() => {
        time--;
        if (time <= 0) {
            clearInterval(countdown);
            timerDisplay.innerText = "00:00";
            timerDisplay.style.color = "#a0aec0";
            return;
        }
        
        let m = Math.floor(time / 60);
        let s = time % 60;
        timerDisplay.innerText = `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
    }, 1000);

    // Initial focus
    inputs[0].focus();
</script>
@endpush