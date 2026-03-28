<?php

namespace App\Services;

use App\Models\Owner\InstituteWallet;
use App\Models\Owner\InstituteTransaction;
use Illuminate\Support\Facades\DB;

class WalletService
{
    /**
     * Debit from institute wallet and log transaction.
     */
    public function debit(int $instituteId, float $amount, string $des, int $type, ?string $invoiceNo = null): void
    {
        $wallet = InstituteWallet::where('institute_id', $instituteId)->lockForUpdate()->firstOrFail();

        $opBal = (float) $wallet->main_b;
        $clBal = $opBal - $amount;

        InstituteTransaction::create([
            'institute_id' => $instituteId,
            'des'          => $des,
            'credit'       => 0.00,
            'debit'        => $amount,
            'type'         => $type,
            'date'         => now()->toDateString(),
            'c_date'       => now(),
            'op_bal'       => $opBal,
            'cl_bal'       => $clBal,
            'invoice_no'   => $invoiceNo,
            'by_userid'    => auth()->id() ?? 1,
        ]);

        $wallet->update(['main_b' => $clBal]);
    }

    /**
     * Credit to institute wallet and log transaction.
     */
    public function credit(int $instituteId, float $amount, string $des, int $type, ?string $invoiceNo = null): void
    {
        $wallet = InstituteWallet::where('institute_id', $instituteId)->lockForUpdate()->firstOrFail();

        $opBal = (float) $wallet->main_b;
        $clBal = $opBal + $amount;

        InstituteTransaction::create([
            'institute_id' => $instituteId,
            'des'          => $des,
            'credit'       => $amount,
            'debit'        => 0.00,
            'type'         => $type,
            'date'         => now()->toDateString(),
            'c_date'       => now(),
            'op_bal'       => $opBal,
            'cl_bal'       => $clBal,
            'invoice_no'   => $invoiceNo,
            'by_userid'    => auth()->id() ?? 1,
        ]);

        $wallet->update(['main_b' => $clBal]);
    }
}
