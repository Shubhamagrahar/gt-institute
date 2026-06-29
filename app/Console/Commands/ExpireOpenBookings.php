<?php

namespace App\Console\Commands;

use App\Models\CourseBook;
use App\Models\Institute;
use Illuminate\Console\Command;

class ExpireOpenBookings extends Command
{
    protected $signature   = 'bookings:expire-open';
    protected $description = 'Expire OPEN seat bookings that have exceeded the institute validity window';

    public function handle(): int
    {
        $total = 0;

        Institute::query()->each(function (Institute $institute) use (&$total) {
            $days = (int) ($institute->seat_booking_validity_days ?? 30);

            $count = CourseBook::where('institute_id', $institute->id)
                ->where('status', 'OPEN')
                ->where('book_date', '<', now()->subDays($days)->toDateString())
                ->update(['status' => 'EXPIRED']);

            $total += $count;
        });

        $this->info("Expired {$total} booking(s).");
        return Command::SUCCESS;
    }
}
