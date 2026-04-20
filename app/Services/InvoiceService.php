<?php

namespace App\Services;

use App\Models\Franchise;
use App\Models\FranchiseTransaction;
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

    public function generateFranchiseUniqueId(int $instituteId): string
    {
        $year = now()->year;
        $count = Franchise::where('institute_id', $instituteId)->whereYear('created_at', $year)->count();
        $next = str_pad($count + 1, 4, '0', STR_PAD_LEFT);

        return "FRN{$year}{$next}";
    }

    public function generateFranchiseTxnNo(int $instituteId, int $franchiseId): string
    {
        $year = now()->year;
        $count = FranchiseTransaction::where('institute_id', $instituteId)
            ->where('franchise_id', $franchiseId)
            ->whereYear('created_at', $year)
            ->count();
        $next = str_pad($count + 1, 4, '0', STR_PAD_LEFT);

        return "FR-TXN/{$year}/{$next}";
    }
}
