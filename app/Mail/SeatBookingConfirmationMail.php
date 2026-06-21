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
        public CourseBook $courseBook,
        public int $validityDays = 30,
        public string $instituteName = '',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Seat Booked — ' . ($this->courseBook->course?->name ?? 'Your Course'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.seat-booking-confirmation',
        );
    }
}
