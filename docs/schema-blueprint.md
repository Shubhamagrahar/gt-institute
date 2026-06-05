# Schema Blueprint

## Why this file

Current project me old institute-only tables aur new franchise-ready tables dono parallel chal rahe hain. Isliye same domain ka data multiple jagah store ho raha hai:

- `student_profiles` vs `user_profiles`
- `wallets` / `transactions` vs `student_wallets` / `student_transactions`
- institute-owned aur franchise-owned students ka ownership explicit nahi hai

Real client system ke liye pehle canonical schema lock karna zaroori hai. Ye file wahi final direction define karti hai.

## Final hierarchy

System ki ownership chain:

1. `super_admins`
2. `institutes`
3. `franchises`
4. `users`

Business rules:

- super admin institute create karega
- institute apne under franchise create karega
- institute ke direct staff/student alag honge
- franchise ke staff/student alag honge
- har end-user ek institute ke under hoga
- franchise user ke liye `franchise_id` required hoga

## Canonical tables

### 1. Super admin domain

#### `super_admins`

Purpose:

- owner panel login

Important fields:

- `id`
- `admin_id`
- `name`
- `email`
- `mobile`
- `password`
- `status`

### 2. Subscription and feature domain

#### `plans`

- `id`
- `name`
- `price`
- `duration`
- `description`
- `status`

#### `features`

- `id`
- `name`
- `slug`
- `description`
- `price`
- `status`

#### `plan_features`

- `id`
- `plan_id`
- `feature_id`

#### `institute_subscriptions`

- `id`
- `institute_id`
- `plan_id`
- `start_date`
- `end_date`
- `price`
- `discount_type`
- `discount_value`
- `discount_amount`
- `final_price`
- `status`

#### `institute_features`

- `id`
- `institute_id`
- `institute_subscription_id`
- `feature_id`
- `price`
- `is_addon`

### 3. Institute domain

#### `institutes`

Purpose:

- institute master table, created by super admin

Important fields:

- `id`
- `unique_id`
- `name`
- `short_name`
- `email`
- `mobile`
- `owner_name`
- `owner_mobile`
- `logo`
- `address`
- `state`
- `pin_code`
- `website`
- `status`
- `slug`

### 4. Franchise domain

#### `franchise_levels`

Purpose:

- institute-wise franchise classification

Important fields:

- `id`
- `institute_id`
- `name`
- `commission_percent`
- `notes`
- `status`

Recommended future enhancement:

- `commission_type`
- `commission_value`
- `default_admission_charge`
- `default_certificate_charge`
- `sort_order`

#### `franchises`

Purpose:

- institute ke under franchise master

Important fields:

- `id`
- `institute_id`
- `franchise_level_id`
- `unique_id`
- `name`
- `short_name`
- `email`
- `mobile`
- `owner_name`
- `owner_mobile`
- `address`
- `state`
- `pin_code`
- `website`
- `commission_percent`
- `wallet_enabled`
- `low_wallet_alert`
- `has_sub_franchise`
- `status`
- `slug`

### 5. User domain

#### `users`

Purpose:

- sirf auth + ownership + account status

Important fields:

- `id`
- `user_id`
- `mobile`
- `email`
- `password`
- `role`
- `user_type`
- `institute_id`
- `franchise_id`
- `owner_type`
- `status`

Canonical meaning:

- `role` panel and permission ke liye
- `user_type` business identity ke liye
- `owner_type = institute` means direct institute user
- `owner_type = franchise` means franchise-owned user

Recommended role set:

- `institute_head`
- `staff`
- `student`
- `franchise_head`
- `franchise_staff`
- `franchise_student`

Recommended `user_type`:

- `staff`
- `student`

Recommended `owner_type`:

- `institute`
- `franchise`

### 6. User profile domain

#### `user_profiles`

Purpose:

- unified personal profile

Important fields:

- `id`
- `user_id`
- `institute_id`
- `franchise_id`
- `name`
- `photo`
- `father_name`
- `mother_name`
- `guardian_name`
- `guardian_relation`
- `guardian_mobile`
- `dob`
- `gender`
- `category`
- `religion`
- `nationality`
- `whatsapp_no`
- `alternate_mobile`
- `aadhar_no`
- `pan_no`
- `blood_group`
- `employment_status`
- `computer_literacy`
- `qualification`
- `address`
- `permanent_address`
- `state`
- `district`
- `pin_code`

Student-specific fields that should also live here:

- `reg_no`
- `admission_no`
- `roll_no`
- `fee_collect_type`
- `monthly_fee`
- `daily_late_fee`
- `late_fee_count_after`
- `next_fee_date`
- `issue_date`
- `valid_till_date`
- `r_date`

