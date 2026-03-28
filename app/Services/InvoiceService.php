<?php

namespace App\Services;

use App\Models\Owner\InstitutePayCollect;
use App\Models\Owner\Institute;
use App\Models\FeeCollectDetail;

class InvoiceService
{
    public function generateInstituteInvoice(): string
    {
        $year = now()->year;
        $count = InstitutePayCollect::whereYear('created_at', $year)->count();
        $next  = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
        return "GT/PAY/{$year}/{$next}";
    }

    public function generateInstituteUniqueId(): string
    {
        $year  = now()->year;
        $count = Institute::whereYear('created_at', $year)->count();
        $next  = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
        return "INST{$year}{$next}";
    }

    public function generateFeeInvoice(int $instituteId): string
    {
        $year  = now()->year;
        $count = FeeCollectDetail::where('institute_id', $instituteId)->whereYear('created_at', $year)->count();
        $next  = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
        return "FEE/{$year}/{$next}";
    }
}
