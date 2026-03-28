<?php

namespace App\Services;

use App\Models\Owner\{
    Feature, Institute, InstituteFeature,
    InstituteSubscription, InstituteWallet, InstitutePayCollect
};
use App\Models\Owner\Plan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\InstituteWelcomeMail;

class InstituteOnboardingService
{
    public function __construct(
        protected WalletService  $walletService,
        protected InvoiceService $invoiceService,
    ) {}

    /**
     * Full onboarding flow inside a DB transaction.
     * Returns the newly created Institute.
     */
    public function create(array $data): Institute
    {
        return DB::transaction(function () use ($data) {

            // ── 1. Generate random plain-text password ───────────────────────
            $plainPassword = $this->generatePassword();

            // ── 2. Create Institute record ────────────────────────────────────
            $institute = Institute::create([
                'unique_id'    => $this->invoiceService->generateInstituteUniqueId(),
                'name'         => $data['name'],
                'short_name'   => $data['short_name']  ?? null,
                'email'        => $data['email'],
                'mobile'       => $data['mobile'],
                'owner_name'   => $data['owner_name'],
                'owner_mobile' => $data['owner_mobile'],
                'logo'         => $data['logo'] ?? 'images/default-institute.png',
                'address'      => $data['address']     ?? null,
                'state'        => $data['state']        ?? null,
                'pin_code'     => $data['pin_code']     ?? null,
                'website'      => $data['website']      ?? null,
                'type'         => $data['type']         ?? 'PRIVATE',
                'status'       => 'active',
                'slug'         => $this->makeSlug($data['name']),
            ]);

            // ── 3. Create institute head user ─────────────────────────────────
            $user = User::create([
                'user_id'      => $institute->unique_id . '/HEAD',
                'name'         => $data['owner_name'],
                'mobile'       => $data['owner_mobile'],
                'email'        => $data['email'],
                'password'     => Hash::make($plainPassword),
                'role'         => 'institute_head',
                'institute_id' => $institute->id,
                'status'       => 'active',
            ]);

            // ── 4. Create wallet (balance = 0) ────────────────────────────────
            InstituteWallet::create([
                'institute_id' => $institute->id,
                'main_b'       => 0.00,
            ]);

            // ── 5. Amounts ────────────────────────────────────────────────────
            $plan        = Plan::with('features')->findOrFail($data['plan_id']);
            $addonIds    = $data['addon_feature_ids'] ?? [];
            $addonFeatures = Feature::whereIn('id', $addonIds)->get();
            $addonTotal  = $addonFeatures->sum('price');
            $subtotal    = (float)$plan->price + $addonTotal;

            $discountType   = $data['discount_type']  ?? 'NONE';
            $discountValue  = (float)($data['discount_value'] ?? 0);
            $discountAmount = match ($discountType) {
                'PERCENT' => round(($subtotal * $discountValue) / 100, 2),
                'FLAT'    => $discountValue,
                default   => 0.00,
            };
            $finalPrice = $subtotal - $discountAmount;

            // ── 6. Create subscription ────────────────────────────────────────
            $subscription = InstituteSubscription::create([
                'institute_id'    => $institute->id,
                'plan_id'         => $plan->id,
                'start_date'      => now()->toDateString(),
                'end_date'        => now()->addMonths($plan->duration)->toDateString(),
                'price'           => $plan->price,
                'discount_type'   => $discountType,
                'discount_value'  => $discountValue,
                'discount_amount' => $discountAmount,
                'final_price'     => $finalPrice,
                'status'          => 'active',
            ]);

            // ── 7. Plan-included features (price = 0) ─────────────────────────
            foreach ($plan->features as $feature) {
                InstituteFeature::create([
                    'institute_id'              => $institute->id,
                    'institute_subscription_id' => $subscription->id,
                    'feature_id'                => $feature->id,
                    'price'                     => 0.00,
                    'is_addon'                  => 0,
                ]);
            }

            // ── 8. Add-on features ────────────────────────────────────────────
            $planFeatureIds = $plan->features->pluck('id')->toArray();
            foreach ($addonFeatures as $feature) {
                if (!in_array($feature->id, $planFeatureIds)) {
                    InstituteFeature::create([
                        'institute_id'              => $institute->id,
                        'institute_subscription_id' => $subscription->id,
                        'feature_id'                => $feature->id,
                        'price'                     => $feature->price,
                        'is_addon'                  => 1,
                    ]);
                }
            }

            // ── 9. Wallet transactions ────────────────────────────────────────
            // 9a. Plan debit
            $this->walletService->debit(
                $institute->id,
                (float)$plan->price,
                "Plan Subscription: {$plan->name} ({$plan->duration} months)",
                1
            );

            // 9b. Each addon debit
            foreach ($addonFeatures as $feature) {
                if (!in_array($feature->id, $planFeatureIds)) {
                    $this->walletService->debit(
                        $institute->id,
                        (float)$feature->price,
                        "Add-on Feature: {$feature->name}",
                        2
                    );
                }
            }

            // 9c. Discount credit
            if ($discountAmount > 0) {
                $label = $discountType === 'PERCENT'
                    ? "Discount: {$discountValue}% on ₹{$subtotal}"
                    : "Discount: ₹{$discountValue} flat";
                $this->walletService->credit($institute->id, $discountAmount, $label, 4);
            }

            // ── 10. Send welcome email ────────────────────────────────────────
            try {
                Mail::to($institute->email)->send(
                    new InstituteWelcomeMail($institute, $user, $plainPassword)
                );
            } catch (\Throwable $e) {
                // Log but don't fail the transaction
                logger()->error("Welcome mail failed for institute {$institute->id}: " . $e->getMessage());
            }

            return $institute;
        });
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function generatePassword(int $length = 10): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ0123456789';
        return substr(str_shuffle(str_repeat($chars, 4)), 0, $length);
    }

    private function makeSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i    = 1;
        while (Institute::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
