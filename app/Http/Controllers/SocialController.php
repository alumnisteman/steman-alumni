<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        try {
            // LinkedIn now uses OpenID Connect
            $driver = ($provider === 'linkedin') ? 'linkedin-openid' : $provider;
            $socialUser = Socialite::driver($driver)->user();
            
            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'social_id' => $socialUser->getId(),
                    'social_type' => $provider,
                    'password' => null,
                    'role' => 'alumni',
                    'status' => 'approved', // Auto-approve social logins
                    'email_verified_at' => now(),
                ]);
            } else {
                $user->update([
                    'social_id' => $socialUser->getId(),
                    'social_type' => $provider,
                ]);
            }

            Auth::login($user);

            return redirect()->intended($user->dashboardUrl());
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Gagal login via ' . $provider . ': ' . $e->getMessage());
        }
    }
}
