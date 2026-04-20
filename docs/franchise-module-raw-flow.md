# Franchise Module Raw Flow

## Goal

Is document ka purpose hai institute ke andar franchise system ka raw blueprint define karna. Abhi focus sirf `Franchise Panel` par rahega. Enrollment, certificate, ledger, payout, reports jaise modules ko future-ready way me socha jayega, lekin implementation phase-by-phase hogi.

Current direction:

- Super Admin abhi ke liye sirf institute create karega.
- Institute apne under franchise levels aur franchises create karega.
- Institute ke khud ke students/staff alag rahenge.
- Har franchise ke khud ke students/staff alag rahenge.
- Har entity ka apna login/panel hoga, but first implementation franchise admin panel se start hogi.

## Existing System Status

Institute panel me abhi ye modules usable hain:

- Session Management
- Course Management
- Subject Management
- Course + Subject Binding with Max Marks
- Fee Types
- Payment Plans
- Form Builder

Known partial area:

- Enrollment me `new` aur `existing` step dikh raha hai, uske baad flow incomplete/error state me hai.

Conclusion:

- Franchise module ko existing enrollment code par depend karke nahi banana chahiye.
- Franchise architecture pehle alag se define karna better hai.

## Core Business Structure

System me 4 practical layers hongi:

1. Super Admin
2. Institute
3. Franchise
4. End Users

End users 2 type ke honge:

- Staff
- Student

Important ownership rule:

- Institute ke apne staff/students ho sakte hain.
- Franchise ke apne staff/students ho sakte hain.
- Franchise hamesha kisi ek institute ke under hogi.
- Franchise ka level institute define karega.

## Recommended MVP Scope

Phase 1 me sirf ye banana chahiye:

1. Franchise Level Management
2. Franchise Create / Edit / Status
3. Franchise Login Credentials
4. Franchise Wallet Setup
5. Franchise Recharge
6. Franchise Transaction Ledger
7. Franchise Dashboard

Phase 1 me abhi ye hold par rahe:

- Franchise student admission
- Franchise certificate issue
- Franchise commission payout
- Offer engine automation
- Franchise staff/student CRUD
- Franchise reports

Reason:

- Agar hum pehle wallet + level + franchise identity clear kar denge, to baaki modules easily plug honge.

## Panel Structure

### 1. Super Admin Panel

Abhi ke liye simple rahe:

- institute create karega
- institute credentials bhejega
- subscription manage karega

Franchise creation super admin panel se nahi hoga.

### 2. Institute Panel

Institute ke andar franchise related menu:

- Franchise Levels
- Franchises
- Franchise Wallet Recharge
- Franchise Transactions
- Franchise Reports

Institute control karega:

- kaun sa level available hai
- kis level par kitna commission ya pricing rule hai
- franchise wallet use karegi ya nahi
- admission/certificate charge kitna hoga
- recharge bonus dena hai ya nahi

### 3. Franchise Panel

Initial franchise panel:

- Dashboard
- Profile
- Wallet Summary
- Recharge History
- Transaction Ledger

Future franchise panel:

- Students
- Staff
- Admissions
- Certificates
- Fee Collection
- Reports

## Recommended Business Logic

### A. Franchise Level

`Level` ka use sirf naming ke liye nahi hona chahiye. Iska business meaning hona chahiye.

Recommended fields:

- Level Name
- Commission Type
- Commission Value
- Default Admission Charge
- Default Certificate Charge
- Priority / Order
- Status

Example:

- Silver: commission 10%
- Gold: commission 15%
- Platinum: commission 20%

MVP suggestion:

- Abhi commission ko sirf configuration/reporting ke liye store karo.
- Actual payout calculation baad me implement karo.

### B. Wallet System

Franchise create karte waqt institute decide karega:

- wallet system enabled hai ya nahi

#### If wallet disabled

- Franchise normal operations karegi
- Wallet balance maintain nahi hoga
- Admission/certificate par automatic cut nahi hoga
- Reporting ho sakti hai, deduction nahi

#### If wallet enabled

- Institute franchise ka opening wallet balance dalega
- Per admission deduction amount define karega
- Per certificate deduction amount define karega
- Future me kisi aur service ke liye bhi deduction rules add kiye ja sakte hain

### C. Recharge

Recharge manual hoga:

1. Franchise institute ko cash/UPI/NEFT degi
2. Institute panel se recharge entry karega
3. Wallet me amount credit hoga
4. Agar offer hai to bonus amount bhi add hoga
5. Ledger me full entry save hogi

Recommended recharge model:

- paid_amount
- bonus_amount
- credit_amount = paid_amount + bonus_amount

Example:

- Paid: 5000
- Bonus: 3000
- Wallet Credit: 8000

### D. Charges

Wallet-enabled franchise ke liye future deductions:

- new admission charge
- certificate generation charge
- optional exam charge
- optional migration/verification charge

MVP me store to all charges karna chahiye, but actual automatic deduction Phase 2 me start karna better hai.

## Recommended Data Ownership Model

Current system me `users` table institute-based hai. Franchise ko support karne ke liye ownership model explicit karna hoga.

Best approach:

- `users` table ko future-proof banao
- har user ke saath ye identify ho:
  - institute_id
  - franchise_id nullable
  - panel_scope / owner_type

Interpretation:

- `franchise_id = null` means direct institute user
- `franchise_id != null` means franchise user

## Database Design

## New Tables Required

### 1. franchise_levels

Purpose:

- Institute-wise levels define karna

Suggested columns:

- id
- institute_id
- name
- code nullable
- commission_type enum(`PERCENT`, `FLAT`, `NONE`)
- commission_value decimal(11,2) default 0
- default_admission_charge decimal(11,2) default 0
- default_certificate_charge decimal(11,2) default 0
- notes nullable
- sort_order integer default 0
- status enum(`active`, `inactive`)
- timestamps

Notes:

- Ye table institute scoped hogi.
- Har institute apne custom levels bana sakta hai.

### 2. franchises

Purpose:

- Institute ke under franchise master record

Suggested columns:

- id
- institute_id
- franchise_level_id nullable
- unique_id
- name
- owner_name
- email
- mobile
- password_send_to_email boolean default 1
- address nullable
- state nullable
- pin_code nullable
- logo nullable
- wallet_enabled boolean default 0
- opening_balance decimal(11,2) default 0
- admission_charge decimal(11,2) default 0
- certificate_charge decimal(11,2) default 0
- joining_date nullable
- status enum(`active`, `inactive`, `suspended`)
- slug
- timestamps

Notes:

- `admission_charge` and `certificate_charge` yahin freeze ho sakte hain.
- Level ke defaults yahan copy karke override allow karna useful rahega.

### 3. franchise_wallets

Purpose:

- Har franchise ka current wallet balance

Suggested columns:

- id
- franchise_id unique
- institute_id
- balance decimal(11,2) default 0
- timestamps

Notes:

- `institute_id` duplicate lag sakta hai but reporting/query fast ho jayegi.

### 4. franchise_transactions

Purpose:

- Franchise wallet ledger

Suggested columns:

- id
- institute_id
- franchise_id
- txn_no nullable
- description
- credit decimal(11,2) default 0
- debit decimal(11,2) default 0
- type tinyInteger
- ref_type nullable
- ref_id nullable
- payment_mode nullable
- utr nullable
- paid_amount decimal(11,2) default 0
- bonus_amount decimal(11,2) default 0
- op_bal decimal(11,2) default 0
- cl_bal decimal(11,2) default 0
- txn_date date
- c_date datetime
- by_user_id nullable
- timestamps

Type suggestion:

- 1 = opening_balance
- 2 = recharge
- 3 = bonus_credit
- 4 = admission_charge_debit
- 5 = certificate_charge_debit
- 6 = manual_adjustment
- 7 = refund

### 5. franchise_recharge_offers

Purpose:

- Festival ya special recharge offers define karna

Suggested columns:

- id
- institute_id
- title
- min_paid_amount decimal(11,2)
- bonus_amount decimal(11,2) default 0
- start_date
- end_date
- status enum(`active`, `inactive`)
- timestamps

MVP note:

- Is table ko Phase 1 me skip bhi kar sakte hain.
- Agar skip karein to recharge screen me manual bonus field enough rahegi.

## Existing Tables Me Recommended Changes

### 1. users table

Current problem:

- User sirf institute-level concept par based hai.

Recommended new columns:

- franchise_id nullable
- panel_type enum(`institute`, `franchise`) default `institute`

Why needed:

- Institute head/staff aur franchise head/staff/student ko same auth system me handle kar sakenge.

Future interpretation:

- institute panel user: `panel_type = institute`
- franchise panel user: `panel_type = franchise`

### 2. student_profiles table

Recommended add:

- franchise_id nullable
- created_under enum(`institute`, `franchise`) default `institute`

Why:

- Student direct institute ka hai ya franchise ka, ye clear rahega.

### 3. staff_profiles table

Recommended add:

- franchise_id nullable
- created_under enum(`institute`, `franchise`) default `institute`

### 4. course_books / enrollment-related tables

Future-ready recommended add:

- franchise_id nullable

Why:

- Admission institute ne liya ya franchise ne liya, ye track hoga.

### 5. fee_collect_details

