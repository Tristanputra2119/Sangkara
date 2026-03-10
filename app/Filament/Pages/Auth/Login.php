<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Illuminate\Validation\ValidationException;
use App\Models\TwoFactorCode;
use App\Notifications\TwoFactorCodeNotification;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Illuminate\Support\Facades\Auth;

class Login extends BaseLogin
{
    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            throw ValidationException::withMessages([
                'data.email' => __('filament-panels::pages/auth/login.messages.throttled', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]),
            ]);
        }

        $data = $this->form->getState();

        // Attempt to authenticate with credentials
        if (!Auth::attempt([
            'email' => $data['email'],
            'password' => $data['password'],
        ], $data['remember'] ?? false)) {
            throw ValidationException::withMessages([
                'data.email' => __('filament-panels::pages/auth/login.messages.failed'),
            ]);
        }

        $user = Auth::user();

        // Check if user is OAuth user (skip 2FA for OAuth users)
        if ($user->isOAuthUser()) {
            return app(LoginResponse::class);
        }

        // Logout immediately and store user ID in session for 2FA
        Auth::logout();
        
        // Generate and send 2FA code
        $twoFactorCode = TwoFactorCode::generateFor($user, request()->ip());
        $user->notify(new TwoFactorCodeNotification($twoFactorCode->code));

        // Store user ID in session for 2FA verification
        session([
            'two_factor_user_id' => $user->id,
            'two_factor_remember' => $data['remember'] ?? false,
        ]);

        // Redirect to 2FA challenge using Livewire redirect
        $this->redirect(route('filament.sangkara.auth.two-factor-challenge'), navigate: true);
        
        return null;
    }
}
