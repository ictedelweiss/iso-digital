<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CustomLogin extends BaseLogin
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
            $this->getEmailFormComponent(),
            $this->getPasswordFormComponent(),
            $this->getRememberFormComponent(),
        ])
            ->statePath('data');
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Username atau Email')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1])
            ->placeholder('admin atau admin@edelweiss.sch.id');
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Kata Sandi')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->autocomplete('current-password')
            ->required()
            ->extraInputAttributes(['tabindex' => 2]);
    }

    protected function getRememberFormComponent(): Component
    {
        return Checkbox::make('remember')
            ->label('Ingat saya');
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        }
        catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title('Terlalu banyak percobaan login')
                ->body('Silakan coba lagi dalam ' . ceil($exception->secondsUntilAvailable / 60) . ' menit.')
                ->danger()
                ->send();

            return null;
        }

        $data = $this->form->getState();
        $input = $data['email'];

        // Find user by ms_email or username
        $user = User::where('ms_email', $input)
            ->orWhere('username', $input)
            ->orWhere('username', explode('@', $input)[0])
            ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'data.email' => 'Pengguna tidak ditemukan.',
            ]);
        }

        // Verify password
        if (!Hash::check($data['password'], $user->password_hash)) {
            throw ValidationException::withMessages([
                'data.email' => 'Kredensial tidak valid.',
            ]);
        }

        // Login the user
        Auth::login($user, $data['remember'] ?? false);

        session()->regenerate();

        return app(LoginResponse::class);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getAuthenticateFormAction(),
        ];
    }

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label('Masuk')
            ->submit('authenticate');
    }

    public function getHeading(): string
    {
        return 'Masuk ke akun Anda';
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }

    public function getHeadContent(): string
    {
        $baseUrl = config('app.url');
        return "<base href='{$baseUrl}/'>
";
    }
}