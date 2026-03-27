<?php
namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Mail\ContactFormMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactMessageController extends Controller
{
    /**
     * Store a new message from the public contact form.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ], [
            'name.required'    => 'Nama lengkap wajib diisi.',
            'email.required'   => 'Email wajib diisi.',
            'email.email'      => 'Format email tidak valid.',
            'subject.required' => 'Subjek wajib diisi.',
            'message.required' => 'Pesan wajib diisi.',
            'message.min'      => 'Pesan minimal 10 karakter.',
        ]);

        // Save to database
        ContactMessage::create($request->only(['name', 'email', 'subject', 'message']));

        // Send email notification to admin
        try {
            Mail::to(setting('contact_email', config('mail.from.address', 'alumnisteman@gmail.com')))->send(
                new ContactFormMail(
                    $request->name,
                    $request->email,
                    $request->subject,
                    $request->message
                )
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send contact email: ' . $e->getMessage());
            // Email failed but message is saved — non-critical
        }

        return back()->with('message_sent', 'Pesan Anda telah berhasil dikirim! Kami akan segera menghubungi Anda.');
    }

    /**
     * Admin: list all messages.
     */
    public function index()
    {
        $messages = ContactMessage::latest()->paginate(20);
        $unreadCount = ContactMessage::where('is_read', false)->count();
        return view('admin.messages.index', compact('messages', 'unreadCount'));
    }

    /**
     * Admin: view a single message.
     */
    public function show($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->markAsRead();
        return view('admin.messages.show', compact('message'));
    }

    /**
     * Admin: reply internally to a message.
     */
    public function reply(Request $request, $id)
    {
        $message = ContactMessage::findOrFail($id);
        $request->validate([
            'reply_content' => 'required|string',
        ]);

        $message->update([
            'reply_content' => $request->reply_content,
            'replied_at' => now(),
            'is_read' => true,
        ]);

        return back()->with('success', 'Balasan internal berhasil disimpan!');
    }

    /**
     * Admin: delete a message.
     */
    public function destroy($id)
    {
        ContactMessage::findOrFail($id)->delete();
        return back()->with('success', 'Pesan berhasil dihapus.');
    }
}
