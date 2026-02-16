<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class MicrosoftController extends Controller
{
    /**
     * Redirect to Microsoft for authentication
     */
    public function redirect(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Azure Redirect Debug', config('services.azure'));

        if ($request->has('redirect_to')) {
            session(['azure_redirect_to' => $request->get('redirect_to')]);
        }

        return Socialite::driver('azure')->redirect();
    }

    /**
     * Handle callback from Microsoft
     */
    public function callback()
    {
        try {
            \Illuminate\Support\Facades\Log::info('Azure Callback Started');

            $microsoftUser = Socialite::driver('azure')->user();

            \Illuminate\Support\Facades\Log::info('Microsoft User Retrieved', [
                'id' => $microsoftUser->getId(),
                'email' => $microsoftUser->getEmail(),
                'name' => $microsoftUser->getName(),
            ]);

            // Find existing user by Microsoft ID or email
            $user = User::where('ms_id', $microsoftUser->getId())
                ->orWhere('ms_email', $microsoftUser->getEmail())
                ->first();

            if (!$user) {
                \Illuminate\Support\Facades\Log::info('User not found by MS ID/Email, checking username');

                // Check if user exists with matching username
                $email = $microsoftUser->getEmail();
                $username = explode('@', $email)[0];
                $user = User::where('username', $username)->first();

                if ($user) {
                    \Illuminate\Support\Facades\Log::info('User found by username, linking account', ['username' => $username]);

                    // Link Microsoft account to existing user
                    $user->update([
                        'ms_id' => $microsoftUser->getId(),
                        'ms_email' => $microsoftUser->getEmail(),
                        'display_name' => $microsoftUser->getName(), // Save M365 display name
                    ]);
                }
                elseif (str_ends_with($email, '@edelweiss.sch.id')) {
                    \Illuminate\Support\Facades\Log::info('New Edelweiss user detected, performing auto-registration', ['email' => $email]);

                    // Auto-register new Edelweiss staff
                    $user = User::create([
                        'username' => $username,
                        'display_name' => $microsoftUser->getName(),
                        'ms_id' => $microsoftUser->getId(),
                        'ms_email' => $email,
                        'role' => 'Staff', // Default role
                        'password_hash' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16)), // Dummy password
                    ]);
                }
                else {
                    \Illuminate\Support\Facades\Log::warning('User not found in system and domain restricted', ['email' => $email, 'username' => $username]);

                    // User not found - deny access
                    return redirect()->route('filament.admin.auth.login')
                        ->withErrors(['email' => 'Akun Microsoft tidak terdaftar di sistem. Hubungi administrator.']);
                }
            }

            \Illuminate\Support\Facades\Log::info('Logging in user', ['id' => $user->id, 'username' => $user->username]);

            // Login the user
            Auth::login($user, true);

            \Illuminate\Support\Facades\Log::info('Login successful, redirecting');

            $redirectTo = session()->pull('azure_redirect_to', config('app.url') . '/admin');
            return redirect()->to($redirectTo);

        }
        catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Microsoft Login Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('filament.admin.auth.login')
                ->withErrors(['email' => 'Gagal login dengan Microsoft: ' . $e->getMessage()]);
        }
    }
}