<?php

namespace App\Mail;

use App\Models\CourseBook;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SeatBookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public CourseBook $courseBook
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Seat booking confirmed - ' . ($this->courseBook->course?->name ?? 'Admission')
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.seat-booking-confirmation',
            with: [
                'user' => $this->user,
                'courseBook' => $this->courseBook,
            ]
        );
    }
}
