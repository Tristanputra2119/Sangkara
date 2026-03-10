<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\SimplePage;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;
use App\Models\TwoFactorCode;
use App\Models\User;

class TwoFactorChallenge extends SimplePage implements HasForms
{
    use InteractsWithForms;
    
    protected static string $layout = 'filament-panels::components.layout.simple';

    public ?array $data = [];

    public function getView(): string
    {
        return 'filament.pages.auth.two-factor-challenge';
    }

    public function mount(): void
    {
        // Redirect if no 2FA session
        if (!session()->has('two_factor_user_id')) {
            $this->redirect(route('filament.sangkara.auth.login'));
            return;
        }
        
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                TextInput::make('code')
                    ->label('Kode Verifikasi')
                    ->placeholder('')
                    ->required()
                    ->maxLength(6)
                    ->minLength(6)
                    ->numeric()
                    ->autofocus()
                    ->extraInputAttributes([
                        'class' => 'text-center tracking-[0.5rem] text-xl font-semibold',
                        'maxlength' => '6',
                        'inputmode' => 'numeric',
                    ]),
            ])
            ->statePath('data');
    }

    public function verify(): void
    {
        $data = $this->form->getState();
        
        $userId = session('two_factor_user_id');
        $remember = session('two_factor_remember', false);

        if (!$userId) {
            throw ValidationException::withMessages([
                'data.code' => 'Sesi verifikasi tidak valid. Silakan login kembali.',
            ]);
        }

        $user = User::find($userId);

        if (!$user) {
            session()->forget(['two_factor_user_id', 'two_factor_remember']);
            throw ValidationException::withMessages([
                'data.code' => 'User tidak ditemukan. Silakan login kembali.',
            ]);
        }

        // Verify the code
        if (!TwoFactorCode::verify($user, $data['code'])) {
            throw ValidationException::withMessages([
                'data.code' => 'Kode verifikasi tidak valid atau sudah kadaluarsa.',
            ]);
        }

        // Clear 2FA session data
        session()->forget(['two_factor_user_id', 'two_factor_remember']);

        // Complete login
        Auth::login($user, $remember);

        // Redirect to admin panel
        $this->redirect(route('filament.sangkara.pages.dashboard'));
    }

    public function resendCode(): void
    {
        $userId = session('two_factor_user_id');

        if (!$userId) {
            $this->redirect(route('filament.sangkara.auth.login'));
            return;
        }

        $user = User::find($userId);

        if (!$user) {
            $this->redirect(route('filament.sangkara.auth.login'));
            return;
        }

        $twoFactorCode = TwoFactorCode::generateFor($user, request()->ip());
        $user->notify(new \App\Notifications\TwoFactorCodeNotification($twoFactorCode->code));

        \Filament\Notifications\Notification::make()
            ->title('Kode verifikasi baru telah dikirim ke email Anda.')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('verify')
                ->label('Verifikasi')
                ->submit('verify')
                ->size(\Filament\Support\Enums\ActionSize::Large)
                ->fullWidth(),
        ];
    }

    public function getTitle(): string
    {
        return 'Verifikasi 2FA';
    }

    public function getHeading(): string
    {
        return 'Verifikasi Dua Faktor';
    }

    public function getSubheading(): ?string
    {
        return 'Masukkan kode verifikasi 6-digit yang telah dikirim ke email Anda.';
    }

    public function hasLogo(): bool
    {
        return true;
    }
}
