# GT Institute Management Software

Franchise-based Computer Institute Management System built with **Laravel 11** + **MySQL**.

---

## 🔑 Default Login

| Field    | Value              |
|----------|--------------------|
| Login    | `admin` (mobile field) |
| Password | `gt@computer@admin` |
| Role     | Super Admin / Owner |

---

## 📁 Project Structure

```
gt-institute/
├── app/
│   ├── Http/Controllers/
│   │   ├── Auth/LoginController.php
│   │   ├── Owner/          ← Super Admin Panel
│   │   └── Institute/      ← Institute Panel
│   ├── Models/Owner/       ← Feature, Plan, Institute, Wallet models
│   ├── Services/
│   │   ├── InstituteOnboardingService.php  ← Full institute creation flow
│   │   ├── WalletService.php               ← Wallet debit/credit
│   │   └── InvoiceService.php              ← Invoice number generator
│   └── Mail/InstituteWelcomeMail.php
├── database/migrations/     ← All 5 migration files
├── database/seeders/        ← Creates owner account + default features
├── public/
│   ├── css/app.css          ← Black theme (DM Sans font)
│   └── js/app.js            ← Dynamic pricing calculator
├── resources/views/
│   ├── layouts/
│   │   ├── owner.blade.php      ← Owner panel layout
│   │   └── institute.blade.php  ← Institute panel layout
│   ├── auth/login.blade.php
│   ├── emails/institute-welcome.blade.php  ← Welcome email
│   ├── owner/               ← Dashboard, Features, Plans, Institutes
│   └── institute/           ← Dashboard, Students, Fee, Courses, Staff, Attendance
└── routes/web.php
```

---

## 🚀 SETUP GUIDE (Step by Step)

### STEP 1 — Requirements

Make sure these are installed on your machine:

- **PHP 8.2+** → `php --version`
- **Composer** → `composer --version`
- **MySQL 5.7+** or MariaDB
- **Git** → `git --version`
- Optional: XAMPP / Laragon / WAMP (for local development)

---

### STEP 2 — Extract & Setup Project

```bash
# 1. Extract the zip to your projects folder
# e.g. C:/xampp/htdocs/gt-institute  (Windows)
# or   /var/www/html/gt-institute    (Linux)

# 2. Go into the folder
cd gt-institute

# 3. Install PHP dependencies
composer install

# 4. Copy environment file
cp .env.example .env

# 5. Generate application key
php artisan key:generate
```

---

### STEP 3 — Database Setup

```bash
# 1. Create database in MySQL
# Open phpMyAdmin or run:
mysql -u root -p
CREATE DATABASE gt_institute;
EXIT;

# 2. Edit .env file - fill in your DB details:
#    DB_DATABASE=gt_institute
#    DB_USERNAME=root
#    DB_PASSWORD=your_mysql_password

# 3. Run migrations (creates all tables)
php artisan migrate

# 4. Run seeder (creates owner account + default features)
php artisan db:seed
```

---

### STEP 4 — Mail Setup (Gmail)

Edit `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_gmail@gmail.com
MAIL_PASSWORD=your_app_password      ← NOT your Gmail password, use App Password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_gmail@gmail.com
MAIL_FROM_NAME="GT Institute"
```

**How to get Gmail App Password:**
1. Go to myaccount.google.com
2. Security → 2-Step Verification → App Passwords
3. Create new → Name it "GT Institute" → Copy the 16-digit password
4. Paste it in `MAIL_PASSWORD`

**Test mail is working:**
```bash
php artisan tinker
Mail::raw('Test email', fn($m) => $m->to('test@example.com')->subject('Test'));
```

---

### STEP 5 — Run Locally

```bash
# Start development server
php artisan serve

# Open in browser:
# http://localhost:8000
```

**Login with:**
- Mobile: `admin`
- Password: `gt@computer@admin`

---

### STEP 6 — Storage Link (for file uploads)

```bash
php artisan storage:link
```

---

## 🗂️ GIT SETUP & DAILY PUSH

