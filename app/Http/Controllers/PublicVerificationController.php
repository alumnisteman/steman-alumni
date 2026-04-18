<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PublicVerificationController extends Controller
{
    public function verify(string $token)
    {
        // Use the same qr_login_token for verification
        $user = User::where('qr_login_token', $token)->first();

        if (!$user) {
            return view('public.verification-error', [
                'message' => 'Tanda verifikasi tidak valid atau telah dicabut.'
            ]);
        }

        // Only verified/approved alumni can show official certificate
        if ($user->status !== 'approved' && $user->role !== 'admin') {
            return view('public.verification-error', [
                'message' => 'Akun alumni sedang dalam masa peninjauan.'
            ]);
        }

        return view('public.verification', compact('user'));
    }
}
