<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $accountName,
        public string $resetUrl,
        public string $identifier,
    ) {
    }

    public function build(): self
    {
        return $this->subject('Reset Your GT Institute Password')
            ->view('emails.password-reset-link', [
                'accountName' => $this->accountName,
                'resetUrl' => $this->resetUrl,
                'identifier' => $this->identifier,
            ]);
    }
}
