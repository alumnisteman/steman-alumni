<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    /**
     * Tampilkan form minta link reset password
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Kirim email link reset password
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Email tidak ditemukan di sistem kami.');
        }

        // Generate Token
        $token = Str::random(64);

        // Store Token in password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        // Send Email
        try {
            Mail::send('auth.emails.password_reset', ['token' => $token, 'user' => $user], function($message) use($request){
                $message->to($request->email);
                $message->subject('Reset Password - Portal Alumni Steman');
            });
            
            \App\Jobs\LogActivity::dispatch(
                $user->id,
                'Forgot Password',
                'Requested password reset link via email.',
                $request->ip(),
                $request->header('User-Agent')
            );

            return back()->with('success', 'Link reset password telah dikirim ke email Anda. Silakan cek Inbox atau Spam.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Mail Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim email. Pastikan konfigurasi SMTP di server sudah benar.');
        }
    }

    /**
     * Tampilkan form ganti password baru (setelah klik link email)
     */
    public function showResetForm($token)
    {
        return view('auth.passwords.reset', ['token' => $token]);
    }

    /**
     * Proses update password baru
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:4|confirmed',
        ]);

        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$reset) {
            return back()->with('error', 'Token tidak valid atau sudah kadaluarsa.');
        }

        // Update User Password
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->with('error', 'User tidak ditemukan.');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Delete used token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        \App\Jobs\LogActivity::dispatch(
            $user->id,
            'Reset Password',
            'Password successfully reset via email link.',
            $request->ip(),
            $request->header('User-Agent')
        );

        return redirect()->route('login')->with('success', 'Password Anda berhasil diperbarui. Silakan login dengan password baru.');
    }
}
