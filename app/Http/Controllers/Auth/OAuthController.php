<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OAuthController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(Request $request, $provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
            
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                // Update existing user with oauth id if it doesn't have one
                $user->update([
                    'oauth_id' => $socialUser->getId(),
                    'oauth_provider' => $provider,
                ]);
            } else {
                // Create new user
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'oauth_id' => $socialUser->getId(),
                    'oauth_provider' => $provider,
                    'password' => bcrypt(Str::random(16)), // Required field, generate random
                    'email_verified_at' => now(), // Auto verify email to pass Filament checks
                ]);
            }

            // Cek otorisasi Panel untuk user dari OAuth
            // Jika user bukan super_admin dan bukan panel_user (berarti kosongan)
            if (!$user->hasRole('super_admin') && !$user->hasRole('panel_user')) {
                // Kita beri role standar untuk bisa masuk dashboard, yaitu: panel_user
                $user->assignRole('panel_user'); 
            }

            Auth::login($user, true); // true for "remember me"
            $request->session()->regenerate(); // Important to regenerate session

            return redirect()->intended('/sangkara'); // Redirect to filament dashboard
        } catch (\Exception $e) {
            Log::error('OAuth Error: ' . $e->getMessage());
            return redirect('/sangkara/login')->with('error', 'Authentication failed: ' . $e->getMessage());
        }
    }
}
