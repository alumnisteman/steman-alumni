<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialController extends Controller
{
    private function isConfigured(string $provider): bool
    {
        $placeholders = [
            'your-google-client-id', 'your-google-client-secret',
            'your-linkedin-client-id', 'your-linkedin-client-secret',
        ];
        $clientId     = config("services.{$provider}.client_id");
        $clientSecret = config("services.{$provider}.client_secret");
        return !empty($clientId)
            && !empty($clientSecret)
            && !in_array($clientId, $placeholders)
            && !in_array($clientSecret, $placeholders);
    }

    public function redirect($provider)
    {
        if (!$this->isConfigured($provider)) {
            return redirect()->route('login')->with('error',
                'Login via ' . ucfirst($provider) . ' belum dikonfigurasi oleh admin. '
                . 'Silakan gunakan email dan password untuk masuk.');
        }

        $mainDomain  = parse_url(config('app.url'), PHP_URL_HOST);
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
                    'status' => 'approved', // Auto-approve: alumni langsung bisa login
                    'auto_approved' => true,
                    'email_verified_at' => now(),
                ]);

                // Auto-follow batch mates for community engagement
                \App\Jobs\AutoFollowBatchMates::dispatch($user->id);

                // Kirim email selamat datang
                try {
                    \Illuminate\Support\Facades\Mail::to($user->email)->queue(new \App\Mail\WelcomeMail($user));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('Welcome email failed for ' . $user->email . ': ' . $e->getMessage());
                }

                // Notify Admin via Telegram (info only, no approval needed)
                try {
                    \App\Services\SystemGuard\Notifier::send(
                        "👤 *Alumni Baru via {$provider}*\n\n" .
                        "Nama: {$user->name}\n" .
                        "Email: {$user->email}\n\n" .
                        "✅ Status: *Langsung Aktif (Auto-Approved)*",
                        'info'
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
