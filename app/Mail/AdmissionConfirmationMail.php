<?php

namespace App\Mail;

use App\Models\CourseBook;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdmissionConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public CourseBook $courseBook,
        public string $plainPassword,
        public string $instituteName = '',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Admission Confirmed — ' . ($this->courseBook->course?->name ?? 'Your Course'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admission-confirmation',
        );
    }
}
