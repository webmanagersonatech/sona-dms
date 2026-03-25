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

inputs.forEach((input,i)=>{
    input.addEventListener('input',()=>{
        if(input.value && i<5) inputs[i+1].focus();
        update();
    });
});

function update(){
    let val='';
    inputs.forEach(i=>val+=i.value);
    hidden.value = val;
}

let time=300;
setInterval(()=>{
    let m=Math.floor(time/60);
    let s=time%60;
    document.getElementById('timer').innerText =
        `${m.toString().padStart(2,'0')}:${s.toString().padStart(2,'0')}`;
    time--;
},1000);
</script>
@endpush