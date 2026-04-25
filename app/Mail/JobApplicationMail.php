<?php

namespace App\Mail;

use App\Models\User;
use App\Models\JobVacancy;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobApplicationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $applicant;
    public $job;
    public $coverLetter;

    /**
     * Create a new message instance.
     */
    public function __construct(User $applicant, JobVacancy $job, ?string $coverLetter = null)
    {
        $this->applicant = $applicant;
        $this->job = $job;
        $this->coverLetter = $coverLetter;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Lamaran Pekerjaan: ' . $this->job->title . ' - ' . $this->applicant->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.job_application',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
