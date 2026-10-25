<?php

namespace App\Filament\Pages\Auth;

use App\Models\AdminLoginOtp;
use App\Models\User;
use App\Notifications\AdminLoginCode;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Actions\Action;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EmailOtpLogin extends Login
{
    public bool $codeSent = false;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('email')
                ->label('Email address')
                ->email()
                ->required()
                ->autocomplete('email')
                ->autofocus(),
            TextInput::make('code')
                ->label('Six-digit login code')
                ->numeric()
                ->minLength(6)
                ->maxLength(6)
                ->autocomplete('one-time-code')
                ->required(fn (): bool => $this->codeSent)
                ->visible(fn (): bool => $this->codeSent),
        ]);
    }

    public function authenticate(): ?LoginResponse
    {
        if (! $this->codeSent) {
            $this->sendCode();

            return null;
        }

        try {
            $this->rateLimit(10);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();
        $email = Str::lower(trim((string) $data['email']));
        $code = (string) ($data['code'] ?? '');
        $user = User::query()->whereRaw('LOWER(email) = ?', [$email])->first();
        $otp = AdminLoginOtp::query()
            ->where('email', $email)
            ->whereNull('used_at')
            ->latest('id')
            ->first();

        $valid = $user
            && $user->canAccessPanel(Filament::getCurrentOrDefaultPanel())
            && $otp
            && $otp->expires_at->isFuture()
            && $otp->attempts < 5
            && hash_equals($otp->code_hash, $this->hashCode($email, $code));

        if (! $valid) {
            if ($otp && $otp->attempts < 5) {
                $otp->increment('attempts');
            }

            throw ValidationException::withMessages([
                'data.code' => 'The code is invalid or has expired. Request a new code and try again.',
            ]);
        }

        $otp->update(['used_at' => now()]);
        Filament::auth()->login($user, true);
        session()->regenerate();

        return app(LoginResponse::class);
    }

    public function sendCode(): void
    {
        $this->validate([
            'data.email' => ['required', 'email'],
        ]);
        $email = Str::lower(trim((string) ($this->data['email'] ?? '')));
        $rateLimitKey = 'admin-otp-send:'.request()->ip().':'.$email;

        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            throw ValidationException::withMessages([
                'data.email' => 'Too many codes were requested. Please wait '.RateLimiter::availableIn($rateLimitKey).' seconds.',
            ]);
        }

        RateLimiter::hit($rateLimitKey, 600);
        $user = User::query()->whereRaw('LOWER(email) = ?', [$email])->first();

        if ($user && $user->canAccessPanel(Filament::getCurrentOrDefaultPanel())) {
            $code = (string) random_int(100000, 999999);

            AdminLoginOtp::query()->where('email', $email)->whereNull('used_at')->delete();
            AdminLoginOtp::create([
                'email' => $email,
                'code_hash' => $this->hashCode($email, $code),
                'expires_at' => now()->addMinutes(10),
            ]);

            $user->notify(new AdminLoginCode($code));
        }

        $this->codeSent = true;
        $this->data['code'] = null;

        Notification::make()
            ->title('Check your email')
            ->body('If the address is authorized, a six-digit login code has been sent.')
            ->success()
            ->send();
    }

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label(fn (): string => $this->codeSent ? 'Verify code' : 'Send login code')
            ->submit('authenticate');
    }

    protected function getFormActions(): array
    {
        return [
            $this->getAuthenticateFormAction(),
            Action::make('resend')
                ->label('Send a new code')
                ->link()
                ->action('sendCode')
                ->visible(fn (): bool => $this->codeSent),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return 'Sign in with an email code';
    }

    public function getHeading(): string|Htmlable|null
    {
        return $this->codeSent ? 'Enter your login code' : 'Business Hub sign in';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return $this->codeSent
            ? 'The code expires in 10 minutes.'
            : 'Enter an approved email address. No password is required.';
    }

    private function hashCode(string $email, string $code): string
    {
        return hash_hmac('sha256', $email.'|'.$code, (string) config('app.key'));
    }
}
