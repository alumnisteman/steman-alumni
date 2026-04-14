<?php
namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Mail\ContactFormMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Jobs\LogActivity;
use App\Jobs\GenerateAIAutoReply;

class ContactMessageController extends Controller
{
    /**
     * Store a new message from the public contact form.
     */
    public function store(Request $request)
    {
        try {
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
                'message.required' => 'message wajib diisi.',
                'message.min'      => 'message minimal 10 karakter.',
            ]);

            // Save to database
            $message = ContactMessage::create($request->only(['name', 'email', 'subject', 'message']));

            // AI Auto-Reply Suggestion (Background)
            GenerateAIAutoReply::dispatch($message);

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

            return back()->with('message_sent', 'message Anda telah berhasil dikirim! Kami akan segera menghubungi Anda.');
        } catch (\Throwable $t) {
            \Illuminate\Support\Facades\Log::error("FATAL CONTACT FORM ERROR: " . $t->getMessage() . "\n" . $t->getTraceAsString());
            return response()->json([
                'error' => $t->getMessage(),
                'file' => $t->getFile(),
                'line' => $t->getLine(),
                'trace' => $t->getTraceAsString()
            ], 500);
        }
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
        $request->validate([
            'reply_content' => 'required|string',
        ]);

        $message = ContactMessage::findOrFail($id);
        $message->update([
            'reply_content' => strip_tags($request->reply_content),
            'replied_at' => now(),
            'is_read' => true,
        ]);

        LogActivity::dispatch(
            Auth::id(),
            'Reply Contact Message',
            'Replied to message from: ' . $message->email,
            $request->ip(),
            $request->header('User-Agent')
        );

        return back()->with('success', 'Balasan internal berhasil disimpan!');
    }

    /**
     * Admin: delete a message.
     */
    public function destroy($id)
    {
        $message = ContactMessage::findOrFail($id);
        $email = $message->email;
        $message->delete();

        LogActivity::dispatch(
            Auth::id(),
            'Delete Contact Message',
            'Deleted message from: ' . $email,
            request()->ip(),
            request()->header('User-Agent')
        );

        return back()->with('success', 'message berhasil dihapus.');
    }
}
