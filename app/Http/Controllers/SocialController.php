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
        // Always use the main domain for OAuth callback to simplify Google Console config
        // and ensure only one redirect URI needs to be registered.
        $mainDomain = parse_url(config('app.url'), PHP_URL_HOST);
        $callbackUrl = 'https://' . $mainDomain . '/auth/' . $provider . '/callback';

        return Socialite::driver($provider)->redirectUrl($callbackUrl)->redirect();
    }

    public function callback($provider)
    {
        try {
            $mainDomain = parse_url(config('app.url'), PHP_URL_HOST);
            $callbackUrl = 'https://' . $mainDomain . '/auth/' . $provider . '/callback';

            // LinkedIn now uses OpenID Connect
            $driverName = ($provider === 'linkedin') ? 'linkedin-openid' : $provider;
            $socialUser = Socialite::driver($driverName)
                ->redirectUrl($callbackUrl)
                ->user();
            
            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'social_id' => $socialUser->getId(),
                    'social_type' => $provider,
                    'password' => null,
                    'role' => 'alumni',
                    'status' => 'pending', // Require admin approval for new social registrations
                    'email_verified_at' => now(),
                ]);

                // Notify Admin via Telegram
                try {
                    \App\Services\SystemGuard\Notifier::send(
                        "👤 *Registrasi Alumni Baru ({$provider})*\n\n" .
                        "Nama: {$user->name}\n" .
                        "Email: {$user->email}\n\n" .
                        "Status: *Menunggu Persetujuan Admin*", 
                        'warning'
                    );
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Social Login Notifier Error: ' . $e->getMessage());
                }
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