#### `user_education`

Purpose:

- user ke education records, one-to-many

Important fields:

- `id`
- `user_id`
- `institute_id`
- `franchise_id`
- `examination`
- `board_university`
- `passing_year`
- `marks_percentage`

## Dynamic admission form

Canonical design:

- `admission_form_fields` master config table hi source of truth rahe
- actual user data `users`, `user_profiles`, `user_education` me hi save ho
- form builder decide karega kaun se fields visible/required hain

Rule:

- field config alag table me
- field values domain tables me

## Academic domain

#### `course_types`
- institute-scoped

#### `course_details`
- institute-scoped

#### `batch_details`
- institute-scoped

#### `course_books`

Purpose:

- enrollment master

Important fields:

- `id`
- `institute_id`
- `franchise_id`
- `session_id`
- `user_id`
- `course_id`
- `batch_id`
- `enrollment_no`
- `final_fee`
- `status`
- `admission_by`

Ownership rule:

- direct institute enrollment: `franchise_id = null`
- franchise admission: `franchise_id != null`

#### `enrollment_fee_snapshots`

- fee breakup snapshot at admission time

#### `enrollment_payment_plans`

- OTP / PART / MONTHLY details

## Student finance domain

#### `student_wallets`

Purpose:

- student due / advance balance

Important fields:

- `id`
- `user_id`
- `institute_id`
- `franchise_id`
- `owner_type`
- `balance`

Interpretation:

- negative balance = due
- positive balance = advance

#### `student_transactions`

Purpose:

- student personal ledger

Important fields:

- `id`
- `user_id`
- `institute_id`
- `franchise_id`
- `owner_type`
- `description`
- `credit`
- `debit`
- `type`
- `ref_type`
- `ref_id`
- `date`
- `c_date`
- `op_bal`
- `cl_bal`
- `by_user_id`

#### `fee_collect_details`

Purpose:

- fee receipt table

Important fields:

- `id`
- `institute_id`
- `franchise_id`
- `user_id`
- `course_book_id`
- `invoice_no`
- `payment_mode`
- `utr`
- `amount`
- `date`
- `note`
- `received_by`

## Institute earnings domain

#### `institute_student_wallets`

Purpose:

- institute ki student-side earning pool

Important fields:

- `id`
- `institute_id`
- `balance`

#### `institute_student_transactions`

Purpose:

- student fee se institute me aane wala ledger

Important fields:

- `id`
- `institute_id`
- `franchise_id`
- `ref_user_id`
- `description`
- `credit`
- `debit`
- `type`
- `date`
- `c_date`
- `op_bal`
- `cl_bal`
- `by_user_id`

Use case:

- student ne fee di
- student wallet me due kam hua
- institute earning wallet me credit hua

## Institute vs platform finance domain

#### `institute_wallets`

Purpose:

- super admin vs institute commercial ledger balance

#### `institute_transactions`

Purpose:

- plan debit / addon debit / payment / discount ledger

#### `institute_pay_collects`

Current meaning:

- institute se receive hui payment details

Recommended canonical name:

- `institute_pay_details`

Note:

- abhi project me destructive rename avoid karna better hai
- new code me `institute_pay_collects` ko hi canonical मानकर चलना safer hai until dedicated migration window

## Institute vs franchise finance domain

#### `franchise_wallets`

Purpose:

- franchise usable wallet balance

#### `franchise_transactions`

Purpose:

- franchise wallet ledger

#### `institute_franchise_wallets`

Purpose:

- institute point of view se franchise account balance

#### `institute_franchise_transactions`

Purpose:

- institute ne franchise ke saath jo commercial settlement kiya uska ledger

Examples:

- opening balance
- recharge
- bonus
- admission charge deduction
- refund

## Deprecation direction

Ye tables gradually retire karni chahiye:

- `student_profiles`
- `staff_profiles`
- `wallets`
- `transactions`

Reason:

- inki responsibility already `user_profiles`, `student_wallets`, `student_transactions` cover kar rahe hain

## Recommended implementation order

1. New schema ko canonical mark karo
2. New create/update flows ko sirf canonical tables par shift karo
3. Old read screens me compatibility bridge lagao
4. Existing data ka backfill script banao
5. Old tables ko read-only period ke baad retire karo

## Immediate coding rule

New code ke liye:

- auth data `users`
- personal data `user_profiles`
- education data `user_education`
- student balance `student_wallets`
- student ledger `student_transactions`
- fee receipt `fee_collect_details`
- institute earnings `institute_student_wallets` / `institute_student_transactions`
- franchise balance `franchise_wallets` / `franchise_transactions`
- institute-franchise settlement `institute_franchise_wallets` / `institute_franchise_transactions`
