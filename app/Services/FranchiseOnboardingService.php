<?php

namespace App\Services;

use App\Mail\FranchiseWelcomeMail;
use App\Models\CourseDetail;
use App\Models\Franchise;
use App\Models\FranchiseCourseCharge;
use App\Models\FranchiseJoiningWallet;
use App\Models\FranchiseTransaction;
use App\Models\FranchiseWallet;
use App\Models\LevelCourseCharge;
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
                'district' => $data['district'] ?? null,
                'pin_code' => $data['pin_code'] ?? null,
                'website' => $data['website'] ?? null,
                'commission_percent' => $data['commission_percent'] ?? 0,
                'management_type' => $mgmtType,
                'wallet_enabled' => $isWalletMode ? ((int) ($data['wallet_enabled'] ?? 1) !== 0) : false,
                'low_wallet_alert' => $isWalletMode ? ($data['low_wallet_alert'] ?? 1000) : 0,
                'onboarding_fee' => ! $isWalletMode ? ($data['onboarding_fee'] ?? 0) : 0,
                'fee_total' => $levelFee,  // level fee applies to ALL modes
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

            // Operational wallet (admission/certificate deductions — wallet mode only)
            FranchiseWallet::create([
                'franchise_id' => $franchise->id,
                'institute_id' => $instituteId,
                'balance' => $isWalletMode ? $openingBalance : 0,
            ]);

            // Joining fee wallet — tracks level fee outstanding for ALL modes
            FranchiseJoiningWallet::create([
                'franchise_id' => $franchise->id,
                'institute_id' => $instituteId,
                'total_due'    => $levelFee,
                'total_paid'   => 0,
                'balance'      => $levelFee,
            ]);

            // Copy level course charges → franchise course charges for selected course types
            if ($isWalletMode && ! empty($data['franchise_level_id'])) {
                $selectedTypeIds = $data['_course_type_access'] ?? [];
                $customCharges   = $data['_course_charges'] ?? [];

                $levelQuery = LevelCourseCharge::where('franchise_level_id', $data['franchise_level_id'])
                    ->where('level_course_charges.status', 'active')
                    ->join('course_details', 'course_details.id', '=', 'level_course_charges.course_id')
                    ->select('level_course_charges.*', 'course_details.course_type_id as ct_id');

                if (! empty($selectedTypeIds)) {
                    $levelQuery->whereIn('course_details.course_type_id', $selectedTypeIds);
                }

                foreach ($levelQuery->get() as $lcc) {
                    $override = $customCharges[$lcc->course_id] ?? null;

                    FranchiseCourseCharge::updateOrCreate(
                        ['franchise_id' => $franchise->id, 'course_id' => $lcc->course_id],
                        [
                            'institute_id'       => $instituteId,
                            'course_type_id'     => $lcc->ct_id,
                            'course_name'        => $lcc->course_name,
                            'duration'           => $lcc->duration,
                            'admission_charge'   => $override ? $override['admission'] : $lcc->student_admission_charge,
                            'certificate_charge' => $override ? $override['certificate'] : $lcc->student_certificate_charge,
                            'student_fee'        => null,
                            'enabled'            => true,
                        ]
                    );
                }
            }

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

    public function generatePassword(int $length = 10): string
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
