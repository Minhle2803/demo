# Feature Specification: Client User Profile Page Update

## Overview

Implement and update the client user profile page for a Laravel project.

Main Blade view:

```text
resources/views/profile.blade.php
```

This page must support multiple profile-related tabs using the existing page structure.

---

## Important Existing Layout Requirement

The page already has a card layout.

### `div.card-header`

This area must be used as the main tab menu.

Tabs:

1. Profile
2. Change Password
3. Deposit / Withdraw
4. KYC Verification

---

### `div.card-body`

This area must be used as the content area for each tab.

Do not create a completely unrelated layout.

Reuse the current page structure and update it cleanly.

---

## General Requirements

- Use Laravel Blade
- Use Laravel validation
- Use existing authenticated client user
- Use client guard if applicable
- Do not hardcode user information
- Load user data from database
- Keep UI consistent with current template
- Use dark mode friendly UI if the existing profile page is dark
- Use multilingual messages if the project already uses Laravel lang files
- Use clean service/controller structure where needed

---

# 1. Profile Tab

## Goal

Display user profile information from the database.

The profile tab must show real user data instead of static placeholder form values.

---

## Fields to Display

Show user data from database, including:

- user_id
- email
- nickname
- phone_number
- balance
- bank_name
- bank_account
- bank_number
- kyc_front_url
- kyc_back_url
- is_verified
- created_at
- verified_at

Adjust field names based on the actual database schema if needed.

---

## Editable Fields

Only normal profile fields such as:

- nickname

can be editable.

---

## Read-only Fields

These fields must be displayed only and must NOT be editable:

- phone_number
- bank_name
- bank_account
- bank_number
- email if email is already verified
- balance
- user_id
- KYC-related fields

Use disabled inputs, readonly inputs, or display-only UI blocks.

---

## Profile Update Rules

When updating profile:

- Validate nickname
- Nickname must be unique except current user
- Do not allow updating phone number, bank info, balance, or KYC info from this tab
- Return success/error message back to the same page

---

## Required Backend

Create or update:

```text
ProfileController
```

Suggested methods:

```php
show()
updateProfile()
```

---

# 2. Change Password Tab

## Goal

Allow user to change password after verifying current password.

Remove or ignore any login history section.

---

## Fields

- current_password
- new_password
- new_password_confirmation

---

## Validation Rules

- current_password is required
- current_password must match current user password
- new_password is required
- new_password minimum 8 characters
- new_password must be confirmed
- new_password should follow existing password policy if available

---

## Behavior

On submit:

1. Verify current password
2. If wrong, return error to current page/tab
3. If correct, update password
4. Show success message

---

## Required Backend

Suggested method:

```php
updatePassword()
```

Use:

```php
Hash::check()
Hash::make()
```

---

# 3. Deposit / Withdraw Tab

## Goal

Implement deposit and withdraw UI inside the Deposit / Withdraw tab.

This tab has 2 sub-tabs:

```text
sub-tab 1: Nạp tiền (Deposit)
sub-tab 2: Rút tiền (Withdraw)
```

---

## Current Balance Display

Display the current user balance from database:

```text
balance
```

Do not use static content.

Example display:

```text
Current Balance: 1,000.00 VND
```

---

## 3A. Deposit Sub-Tab

### Deposit Input

User can enter the amount they want to deposit.

Validation:

- amount is required
- amount must be numeric
- amount must be greater than 0
- minimum: 10,000 VND

If the user submits without amount, show validation error on UI.

---

### QR Deposit Requirement

When the deposit form is submitted successfully:

Generate a deposit QR code.

The QR content must include:

1. Deposit amount
2. Payment note / description
3. Username / login name as note

Payment note should use the user's login name or nickname.

Use the VietQR image service (`img.vietqr.io`) or a similar QR generation approach.

---

### Deposit Submit Behavior

Recommended flow:

1. User enters amount
2. Frontend validates empty input
3. Backend validates amount again
4. Return data needed to generate QR
5. JS renders QR code on the page

---

### Deposit History

