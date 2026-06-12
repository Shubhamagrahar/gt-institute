<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ShowMasterOtp extends Command
{
    protected $signature   = 'otp:master {--date= : Date in Y-m-d format (default: today)}';
    protected $description = 'Show the master OTP for emergency login when SMTP is down';

    public function handle(): int
    {
        $secret = env('MASTER_OTP_SECRET');

        if (!$secret) {
            $this->error('MASTER_OTP_SECRET is not set in .env');
            return self::FAILURE;
        }

        $date = $this->option('date') ?? now()->format('Y-m-d');

        // Validate date format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $this->error('Invalid date format. Use Y-m-d (e.g. 2026-06-12)');
            return self::FAILURE;
        }

        $otp = $this->compute($secret, $date);

        $this->newLine();
        $this->line('  <fg=yellow>GT Institute — Master OTP</>');
        $this->line('  ─────────────────────────────');
        $this->line("  Date   : <fg=cyan>{$date}</>");
        $this->line("  Code   : <fg=green;options=bold>{$otp}</>");
        $this->line('  Expiry : Midnight (rotates daily)');
        $this->newLine();
        $this->line('  <fg=red>⚠  Keep this code confidential. Use only in emergencies.</>');
        $this->newLine();

        return self::SUCCESS;
    }

    public static function compute(string $secret, string $date): string
    {
        $hash = hash('sha256', $secret . '|' . $date);
        return str_pad((string) (hexdec(substr($hash, 0, 10)) % 1000000), 6, '0', STR_PAD_LEFT);
    }
}