### First Time — Initialize Git & Connect to GitHub

```bash
# 1. Go to project folder
cd gt-institute

# 2. Initialize git
git init

# 3. Add all files
git add .

# 4. First commit
git commit -m "Initial commit: GT Institute Laravel project"

# 5. Create repo on GitHub:
#    → Go to github.com → New Repository
#    → Name: gt-institute
#    → Private (recommended, ye aapka business software hai)
#    → Don't add README (we have one)
#    → Click Create

# 6. Connect to GitHub (copy the URL from GitHub)
git remote add origin https://github.com/YOUR_USERNAME/gt-institute.git

# 7. Push to GitHub
git branch -M main
git push -u origin main
```

---

### Daily Git Workflow

Every day jab bhi koi changes karo, ye commands chalao:

```bash
# 1. Check what changed
git status

# 2. Add all changes
git add .

# 3. Commit with a message describing what you did
git commit -m "Add fee collection module"
# OR
git commit -m "Fix login redirect issue"
# OR
git commit -m "Add institute dashboard stats"

# 4. Push to GitHub
git push origin main
```

---

### Useful Git Commands

```bash
# See all commits history
git log --oneline

# See what changed in files
git diff

# Undo last commit (keep changes)
git reset --soft HEAD~1

# Pull latest from GitHub (agar aur machine pe kaam karo)
git pull origin main

# Create a new branch (jab koi new feature banao)
git checkout -b feature/lms-module

# Switch back to main
git checkout main

# Merge feature branch into main
git merge feature/lms-module
```

---

## 📋 WHAT'S BUILT

### ✅ Owner (Super Admin) Panel
- Login with `admin` / `gt@computer@admin`
- **Dashboard** — Stats, recent institutes, recent transactions
- **Features** — Add/Edit/Enable/Disable features with price
- **Plans** — Create plans, assign features to plans
- **Institutes** — Add institute with plan + addon features
  - Dynamic pricing calculator (plan + addons + discount)
  - Discount by % or flat amount (auto-sync both fields)
  - Auto wallet creation with debit transactions
  - **Welcome email** sent with login credentials
- **Institute Ledger** — Full transaction history
- **Record Payment** — Credit institute wallet when they pay

### ✅ Institute Panel
- Login with credentials received in email
- **Dashboard** — Students, staff, courses, fee stats
- **Students** — Add/Edit/View, ledger, wallet balance
- **Fee Collection** — Collect fee with invoice number, payment mode
- **Courses** — Add/manage courses, enroll students
- **Staff** — Add/manage staff members
- **Attendance** — Mark student and staff attendance

---

## 🔜 WHAT'S NEXT (Future Modules)

Add these one by one as we discussed:

- [ ] LMS (Videos, Documents, Live Classes)
- [ ] Online Exam System
- [ ] Test Series
- [ ] Certificate Generator
- [ ] Enquiry Management
- [ ] Coupon Codes
- [ ] Reports & PDF Export
- [ ] WhatsApp/SMS Notifications

---

## ⚠️ IMPORTANT NOTES

1. **Never commit `.env` file** — it's in `.gitignore` already
2. **Change default password** after first login
3. **MySQL strict mode** — make sure `SET GLOBAL sql_mode=''` if you get date errors
4. **PHP extensions needed:** `php-mbstring`, `php-xml`, `php-curl`, `php-mysql`, `php-zip`
5. **Session driver** is `file` by default — works out of the box

---

## 🐛 Common Issues & Fixes

### "Class not found" error
```bash
composer dump-autoload
```

### Migration fails
```bash
# Check MySQL version and strict mode
# In .env set: DB_STRICT=false
```

### Permission errors (Linux/Mac)
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Page 404 on Apache
- Make sure `mod_rewrite` is enabled
- `.htaccess` is in `public/` folder ✅

### Mail not sending
```bash
# Test in .env: MAIL_MAILER=log
# Check logs: storage/logs/laravel.log
```

---

## 📞 Contact

Built for GT Institute Management System.
