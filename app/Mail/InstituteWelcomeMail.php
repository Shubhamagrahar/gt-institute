<?php

namespace App\Mail;

use App\Models\Owner\Institute;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InstituteWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Institute $institute,
        public User      $user,
        public string    $plainPassword,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Welcome to GT Institute Platform — Your Login Credentials",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.institute-welcome',
            with: [
                'institute'     => $this->institute,
                'user'          => $this->user,
                'plainPassword' => $this->plainPassword,
            ],
        );
    }
}
