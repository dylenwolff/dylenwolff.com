<?php

namespace App\Http\Controllers;

use App\Models\AdminLoginOtp;
use App\Models\User;
use App\Notifications\AdminLoginCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class WorkspaceAuthController extends Controller
{
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
    public function create(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('workspace.dashboard');
        }

        return view('workspace.login', ['codeSent' => (bool) $request->session()->get('workspace_otp_email')]);
    }

    public function sendCode(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email']]);
        $email = Str::lower(trim($data['email']));
        $key = 'workspace-otp-send:'.$request->ip().':'.$email;

        if (RateLimiter::tooManyAttempts($key, 3)) {
            return back()->withErrors(['email' => 'Too many codes were requested. Please wait '.RateLimiter::availableIn($key).' seconds.'])->withInput();
        }

        RateLimiter::hit($key, 600);
        $user = User::query()->whereRaw('LOWER(email) = ?', [$email])->first();

        if ($user && $user->canAccessWorkspace()) {
            $code = (string) random_int(100000, 999999);
            AdminLoginOtp::query()->where('email', $email)->whereNull('used_at')->delete();
            AdminLoginOtp::create(['email' => $email, 'code_hash' => $this->hashCode($email, $code), 'expires_at' => now()->addMinutes(10)]);
            $user->notify(new AdminLoginCode($code));
        }

        $request->session()->put('workspace_otp_email', $email);

        return back()->with('status', 'If the address is authorized, a six-digit login code has been sent.');
    }

    public function verify(Request $request): RedirectResponse
    {
        $email = Str::lower((string) $request->session()->get('workspace_otp_email'));
        $data = $request->validate(['code' => ['required', 'digits:6']]);
        $user = User::query()->whereRaw('LOWER(email) = ?', [$email])->first();
        $otp = AdminLoginOtp::query()->where('email', $email)->whereNull('used_at')->latest('id')->first();
        $valid = $user && $user->canAccessWorkspace() && $otp && $otp->expires_at->isFuture() && $otp->attempts < 5
            && hash_equals($otp->code_hash, $this->hashCode($email, $data['code']));

        if (! $valid) {
            if ($otp && $otp->attempts < 5) $otp->increment('attempts');
            return back()->withErrors(['code' => 'The code is invalid or has expired. Request a new code and try again.']);
        }

        $otp->update(['used_at' => now()]);
        Auth::login($user, true);
        $request->session()->regenerate();
        $request->session()->forget('workspace_otp_email');

        return redirect()->intended(route('workspace.dashboard'));
    }

    private function hashCode(string $email, string $code): string
    {
        return hash_hmac('sha256', $email.'|'.$code, (string) config('app.key'));
    }
}
