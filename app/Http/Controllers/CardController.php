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
        
        // Generate QR Code pointing to the user's public profile
        $profileUrl = route('alumni.show', $user->id);
        
        $qrCode = QrCode::size(200)
            ->backgroundColor(255, 255, 255)
            ->color(30, 41, 59)
            ->margin(1)
            ->generate($profileUrl);

        return view('alumni.card', compact('user', 'qrCode'));
    }
}