Future-ready recommended add:

- franchise_id nullable

Why:

- Fee source institute ka tha ya franchise ka, report me kaam aayega.

## Important Architectural Decision

Do we make separate `franchise_users` table?

Recommendation: `No`

Reason:

- already `users` auth chal raha hai
- duplicate auth system avoid hoga
- future me staff/student common logic easy rahega

Bas `users` table ko ownership aware banana hoga.

## Raw Franchise Creation Flow

### Step 1. Institute creates Franchise Level

Institute fills:

- level name
- commission type/value
- default admission charge
- default certificate charge
- status

Output:

- level saved

### Step 2. Institute creates Franchise

Institute fills:

- franchise basic details
- selected level
- wallet enabled yes/no
- opening balance
- admission charge
- certificate charge

System actions:

1. franchise record create
2. franchise head login user create
3. franchise wallet create
4. if opening balance > 0 then transaction entry create
5. credentials email/send flow run

### Step 3. Franchise login

Franchise can see:

- profile
- level
- wallet balance
- recharge history
- transaction history

### Step 4. Institute recharge franchise wallet

Institute fills:

- franchise
- payment mode
- amount paid
- bonus amount
- note
- transaction date

System actions:

1. wallet credit
2. transaction save
3. closing balance update

### Step 5. Future charge deduction events

Later when we implement admission/certificate:

- franchise student admission
- certificate issue

Then system will:

1. read franchise wallet settings
2. check wallet enabled or not
3. if enabled, debit configured amount
4. write transaction ledger
5. if insufficient balance, block action or allow pending mode based on config

## Wallet Policy Recommendation

MVP ke liye safest policy:

- insufficient wallet balance par admission/certificate block karo

Reason:

- balance negative allow karne se reconciliation complex ho jayega
- ledger aur recharge disputes badhenge

Future option:

- `allow_negative_balance` setting add ki ja sakti hai

## Suggested Menus For Institute Panel

- Franchise Dashboard
- Franchise Levels
- Franchise List
- Add Franchise
- Wallet Recharge
- Franchise Transactions

## Suggested Menus For Franchise Panel

Phase 1:

- Dashboard
- Profile
- Wallet
- Transactions

Phase 2:

- Students
- Staff
- Admissions
- Certificates
- Fee Collection

## Suggested Route Strategy

Existing route design me 2 major panels already hain:

- owner
- institute

Franchise ke liye new prefix add kiya ja sakta hai:

- `franchise/*`

Guard options:

### Option A. Same `institute` guard reuse

Use same `users` table, but middleware me role + panel type check karein.

Pros:

- simple auth reuse

Cons:

- permission checks thode strict likhne padenge

### Option B. Separate franchise guard

Users same table me rahenge but guard alag define hoga.

Pros:

- cleaner route separation

Cons:

- config thoda extra

Recommendation:

- Phase 1 ke liye `same users table + separate franchise route group` best rahega

## Minimal Role Plan

Current roles:

- institute_head
- staff
- student

Recommended additions:

- franchise_head
- franchise_staff
- franchise_student

Why:

- role checks readable rahenge
- reporting clean hogi

## MVP Tables Summary

Phase 1 me likely enough:

New tables:

- franchise_levels
- franchises
- franchise_wallets
- franchise_transactions

Current tables changes:

- users

Optional future-ready changes now:

- student_profiles
- staff_profiles
- course_books
- fee_collect_details

## Recommended Implementation Order

1. DB schema for franchise levels + franchises + wallet + transactions
2. Franchise models and relations
3. Institute panel franchise level CRUD
4. Institute panel franchise CRUD
5. Franchise auth/login routing
6. Franchise dashboard
7. Franchise wallet recharge
8. Franchise transaction history

## My Recommendation For Now

Agar hum practical aur safe tareeke se chalna chahte hain, to next coding step ye hona chahiye:

1. Franchise DB structure finalize
2. Franchise Level module build
3. Franchise Create module build
4. Franchise Wallet module build

Is order me chalne se har next module previous module par naturally depend karega.

## Open Decisions Before Coding

Ye 4 decisions implementation start karne se pehle lock karne chahiye:

1. Franchise ke login ke liye same login page use hoga ya alag
2. Franchise head user `users` table me hi create hoga ya separate table me
3. Wallet disabled franchise ko admission allowed hoga ya sirf reporting mode me rahega
4. Level commission abhi sirf config rahega ya immediate calculation bhi hogi

## Recommended Answer To Open Decisions

Meri recommendation:

1. Same login page
2. Same `users` table
3. Wallet disabled means operations allowed, no deduction
4. Commission abhi config/reporting only

Ye approach fastest aur least risky hai.
