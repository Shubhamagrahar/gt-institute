<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoginOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $accountName,
        public string $otp,
    ) {
    }

    public function build(): self
    {
        return $this->subject('Your GT Institute Login OTP')
            ->view('emails.login-otp', [
                'accountName' => $this->accountName,
                'otp'         => $this->otp,
            ]);
    }
}
