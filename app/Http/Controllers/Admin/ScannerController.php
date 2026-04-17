<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ScannerController extends Controller
{
    public function index()
    {
        return view('admin.scanner');
    }

    public function verify(Request $request)
    {
        $token = $request->token;
        
        // Find user by either the token (from URL) or direct token
        // Robust extraction: Handle URLs with query strings or trailing slashes
        if (filter_var($token, FILTER_VALIDATE_URL)) {
            $path = parse_url($token, PHP_URL_PATH);
            $parts = explode('/', rtrim($path, '/'));
            $tokenValue = end($parts);
        } else {
            $tokenValue = $token;
        }

        $user = User::where('qr_login_token', $tokenValue)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid atau alumni tidak ditemukan.'
            ], 404);
        }

        // Log the scan activity
        \App\Jobs\LogActivity::dispatch(
            \Illuminate\Support\Facades\Auth::id(),
            'Scan QR Code',
            'Scanned and verified alumni: ' . $user->name,
            $request->ip(),
            $request->header('User-Agent')
        );

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'major' => $user->major,
                'graduation_year' => $user->graduation_year,
                'current_job' => $user->current_job ?: 'Belum bekerja',
                'profile_picture' => $user->profile_picture_url, // Use the accessor
                'points' => $user->points,
                'status' => $user->status,
            ]
        ]);
    }

    public function awardPoints(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'nullable|integer|min:1|max:1000',
        ]);

        $user = User::find($request->user_id);
        $amount = (int) ($request->amount ?: 10);
        
        $user->awardPoints($amount);

        return response()->json([
            'success' => true,
            'new_points' => $user->points,
            'message' => "Berhasil memberikan $amount poin kepada $user->name!"
        ]);
    }
}
