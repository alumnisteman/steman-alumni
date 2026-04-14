<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $senderName;
    public string $senderEmail;
    public string $messageSubject;
    public string $body;

    public function __construct(string $name, string $email, string $subject, string $body)
    {
        $this->senderName  = $name;
        $this->senderEmail = $email;
        $this->messageSubject = $subject;
        $this->body        = $body;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[message KONTAK] ' . $this->messageSubject,
            replyTo: [
                new \Illuminate\Mail\Mailables\Address($this->senderEmail, $this->senderName),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact_form',
        );
    }
}
