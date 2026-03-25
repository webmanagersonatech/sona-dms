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

// password match
document.getElementById('confirm').addEventListener('input',function(){
    let p = document.getElementById('password').value;
    let c = this.value;
    let msg = document.getElementById('match');

    msg.innerHTML = (p===c)
        ? '<span class="text-success">Match</span>'
        : '<span class="text-danger">Not Match</span>';
});
</script>
@endpush