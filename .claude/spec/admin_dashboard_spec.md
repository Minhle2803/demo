# Feature Specification: Admin Dashboard & Management System

## Overview

Implement a new admin dashboard system for the Laravel project.

This feature is for **admin users**, using Laravel's default `users` table / Laravel admin user system.

This is separate from the client user system.

---

## Admin Authentication

### Requirement

Implement admin login using Laravel default user model/table.

Admin account:

```text
email or username: admin
password: trade@#!123
```

If the current admin login uses email, create default admin with:

```text
email: admin@tradex.local
password: trade@#!123
```

Claude must check the current Laravel auth structure before implementation.

---

## Important Rules

- Do NOT use client guard for admin.
- Do NOT mix admin users with client users.
- Admin uses Laravel `users` table.
- Client users use existing client user system.
- Admin pages must be protected by admin auth middleware.
- Use Blade templates provided by the project.
- Do not create a completely new UI if existing templates exist.

---

# Template html :

Template html ussing template in folder : resources/views/pages/dashboard/html_template/*

---

# 1. Admin Dashboard Page

## Template

Use existing template:

```text
resources/views/dashboard/dashboard.blade.php
```

---

## Dashboard Stats Section

Inside:

```text
div.order-first
```

There are existing `.card` elements.

Replace/update cards to display these statistics:

1. Total Spot Buy Orders
2. Total Spot Sell Orders
3. Total Trading Sell Orders from `trades` table
4. Total Trading Buy Orders from `trades` table
5. Revenue

---

## Revenue Formula

Revenue is calculated from the session trading `trades` table:

```text
Revenue = SUM(amount where status = lose) - SUM(amount where status = win)
```

Notes:

- `lose` means user lost, platform keeps amount
- `win` means user won, platform pays out
- Confirm column names from current `trades` table
- If payout is stored separately, Claude should mention whether formula should use `amount` or `payout`

---

## Market Graph

The dashboard must include a Market Graph section.

Requirement:

- The graph should use the same chart logic and display style as the existing client trading page.
- Reuse existing chart/candle APIs if available.
- Do not duplicate chart generation logic.
- If using KLineCharts on the frontend, reuse existing JS modules or create a dashboard-specific wrapper.

Suggested:

```text
resources/js/admin/dashboard-chart.js
```

---

## Recent Activity Section

Existing `Recent Activity` section should be replaced.

New content:

- List of recent Spot Buy Orders
- Filter by crypto type / symbol

Fields to show:

- User nickname
- Symbol
- Price
- Quantity
- Total
- Order status
- Created time

---

## Top Performers Section

Existing `Top Performers` section should be replaced.

New content:

- List of recent Spot Sell Orders
- Filter by crypto type / symbol

Fields to show:

- User nickname
- Symbol
- Price
- Quantity
- Total
- Order status
- Created time

---

# 2. User Management Page

## Template

Use existing template:

```text
resources/views/dashboard/template_manager.blade.php
```

---

## Goal

Create admin page to manage client users.

Features:

- List client users
- View user details
- View KYC verification status
- Search/filter by:
  - nickname
  - email
  - phone number
  - KYC status

---

## Data to show

- user_id
- nickname
- email
- phone_number
- balance
- bank_name
- bank_account
- bank_number
- KYC status
- created_at
- verified_at

---

## KYC Status Logic

User is considered KYC verified if:

```text
kyc_front_url is not empty
kyc_back_url is not empty
```

If project uses additional fields such as `kyc_verified_at`, use them as well.

---

# 3. Deposit Management Page

## Template

Use existing template:

```text
resources/views/dashboard/template_manager.blade.php
```

---

## Goal

Admin can manage deposit requests.

---

## Data to show

For each deposit request:

- User information
- Nickname
- KYC verification status
- Bank name
- Deposit amount
- Deposit note if available
- Request status
- Created time

---

## Actions

### Confirm Deposit

Button:

```text
Confirm Deposit
```

When admin confirms:

- Add deposit amount to user's `balance`
- Mark deposit request as approved/confirmed
- Save admin ID if available
- Save confirmed timestamp
- Record transaction/ledger if wallet ledger exists

Use DB transaction.

---

### Reject Deposit

Button:

```text
Reject
```

Admin must provide rejection reason.

When rejected:

- Mark deposit request as rejected
- Save reject reason
- Do not modify user balance

---

## Important

If deposit request table does not exist yet, Claude must propose migration.

Suggested table:

```text
deposit_requests
```

Suggested fields:

- id
- user_id
- amount
- bank_name
- bank_account
- bank_number
- note
- status: pending, approved, rejected
- reject_reason nullable
- approved_by nullable
- approved_at nullable
- rejected_at nullable
- created_at
- updated_at

---

# 4. Withdraw Management Page

## Template

Use existing template:

```text
resources/views/dashboard/template_manager.blade.php
```

---

## Goal

Admin can manage withdraw requests.

---

## Data to show

For each withdraw request:

- User information
- Nickname
- KYC verification status
- Bank name
- Withdraw amount
- Request status
- Created time

---

## Actions

### Confirm Withdraw

Button:

```text
Confirm Withdraw
```

When admin confirms:

- Deduct withdraw amount from user's `balance`
- Mark withdraw request as approved/confirmed
- Save admin ID if available
- Save confirmed timestamp
- Record transaction/ledger if wallet ledger exists

Use DB transaction.

Important:

- Do not allow balance to become negative
- Validate sufficient balance before confirming withdraw

---

### Reject Withdraw

Button:

```text
Reject
```

Admin must provide rejection reason.

When rejected:

- Mark withdraw request as rejected
- Save reject reason
- Do not modify user balance

---

## Important

If withdraw request table does not exist yet, Claude must propose migration.

Suggested table:

```text
withdraw_requests
```

Suggested fields:

- id
- user_id
- amount
- bank_name
- bank_account
- bank_number
- status: pending, approved, rejected
- reject_reason nullable
- approved_by nullable
- approved_at nullable
- rejected_at nullable
- created_at
- updated_at

---

# 5. Settings Page

## Goal

Create admin settings page for project configuration.

Settings include:

1. Bank information for deposit QR code
2. Project logo
3. Crypto asset management

---

## Bank Settings

Admin can set:

- Bank name
- Bank account
- Bank number

These values must be used when generating deposit QR codes on the client profile page.

Suggested keys:

```text
deposit_bank_name
deposit_bank_account
deposit_bank_number
```

---

## Logo Setting

Admin can upload/update project logo.

After setting logo:

- Apply logo globally across the project
- Header/logo areas should use this configured logo
- Provide fallback default logo if no setting exists

Suggested key:

```text
project_logo
```

---

## Crypto Management

Admin can add, update, or remove crypto assets used in the system.

Fields:

- symbol
- name
- icon
- base_asset
- quote_asset
- price / exchange rate
- status: active/inactive
- precision settings if needed

When new crypto is added:

- It must apply globally
- It must be available in market list
- It must be available in trading page
- It must not remain hardcoded in config only

---

## Suggested Table: crypto_assets

If not existing, create:

```text
crypto_assets
```

Fields:

- id
- symbol
- name
- icon_url nullable
- base_asset
- quote_asset
- price decimal nullable
- price_precision
- quantity_precision
- min_quantity
- min_notional
- status active/inactive
- created_at
- updated_at

---

## Suggested Table: project_settings

If not existing, create:

```text
project_settings
```

Fields:

- id
- key unique
- value nullable text
- type string nullable
- created_at
- updated_at

---

# 6. Routes

Suggested admin routes:

```php
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [AdminUserController::class, 'show'])->name('users.show');

    Route::get('/deposits', [AdminDepositController::class, 'index'])->name('deposits.index');
    Route::post('/deposits/{id}/approve', [AdminDepositController::class, 'approve'])->name('deposits.approve');
    Route::post('/deposits/{id}/reject', [AdminDepositController::class, 'reject'])->name('deposits.reject');

    Route::get('/withdraws', [AdminWithdrawController::class, 'index'])->name('withdraws.index');
    Route::post('/withdraws/{id}/approve', [AdminWithdrawController::class, 'approve'])->name('withdraws.approve');
    Route::post('/withdraws/{id}/reject', [AdminWithdrawController::class, 'reject'])->name('withdraws.reject');

    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/bank', [AdminSettingController::class, 'updateBank'])->name('settings.bank');
    Route::post('/settings/logo', [AdminSettingController::class, 'updateLogo'])->name('settings.logo');

    Route::get('/settings/crypto-assets', [AdminCryptoAssetController::class, 'index'])->name('crypto-assets.index');
    Route::post('/settings/crypto-assets', [AdminCryptoAssetController::class, 'store'])->name('crypto-assets.store');
    Route::put('/settings/crypto-assets/{id}', [AdminCryptoAssetController::class, 'update'])->name('crypto-assets.update');
    Route::delete('/settings/crypto-assets/{id}', [AdminCryptoAssetController::class, 'destroy'])->name('crypto-assets.destroy');
});
```

Adjust route style based on existing project.

---

# 7. Controllers

Suggested controllers:

```text
app/Http/Controllers/Admin/AdminDashboardController.php
app/Http/Controllers/Admin/AdminUserController.php
app/Http/Controllers/Admin/AdminDepositController.php
app/Http/Controllers/Admin/AdminWithdrawController.php
app/Http/Controllers/Admin/AdminSettingController.php
app/Http/Controllers/Admin/AdminCryptoAssetController.php
```

---

# 8. Services

Suggested services:

```text
app/Services/Admin/AdminDashboardService.php
app/Services/Admin/AdminDepositService.php
app/Services/Admin/AdminWithdrawService.php
app/Services/Admin/AdminSettingService.php
app/Services/Admin/CryptoAssetService.php
```

Use services for:

- stats calculation
- deposit approve/reject
- withdraw approve/reject
- setting update
- crypto asset management

---

# 9. Request Validation

Suggested FormRequests:

```text
app/Http/Requests/Admin/ApproveDepositRequest.php
app/Http/Requests/Admin/RejectDepositRequest.php
app/Http/Requests/Admin/ApproveWithdrawRequest.php
app/Http/Requests/Admin/RejectWithdrawRequest.php
app/Http/Requests/Admin/UpdateBankSettingRequest.php
app/Http/Requests/Admin/UpdateLogoRequest.php
app/Http/Requests/Admin/StoreCryptoAssetRequest.php
app/Http/Requests/Admin/UpdateCryptoAssetRequest.php
```

---

# 10. Dashboard Data Requirements

AdminDashboardService should provide:

```php
[
    'total_spot_buy_orders' => ...,
    'total_spot_sell_orders' => ...,
    'total_trading_sell_orders' => ...,
    'total_trading_buy_orders' => ...,
    'revenue' => ...,
    'recent_spot_buy_orders' => ...,
    'recent_spot_sell_orders' => ...,
    'symbols' => ...,
]
```

---

# 11. Frontend JavaScript Requirements

Create admin dashboard JS if needed:

```text
resources/js/admin/dashboard.js
resources/js/admin/dashboard-chart.js
resources/js/admin/manager.js
```

Use Vite:

```blade
@vite(['resources/js/admin/dashboard.js'])
```

Dashboard chart should reuse existing trading chart logic where possible.

---

# 12. UI Requirements

- Use existing dashboard templates
- Keep dark style consistent
- Do not create unrelated UI
- Use tables for user/deposit/withdraw management
- Add action buttons with confirm dialogs
- Show success/error session messages
- Support filters where required

---

# 13. Security Rules

- Admin routes must require admin authentication
- Do not expose admin pages to client users
- Use DB transactions for deposit/withdraw approval
- Prevent duplicate approval/rejection
- Prevent negative user balance
- Save admin action audit data if possible
- Validate all inputs server-side

---

# 14. Expected Output From Claude

Claude must work step by step.

Do NOT generate all code at once.

First output:

1. Architecture plan
2. File structure
3. Tables/migrations needed
4. Existing table assumptions
5. Potential conflicts with current project

After confirmation, generate implementation in this order:

1. Admin auth setup / seeder for default admin
2. Routes
3. Migrations if needed
4. Models
5. Services
6. Controllers
7. Request validation
8. Blade updates
9. JS files
10. Lang messages

---

# 15. Important Instruction

This feature must integrate into the existing Laravel project.

Do not create a new project.

Do not rewrite existing client features.

Do not break existing trading, profile, KYC, or wallet logic.

If a required table or field is missing, Claude must report it before generating code.
