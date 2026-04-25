<?php

namespace App\Http\Controllers;

use App\Models\ProgramRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketController extends Controller
{
    // Frontend: Show Ticket
    public function show($ticket_code)
    {
        $registration = ProgramRegistration::where('ticket_code', $ticket_code)->with(['user', 'program'])->firstOrFail();
        
        // Only owner or admin/staff can view
        if (Auth::id() !== $registration->user_id && !Auth::user()->hasRole(['admin', 'staff'])) {
            abort(403);
        }

        return view('events.ticket', compact('registration'));
    }

    // Admin/Staff: Scanner View
    public function scanner()
    {
        if (!Auth::user()->hasRole(['admin', 'staff', 'editor'])) {
            abort(403);
        }
        return view('events.scanner');
    }

    // Admin/Staff: Process Scan
    public function scan(Request $request)
    {
        if (!Auth::user()->hasRole(['admin', 'staff', 'editor'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $registration = ProgramRegistration::where('ticket_code', $request->ticket_code)->first();

        if (!$registration) {
            return response()->json(['success' => false, 'message' => 'Tiket tidak valid!'], 404);
        }

        if ($registration->checked_in_at) {
            return response()->json([
                'success' => false, 
                'message' => 'Tiket SUDAH DIGUNAKAN pada ' . $registration->checked_in_at->format('H:i'),
                'user' => $registration->user->name
            ], 422);
        }

        $registration->update(['checked_in_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Check-in Berhasil!',
            'user' => $registration->user->name,
            'program' => $registration->program->title
        ]);
    }
}
