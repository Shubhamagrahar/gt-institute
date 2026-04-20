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

    public function create(array $data, int $instituteId, int $createdBy): Franchise
    {
        return DB::transaction(function () use ($data, $instituteId, $createdBy) {
            $plainPassword = $this->generatePassword();
            $openingBalance = (float) ($data['opening_balance'] ?? 0);

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
                'wallet_enabled' => (bool) ($data['wallet_enabled'] ?? true),
                'low_wallet_alert' => $data['low_wallet_alert'] ?? 1000,
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
                'institute_id' => $instituteId,
                'franchise_id' => $franchise->id,
                'status' => 'active',
            ]);

            UserProfile::create([
                'user_id' => $user->id,
                'name' => $data['owner_name'],
                'address' => $data['address'] ?? null,
                'state' => $data['state'] ?? null,
                'pin_code' => $data['pin_code'] ?? null,
            ]);

            FranchiseWallet::create([
                'franchise_id' => $franchise->id,
                'institute_id' => $instituteId,
                'balance' => $franchise->wallet_enabled ? $openingBalance : 0,
            ]);

            if ($franchise->wallet_enabled && $openingBalance > 0) {
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
