<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ProgramRegistration;
use App\Models\ActivityLog;
use App\Jobs\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProgramRegistrationController extends Controller
{
    /**
     * Store a new program registration (Alumni side)
     */
    public function store(Request $request, Program $program)
    {
        $request->validate([
            'phone_number' => 'required|string|max:20',
            'motivation' => 'required|string|min:10',
            'attachment' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120', // 5MB max
        ]);

        // Check for existing registration
        $existing = ProgramRegistration::where('user_id', Auth::id())
            ->where('program_id', $program->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah terdaftar di program ini.');
        }

        $data = [
            'user_id' => Auth::id(),
            'program_id' => $program->id,
            'status' => ProgramRegistration::STATUS_PENDING,
            'phone_number' => $request->phone_number,
            'motivation' => $request->motivation,
        ];

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('registrations/attachments', 'public');
            $data['attachment_path'] = $path;
        }

        $registration = ProgramRegistration::create($data);

        LogActivity::dispatch(
            Auth::id(),
            'Program Registration',
            'Registered for program: ' . $program->title,
            $request->ip(),
            $request->header('User-Agent')
        );

        return back()->with('success', 'Pendaftaran Anda berhasil dikirim dan sedang menunggu verifikasi.');
    }

    /**
     * List all registrations (Admin side)
     */
    public function adminIndex(Request $request)
    {
        $query = ProgramRegistration::with(['user', 'program']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('program', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $registrations = $query->latest()->paginate(20)->withQueryString();

        return view('admin.programs.registrations', compact('registrations'));
    }

    /**
     * Update registration status (Admin side)
     */
    public function updateStatus(Request $request, ProgramRegistration $registration)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'admin_notes' => 'nullable|string',
        ]);

        $registration->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ]);

        // Generate Ticket if approved and is an event
        if ($request->status == 'approved' && $registration->program->is_event && !$registration->ticket_code) {
            $ticketCode = 'STMN-' . strtoupper(\Illuminate\Support\Str::random(8)) . '-' . $registration->id;
            $registration->update([
                'ticket_code' => $ticketCode
            ]);
        }

        LogActivity::dispatch(
            Auth::id(),
            'Update Registration Status',
            'Updated status for registration #' . $registration->id . ' to ' . $request->status,
            $request->ip(),
            $request->header('User-Agent')
        );

        return back()->with('success', 'Status pendaftaran berhasil diperbarui.');
    }

    /**
     * Delete registration (Admin side)
     */
    public function destroy(ProgramRegistration $registration)
    {
        if ($registration->attachment_path) {
            Storage::disk('public')->delete($registration->attachment_path);
        }
        
        $registration->delete();

        return back()->with('success', 'Data pendaftaran berhasil dihapus.');
    }
}
