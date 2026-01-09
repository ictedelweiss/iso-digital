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
    public function redirect()
    {
        return Socialite::driver('azure')->redirect();
    }

    /**
     * Handle callback from Microsoft
     */
    public function callback()
    {
        try {
            $microsoftUser = Socialite::driver('azure')->user();

            // Find existing user by Microsoft ID or email
            $user = User::where('ms_id', $microsoftUser->getId())
                ->orWhere('ms_email', $microsoftUser->getEmail())
                ->first();

            if (!$user) {
                // Check if user exists with matching username
                $username = explode('@', $microsoftUser->getEmail())[0];
                $user = User::where('username', $username)->first();

                if ($user) {
                    // Link Microsoft account to existing user
                    $user->update([
                        'ms_id' => $microsoftUser->getId(),
                        'ms_email' => $microsoftUser->getEmail(),
                        'display_name' => $microsoftUser->getName(), // Save M365 display name
                    ]);
                } else {
                    // User not found - deny access
                    return redirect('/admin/login')
                        ->withErrors(['email' => 'Akun Microsoft tidak terdaftar di sistem. Hubungi administrator.']);
                }
            }

            // Login the user
            Auth::login($user, true);

            return redirect('/admin');

        } catch (\Exception $e) {
            return redirect('/admin/login')
                ->withErrors(['email' => 'Gagal login dengan Microsoft: ' . $e->getMessage()]);
        }
    }
}
