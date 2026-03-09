<?php

return [
    'expiry_minutes' => env('OTP_EXPIRY_MINUTES', 10),
    'max_attempts' => env('OTP_MAX_ATTEMPTS', 3),
    'resend_limit' => env('OTP_RESEND_LIMIT', 5),
    'length' => env('OTP_LENGTH', 6),
];