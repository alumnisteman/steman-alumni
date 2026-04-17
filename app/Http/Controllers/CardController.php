<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        try {
            // QR Code points to the official PUBLIC verification link
            $verificationUrl = route('public.verification', $user->qr_login_token);
            
            $qrCode = QrCode::size(200)
                ->backgroundColor(255, 255, 255)
                ->color(30, 41, 59)
                ->margin(1)
                ->generate($verificationUrl);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('QR Code Generation Error: ' . $e->getMessage());
            // Fallback: Simple placeholder or text if QR generation fails
            $qrCode = '<div class="text-center p-3 small border rounded bg-light">QR Unavailable</div>';
        }

        return view('alumni.card', compact('user', 'qrCode'));
    }
}
