<?php

namespace App\Rules;

use App\Models\Otp;
use Illuminate\Contracts\Validation\Rule;

class ValidOTP implements Rule
{
    protected $email;
    protected $purpose;

    public function __construct($email, $purpose = null)
    {
        $this->email = $email;
        $this->purpose = $purpose;
    }

    public function passes($attribute, $value)
    {
        $otp = Otp::where('email', $this->email)
            ->where('otp_code', $value)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otp) {
            return false;
        }

        if ($this->purpose && $otp->purpose !== $this->purpose) {
            return false;
        }

        return true;
    }

    public function message()
    {
        return 'The OTP is invalid or expired.';
    }
}