The deposit sub-tab must include a deposit history section below the QR area.

Show a table with user's deposit requests:

| Column | Description |
|--------|-------------|
| Date | Created date |
| Amount | Deposit amount |
| Status | pending / processing / done |
| Note | Admin note (if any) |

Status definitions:
- `pending`: User created deposit, waiting for admin confirmation
- `processing`: Admin is reviewing
- `done`: Admin confirmed deposit, balance was credited

Deposit history data comes from a database table.

---

### Deposit Database Table

Create table `deposit_requests`:

| Column | Type | Notes |
|--------|------|-------|
| id | bigint | primary key |
| user_id | bigint | client user id |
| amount | decimal(18,2) | deposit amount |
| status | enum | pending, processing, done |
| admin_note | text nullable | admin note |
| processed_by | bigint nullable | admin user id |
| processed_at | timestamp nullable | when admin processed |
| created_at | timestamp | |
| updated_at | timestamp | |

---

### Admin Deposit Confirm API

Admin confirms deposit via API:

```http
POST /api/admin/spot/deposit/confirm
```

Request:
```json
{
  "user_id": "USR-XXXXXXXX",
  "deposit_id": 1,
  "amount": 100000,
  "status": "done"
}
```

When confirmed with status `done`, user balance increases by amount.

---

## 3B. Withdraw Sub-Tab

### Withdraw Form

User can request a withdrawal:

Fields:
- amount (required, numeric, min: 10,000)
- bank_account (auto-filled from user profile, readonly)
- bank_number (auto-filled from user profile, readonly)
- account_name (auto-filled from user profile, readonly)

Validation:
- amount must be <= user balance
- amount must be > 0
- User must have bank info filled (bank_account, bank_number, account_name not empty)

Flow:
1. User enters amount
2. System validates
3. If valid, create withdraw request with status `processing`
4. Send request to admin for review

---

### Admin Withdraw Processing

Admin can process withdraw requests with these actions:

1. **Success** (`done`): Admin approves, balance is deducted
2. **Pending** (`pending`): Admin marks as pending (waiting for more info)
3. **Reject** (`rejected`): Admin rejects with reason in `admin_note`

Admin API:

```http
POST /api/admin/spot/withdraw/process
```

Request:
```json
{
  "withdraw_id": 1,
  "status": "done",
  "admin_note": "Đã chuyển khoản"
}
```

When status `done`: 
- User balance is deducted by amount
- Status updated to `done`

When status `rejected`:
- Balance is NOT deducted
- Status updated to `rejected`
- `admin_note` must contain reason

---

### Withdraw History

The withdraw sub-tab must include a withdraw history section.

Show a table with user's withdrawal requests:

| Column | Description |
|--------|-------------|
| Date | Created date |
| Amount | Withdraw amount |
| Status | processing / pending / done / rejected |
| Admin Note | Reason from admin (if rejected) |

---

### Withdraw Database Table

Create table `withdraw_requests`:

| Column | Type | Notes |
|--------|------|-------|
| id | bigint | primary key |
| user_id | bigint | client user id |
| amount | decimal(18,2) | withdraw amount |
| status | enum | processing, pending, done, rejected |
| admin_note | text nullable | admin note/reason |
| processed_by | bigint nullable | admin user id |
| processed_at | timestamp nullable | when admin processed |
| created_at | timestamp | |
| updated_at | timestamp | |

---

## JavaScript Requirement

Deposit/withdraw JS must be placed in:

```text
resources/js/profile.js
```

Do not write all JavaScript inline inside Blade.

Use Laravel Vite to load:

```blade
@vite(['resources/js/profile.js'])
```

---

# 4. KYC Verification Tab

## Goals

Implement KYC verification UI and logic.

This tab has 2 main requirements:

---

## Requirement 1: Direct Link to KYC Tab

There must be a direct link that opens the profile page with the KYC Verification tab active.

Use query string or hash.

Recommended URL:

```text
/profile?tab=kyc
```

or:

```text
/profile#kyc
```

