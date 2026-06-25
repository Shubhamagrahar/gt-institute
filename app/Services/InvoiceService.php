<?php

namespace App\Services;

use App\Models\Franchise;
use App\Models\FranchiseFeeCollection;
use App\Models\FranchiseTransaction;
use App\Models\Owner\InstitutePayCollect;
use App\Models\Owner\Institute;
use App\Models\FeeCollectDetail;
use App\Models\User;

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

        // Start from count+1 for efficiency, then loop until we find an ID
        // that doesn't exist in either franchises table or users table.
        // This handles the case where franchises were manually deleted but
        // their linked user records still exist (orphaned users).
        $seq = Franchise::where('institute_id', $instituteId)
                ->whereYear('created_at', $year)
                ->count() + 1;

        do {
            $uid = 'FRN' . $year . str_pad($seq, 4, '0', STR_PAD_LEFT);
            $seq++;
        } while (
            Franchise::where('unique_id', $uid)->exists() ||
            User::where('user_id', $uid . '/HEAD')->exists()
        );

        return $uid;
    }

    public function generateFranchiseFeeInvoice(int $instituteId, int $franchiseId): string
    {
        $year = now()->year;
        // Institute-wide sequential count (not per-franchise) to avoid duplicates
        // since invoice_no has a global unique constraint
        $seq = FranchiseFeeCollection::where('institute_id', $instituteId)
            ->whereYear('created_at', $year)
            ->count() + 1;

        do {
            $invoice = "FR-FEE/{$year}/" . str_pad($seq, 4, '0', STR_PAD_LEFT);
            $seq++;
        } while (FranchiseFeeCollection::where('invoice_no', $invoice)->exists());

        return $invoice;
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
