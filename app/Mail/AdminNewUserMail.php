<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $newUser;

    public function __construct(User $newUser)
    {
        $this->newUser = $newUser;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Alumni STEMAN] Anggota Baru Mendaftar — ' . $this->newUser->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin_new_user',
        );
    }
}
