<?php

namespace App\Services;

use App\Mail\FranchiseWelcomeMail;
use App\Models\Franchise;
use App\Models\FranchiseTransaction;
use App\Models\FranchiseWallet;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class FranchiseOnboardingService
{
    public function __construct(
        protected InvoiceService $invoiceService,
    ) {}

    public function create(array $data, int $instituteId, int $createdBy, float $levelFee = 0): Franchise
    {
        return DB::transaction(function () use ($data, $instituteId, $createdBy, $levelFee) {
            $plainPassword = $this->generatePassword();
            $openingBalance = (float) ($data['opening_balance'] ?? 0);

            $mgmtType = $data['management_type'] ?? 'wallet';
            $isWalletMode = $mgmtType === 'wallet';

            $franchise = Franchise::create([
                'institute_id' => $instituteId,
                'franchise_level_id' => $data['franchise_level_id'] ?? null,
                'unique_id' => $this->invoiceService->generateFranchiseUniqueId($instituteId),
                'name' => $data['name'],
                'short_name' => $data['short_name'] ?? null,
                'email' => $data['email'],
                'mobile' => $data['mobile'],
                'owner_name' => $data['owner_name'],
                'owner_mobile' => $data['owner_mobile'],
                'logo' => $data['logo'] ?? 'images/default-institute.png',
                'address' => $data['address'] ?? null,
                'state' => $data['state'] ?? null,
                'pin_code' => $data['pin_code'] ?? null,
                'website' => $data['website'] ?? null,
                'commission_percent' => $data['commission_percent'] ?? 0,
                'management_type' => $mgmtType,
                'wallet_enabled' => $isWalletMode ? ((int) ($data['wallet_enabled'] ?? 1) !== 0) : false,
                'low_wallet_alert' => $isWalletMode ? ($data['low_wallet_alert'] ?? 1000) : 0,
                'admission_charge' => $isWalletMode ? ($data['admission_charge'] ?? 0) : 0,
                'certificate_charge' => $isWalletMode ? ($data['certificate_charge'] ?? 0) : 0,
                'onboarding_fee' => ! $isWalletMode ? ($data['onboarding_fee'] ?? 0) : 0,
                'fee_total' => ! $isWalletMode ? $levelFee : 0,
                'has_sub_franchise' => (bool) ($data['has_sub_franchise'] ?? false),
                'status' => 'active',
                'slug' => $this->makeSlug($data['name']),
            ]);

            $user = User::create([
                'user_id' => $franchise->unique_id . '/HEAD',
                'mobile' => $data['owner_mobile'],
                'email' => $data['email'],
                'password' => $plainPassword,
                'role' => 'franchise_head',
                'user_type' => 'staff',
                'institute_id' => $instituteId,
                'franchise_id' => $franchise->id,
                'owner_type' => 'franchise',
                'status' => 'active',
            ]);

            UserProfile::create([
                'user_id' => $user->id,
                'institute_id' => $instituteId,
                'franchise_id' => $franchise->id,
                'name' => $data['owner_name'],
                'address' => $data['address'] ?? null,
                'state' => $data['state'] ?? null,
                'pin_code' => $data['pin_code'] ?? null,
            ]);

            FranchiseWallet::create([
                'franchise_id' => $franchise->id,
                'institute_id' => $instituteId,
                'balance' => $isWalletMode ? $openingBalance : 0,
            ]);

            if ($isWalletMode && $openingBalance > 0) {
                FranchiseTransaction::create([
                    'franchise_id' => $franchise->id,
                    'institute_id' => $instituteId,
                    'txn_no' => $this->invoiceService->generateFranchiseTxnNo($instituteId, $franchise->id),
                    'description' => 'Opening wallet balance credited at franchise creation',
                    'credit' => $openingBalance,
                    'debit' => 0,
                    'type' => 1,
                    'op_bal' => 0,
                    'cl_bal' => $openingBalance,
                    'date' => now()->toDateString(),
                    'c_date' => now(),
                    'by_userid' => $createdBy,
                ]);
            }

            try {
                Mail::to($franchise->email)->send(
                    new FranchiseWelcomeMail($franchise, $user, $plainPassword)
                );
            } catch (\Throwable $e) {
                logger()->error("Franchise welcome mail failed for franchise {$franchise->id}: " . $e->getMessage());
            }

            return $franchise;
        });
    }

    private function generatePassword(int $length = 10): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ0123456789';
        return substr(str_shuffle(str_repeat($chars, 4)), 0, $length);
    }

    private function makeSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (Franchise::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
