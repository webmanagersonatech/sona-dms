<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use App\Models\OtpLog;
use App\Services\BrevoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FAQRCode\Google2FA;
use Jenssegers\Agent\Agent;

class TwoFactorController extends Controller
{
    protected $google2fa;
    protected $brevoService;

    public function __construct(Google2FA $google2fa, BrevoService $brevoService)
    {
        $this->google2fa = $google2fa;
        $this->brevoService = $brevoService;
    }

    public function showSetup()
    {
        $user = Auth::user();

        if ($user->two_factor_enabled) {
            return redirect()->route('settings.security')
                ->with('info', 'Two-factor authentication is already enabled.');
        }

        // Generate secret key
        $secret = $this->google2fa->generateSecretKey();
        session(['2fa_secret' => $secret]);

        // Generate QR code
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return view('auth.twofactor.setup', compact('secret', 'qrCodeUrl'));
    }

    public function enable(Request $request)
    {
        $request->validate([
            'secret' => 'required|string',
            'otp' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        $secret = $request->secret;

        // Verify OTP
        $valid = $this->google2fa->verifyKey($secret, $request->otp);

        if (!$valid) {
            return back()->withErrors(['otp' => 'Invalid verification code.']);
        }

        // Generate recovery codes
        $recoveryCodes = $this->generateRecoveryCodes();

        $user->update([
            'two_factor_enabled' => true,
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
        ]);

        session()->forget('2fa_secret');

        $agent = new Agent();
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'enable_2fa',
            'module' => 'security',
            'description' => 'Enabled two-factor authentication',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_type' => $agent->isMobile() ? 'mobile' : ($agent->isTablet() ? 'tablet' : 'desktop'),
        ]);

        return view('auth.twofactor.recovery-codes', compact('recoveryCodes'));
    }

    public function showDisable()
    {
        $user = Auth::user();

        if (!$user->two_factor_enabled) {
            return redirect()->route('settings.security');
        }

        return view('auth.twofactor.disable');
    }

    public function disable(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        $secret = decrypt($user->two_factor_secret);

        // Verify OTP
        $valid = $this->google2fa->verifyKey($secret, $request->otp);

        if (!$valid) {
            return back()->withErrors(['otp' => 'Invalid verification code.']);
        }

        $user->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ]);

        $agent = new Agent();
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'disable_2fa',
            'module' => 'security',
            'description' => 'Disabled two-factor authentication',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_type' => $agent->isMobile() ? 'mobile' : ($agent->isTablet() ? 'tablet' : 'desktop'),
        ]);

        return redirect()->route('settings.security')
            ->with('success', 'Two-factor authentication disabled successfully.');
    }

    public function showChallenge()
    {
        if (!session('2fa:user:id')) {
            return redirect()->route('login');
        }

        return view('auth.twofactor.challenge');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|string',
        ]);

        $userId = session('2fa:user:id');

        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if (!$user || !$user->two_factor_enabled) {
            return redirect()->route('login');
        }

        $secret = decrypt($user->two_factor_secret);

        // Check if it's a recovery code
        $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

        if (in_array($request->otp, $recoveryCodes)) {
            // Remove used recovery code
            $recoveryCodes = array_values(array_diff($recoveryCodes, [$request->otp]));
            $user->update([
                'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes))
            ]);
            $valid = true;
        } else {
            // Verify TOTP
            $valid = $this->google2fa->verifyKey($secret, $request->otp);
        }

        if (!$valid) {
            return back()->withErrors(['otp' => 'Invalid verification code.']);
        }

        // Login the user
        Auth::login($user);
        session()->forget('2fa:user:id');

        $agent = new Agent();
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => '2fa_verified',
            'module' => 'auth',
            'description' => 'Two-factor authentication verified',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_type' => $agent->isMobile() ? 'mobile' : ($agent->isTablet() ? 'tablet' : 'desktop'),
        ]);

        return redirect()->intended('dashboard');
    }

    public function showRecoveryCodes()
    {
        $user = Auth::user();

        if (!$user->two_factor_enabled) {
            return redirect()->route('settings.security');
        }

        $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

        return view('auth.twofactor.recovery-codes', compact('recoveryCodes'));
    }

    public function regenerateRecoveryCodes(Request $request)
    {
        $user = Auth::user();

        if (!$user->two_factor_enabled) {
            return redirect()->route('settings.security');
        }

        $recoveryCodes = $this->generateRecoveryCodes();

        $user->update([
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes))
        ]);

        $agent = new Agent();
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'regenerate_recovery_codes',
            'module' => 'security',
            'description' => 'Regenerated recovery codes',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_type' => $agent->isMobile() ? 'mobile' : ($agent->isTablet() ? 'tablet' : 'desktop'),
        ]);

        return view('auth.twofactor.recovery-codes', compact('recoveryCodes'))
            ->with('success', 'Recovery codes regenerated successfully.');
    }

    private function generateRecoveryCodes()
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(substr(md5(uniqid()), 0, 8));
        }
        return $codes;
    }
}