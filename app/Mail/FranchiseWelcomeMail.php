<?php

namespace App\Mail;

use App\Models\Franchise;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FranchiseWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Franchise $franchise,
        public User $user,
        public string $plainPassword,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to GT Franchise Panel - Your Login Credentials',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.franchise-welcome',
            with: [
                'franchise' => $this->franchise,
                'user' => $this->user,
                'plainPassword' => $this->plainPassword,
            ],
        );
    }
}