When users try to trade but are not KYC verified, frontend/backend should redirect them to this tab.

Example redirect:

```text
/profile?tab=kyc
```

The JavaScript must detect this and activate the KYC tab automatically.

---

## Requirement 2: KYC Modal with Steps

KYC verification uses a modal dialog (reuse the existing Bootstrap modal structure from the old profile page).

The modal has 2 steps:

### Step 1: Bank Account Information

User fills in bank account details:

- Account Holder Name (account_name)
- Bank Name (bank_account)
- Bank Number (bank_number)

These fields are saved to the `client_users` table.

Validation:
- All 3 fields are required in step 1
- Bank number must be numeric

"Next Step" button advances to step 2.

### Step 2: Upload and Verify KYC

User uploads:

1. CCCD front image (kyc_front)
2. CCCD back image (kyc_back)

Use file inputs with preview.

Store uploaded files and save paths to database fields:

- kyc_front_url
- kyc_back_url

On submit:
- Files are uploaded to server
- Server scans QR code from front image
- Extracts CCCD data
- Compares with user account data
- If match → KYC verified
- If mismatch → show error on UI

---

## Vietnam CCCD Verification Requirement

KYC verification must follow Vietnamese ID card / CCCD logic.

The system must:

1. Scan/read QR code from the front side of CCCD
2. Extract data from QR code
3. Compare extracted QR data with the user's submitted/account data
4. If data does not match, return error to UI

---

## CCCD QR Data Validation

The QR on Vietnamese CCCD usually contains personal information.

Claude must implement a service layer for this logic.

Suggested service:

```text
app/Services/ProfileService.php (submitKyc method)
```

The service should:

- decode QR from uploaded front image
- parse CCCD QR content
- extract relevant fields
- compare with user database fields

---

## Data Matching Rules

Compare QR data with user data.

Possible matching fields:

- full name if available
- date of birth if available
- CCCD number if stored
- phone_number only if applicable
- nickname should NOT be used as legal identity unless the system intentionally uses it

If the current database does not store full legal name or CCCD number, Claude must:

1. List missing fields
2. Suggest migration fields
3. Do not fake verification

Required additional fields:

```text
full_name
date_of_birth
cccd_number
kyc_verified_at
```

---

## KYC Failure Behavior

If QR cannot be scanned:

- show error on UI

If QR data does not match user data:

- show error on UI

If image is invalid:

- show error on UI

If upload is missing:

- show error on UI

---

## KYC Success Behavior

When KYC verification succeeds:

- save front image path
- save back image path
- update KYC status (kyc_verified_at)
- show success message in modal
- close modal and update KYC tab to show verified state

---

## KYC Verified UI (outside modal)

If KYC is already verified:

Do NOT show the upload form.

Instead show verification information:

- Verified status
- Verified date
- User legal information if stored
- Preview links/images of CCCD front/back if allowed
- Clear success state

---

## Important Security Notes

- Do not trust frontend validation
- Validate file type and size on backend
- Only allow image files
- Store uploads safely
- Do not expose private KYC files publicly if project requires privacy
- Consider using private storage disk
- Do not overwrite existing KYC unless allowed

---

# Route Requirements

Suggested web routes:

```php
Route::middleware(['auth:client'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('client.profile');

    Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('client.profile.update');

    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('client.profile.password');

    Route::post('/profile/deposit/qr', [ProfileController::class, 'generateDepositQr'])->name('client.profile.deposit.qr');

    Route::get('/profile/deposit/history', [ProfileController::class, 'depositHistory'])->name('client.profile.deposit.history');

    Route::post('/profile/withdraw', [ProfileController::class, 'submitWithdraw'])->name('client.profile.withdraw');

    Route::get('/profile/withdraw/history', [ProfileController::class, 'withdrawHistory'])->name('client.profile.withdraw.history');

    Route::post('/profile/kyc', [ProfileController::class, 'submitKyc'])->name('client.profile.kyc');
});
```

Admin API routes:

```php
Route::prefix('admin/spot')->middleware(['auth:sanctum'])->group(function () {
    Route::post('deposit/confirm', [SpotDepositController::class, 'confirm'])->name('admin.spot.deposit.confirm');
    Route::post('withdraw/process', [SpotWithdrawController::class, 'process'])->name('admin.spot.withdraw.process');
});
```

---

# Frontend JavaScript Requirements

Create or update:

```text
resources/js/profile.js
```

Responsibilities:

1. Activate tab from URL query/hash
2. Handle deposit amount validation + QR generation
3. Handle withdraw form submission
4. Load deposit history (AJAX)
5. Load withdraw history (AJAX)
6. KYC modal: step 1 (bank info) → step 2 (upload CCCD)
7. KYC file upload preview
8. Keep JS modular and clean

---

## Tab Activation Logic

If URL contains:

```text
/profile?tab=kyc
```

Then activate the KYC tab automatically.

If URL contains:

```text
/profile?tab=password
```

Then activate change password tab.

If no tab query exists, default to Profile tab.

---

# Blade Requirements

In `profile.blade.php`:

- Use `div.card-header` for tab menu
- Use `div.card-body` for tab content
- Do not keep static placeholder data
- Fill all fields from authenticated client user
- Show `session('success')`
- Show `session('error')`
- Show validation errors

## KYC Modal Structure

Reuse the existing Bootstrap modal structure. The modal has:

```
Step 1 (Bank Info):
  - Account Holder Name input
  - Bank Number input
  - Bank Name input
  - "Next Step" button

Step 2 (KYC Upload):
  - CCCD front file input with preview
  - CCCD back file input with preview
  - "Submit" button
  - "Back" button to return to Step 1
```

## Deposit / Withdraw Tab Structure

```
Sub-tab: "Nạp tiền"
  - Balance display
  - Amount input
  - "Tạo mã QR" button
  - QR code display area
  - Deposit History table (loaded via AJAX)

Sub-tab: "Rút tiền"
  - Bank info display (readonly, from user profile)
  - Amount input
  - "Rút tiền" button
  - Withdraw History table (loaded via AJAX)
```

---

# Response Behavior

This is a web Blade feature.

On validation error:

- redirect back to same page
- keep input where appropriate
- use `session('error')`
- use Laravel validation errors

On success:

- redirect back to same tab if possible
- use `session('success')`

---

# Error Codes / Messages

If the project uses centralized error codes and lang messages, add messages for:

```text
PROFILE_UPDATED
PASSWORD_UPDATED
CURRENT_PASSWORD_INVALID
DEPOSIT_AMOUNT_REQUIRED
DEPOSIT_QR_GENERATED
DEPOSIT_CONFIRMED
WITHDRAW_REQUESTED
WITHDRAW_PROCESSED
WITHDRAW_INSUFFICIENT_BALANCE
WITHDRAW_BANK_INFO_MISSING
KYC_UPLOAD_REQUIRED
KYC_QR_SCAN_FAILED
KYC_DATA_MISMATCH
KYC_VERIFIED_SUCCESS
KYC_ALREADY_VERIFIED
```

---

# Expected Output From Claude

Claude must generate step by step.

Do NOT generate everything at once.

First provide:

1. Short architecture plan
2. File structure
3. Required database fields checklist
4. Missing fields warning if any

Then generate implementation in this order:

1. Migrations (deposit_requests, withdraw_requests)
2. Models (DepositRequest, WithdrawRequest)
3. Routes (web + api)
4. Controller methods (ProfileController: deposit, withdraw, histories)
5. Admin controllers (SpotDepositController, SpotWithdrawController)
6. Request validation classes
7. ProfileService updates
8. Blade update (deposit/withdraw sub-tabs, KYC modal steps)
9. `resources/js/profile.js`
10. Error codes + lang messages

---

# Important Instruction

This feature must integrate with the existing Laravel project.

Do not create a new authentication system.

Do not create a new user model unless necessary.

Use the existing authenticated client user and existing database fields.

If required database fields are missing, explain before writing code.
