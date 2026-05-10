# Feature Specification: Admin Invitation & Agent Referral Management System

## Overview

Implement an invitation/referral management system for the Laravel project.

There are two different user types in the system:

1. Admin user
   - Stored in Laravel default `users` table
   - Used for admin dashboard / web admin

2. Client user / agent user
   - Stored in `client_users` table
   - Used by normal players / trading users

Important:

- Admin users and client users are different user types.
- Do NOT mix `users` and `client_users`.
- Admin referral codes belong to admin users.
- Client referral codes belong only to client users who were invited by an admin code.

---

## Business Goal

Each admin account has one unique invitation code.

Admin can share an invitation link with users.

When a user registers using an admin invitation code:

- the user becomes linked to that admin
- the user gets their own referral link/code
- the user's referral code is derived from the admin code

The admin dashboard must show statistics for invited users and agents.

---

# 1. Invitation Code Rules

## Admin Invitation Code

Each admin user has one invitation code.

Format:

```text
Axxxx
```

Where:

- `A` is one random uppercase letter
- `xxxx` is a random numeric string
- Example: `K4821`, `M9021`

Rules:

- Must be unique globally
- Must be generated automatically for admin users
- Must be attached to the admin user account
- Must not be duplicated

---

## Client / Agent Invitation Code

Normal users do not have an invitation code by default.

Only users who register using an admin invitation code will receive their own invitation code.

Client / agent invitation code format:

```text
Axxxx-Bxxxx
```

Where:

- `Axxxx` is the admin invitation code used during registration
- `Bxxxx` follows the same rule as admin code:
  - `B` is one random uppercase letter
  - `xxxx` is a random numeric string

Example:

```text
K4821-P1938
```

Rules:

- Must be unique globally
- Must be linked to the client user
- Must preserve the admin code prefix
- Must only be generated after successful registration with a valid admin invitation code

---

# 2. Invitation Link Rules

## Admin Invitation Link

Admin dashboard must show the admin invitation link.

Example:

```text
https://your-domain.com/register?ref=K4821
```

The page must include a button:

```text
Copy invitation link
```

When clicked:

- copy the link to clipboard
- show success message

---

## Client / Agent Invitation Link

If a client user was invited by an admin code, their profile must show their own invitation link.

Example:

```text
https://your-domain.com/register?ref=K4821-P1938
```

When clicked:

- copy the link to clipboard
- show success message

---

## Registration Behavior

Invitation link must open the registration page.

If URL contains:

```text
/register?ref=K4821
```

or:

```text
/register?ref=K4821-P1938
```

Then registration page must automatically fill the referral code input.

The referral code input should still be visible unless the current project requires it hidden.

---

# 3. Referral Relationship Rules

## Admin invited user

If user registers with admin code:

```text
K4821
```

Then:

- user belongs to that admin
- user receives an agent code like `K4821-P1938`

---

## Agent invited user

If another user registers with agent code:

```text
K4821-P1938
```

Then:

- new user belongs to the original admin code `K4821`
- new user is also linked to the agent/client user who owns `K4821-P1938`

This allows admin to see:

- all users under the admin code
- users invited by each agent

---

# 4. Database Requirements

Claude must inspect existing tables before coding.

Required relationships:

## Admin users table

Existing table:

```text
users
```

Need to store admin invitation code.

Suggested field:

```text
invite_code
```

Migration example:

```php
$table->string('invite_code')->unique()->nullable();
```

---

## Client users table

Existing table:

```text
client_users
```

Need to store referral relationship.

Suggested fields:

```text
admin_invite_code
agent_invite_code
own_invite_code
invited_by_admin_id
invited_by_client_user_id
```

Field meanings:

- `admin_invite_code`: root admin code used in referral chain
- `agent_invite_code`: agent code used during registration, nullable
- `own_invite_code`: generated code for this client if eligible
- `invited_by_admin_id`: admin user id
- `invited_by_client_user_id`: parent client user id if registered by agent code

---

## Alternative Design

If the project prefers normalized structure, create a referral table:

```text
referral_links
```

Suggested fields:

- id
- code
- owner_type: admin/client
- owner_id
- root_admin_id nullable
- parent_client_user_id nullable
- status: active/inactive
- created_at
- updated_at

Claude should recommend the safest approach based on current project structure.

---

# 5. Admin Member Management Page

## Goal

Admin dashboard must include a member management page.

This page shows:

1. Admin invitation link
2. Copy invitation link button
3. Summary statistics for all users invited by this admin
4. Datatable of invited agent/users

---

## Admin Summary Statistics

At the top of the page show:

- Total played amount of all invited users
- Total won amount of all invited users
- Total trading fees of all invited users

Definitions:

### Total played amount

Sum of trade amounts for all users under this admin referral tree.

Suggested source:

```text
trades.amount
```

### Total won amount

Sum of winning payout / final payout for all users under this admin referral tree.

Suggested source:

```text
trades.payout
```

or:

```text
trades.final_payout
```

Claude must inspect current trades table and choose the correct field.

### Total trading fees

Sum of trading fee for all users under this admin referral tree.

Suggested source:

```text
trades.trading_fee
```

If `trading_fee` does not exist, mention dependency on trading fee feature.

---

## Agent / Member Datatable

Below the summary, show datatable of members invited by the admin code.

Columns:

- user id
- nickname
- email
- phone number
- own invitation code
- total played amount
- total won amount
- total trading fee
- created at
- action: view detail

---

# 6. Agent Detail Page

When admin clicks detail button:

Show detail page for that agent/client user.

Page has two parts.

---

## Part 1: Agent Summary

Display total stats for all users invited by that agent.

Fields:

- Total played amount
- Total won amount
- Total trading fee

These totals should include all users registered using that agent code.

---

## Part 2: Invited User Datatable

Show datatable of users invited by that agent code.

Columns:

- user id
- nickname
- email
- phone number
- total played amount
- total won amount
- total trading fee
- created at

---

# 7. Client Profile Integration

Normal users do not have invitation codes by default.

If a client user was invited by admin code and has an `own_invite_code`, show invitation link in profile page.

Profile should display:

- own invite code
- invite link
- copy invite link button

If user does not have invite code:

- do not show invite link
- or show message: `Invitation link is not available`

---

# 8. Registration Integration

During registration:

1. Read referral code from request input or URL query
2. Validate referral code
3. Determine if code belongs to:
   - admin
   - client/agent
4. Store referral relationship
5. Generate own invite code if eligible
6. Create user

---

## Registration Code Handling

### If code is admin code

Example:

```text
K4821
```

Then:

- find admin owner
- set `invited_by_admin_id`
- set `admin_invite_code`
- generate client `own_invite_code` as `K4821-Bxxxx`

---

### If code is agent code

Example:

```text
K4821-P1938
```

Then:

- find client owner of agent code
- get root admin code `K4821`
- set `invited_by_admin_id`
- set `invited_by_client_user_id`
- set `admin_invite_code`
- set `agent_invite_code`
- optionally generate own invite code depending on business rule

Business decision required:

- Should users invited by agents also receive their own invite code?
- Recommended: yes, if multi-level tree is desired.
- If only one agent level is desired, do not generate invite code for second-level users.

Claude must ask or document this decision before coding.

---

# 9. Statistics Calculation

Stats must be calculated from trades.

Need to support:

- admin root referral tree
- agent direct referral list

Suggested service:

```text
app/Services/Referral/ReferralStatsService.php
```

Responsibilities:

- get users under admin
- get users under agent
- calculate total played amount
- calculate total won amount
- calculate total trading fees
- prepare datatable data

---

## Important

Do not calculate stats in Blade.

Use controller/service layer.

---

# 10. Suggested Laravel Structure

## Controllers

```text
app/Http/Controllers/Admin/AdminReferralController.php
app/Http/Controllers/ProfileReferralController.php
```

---

## Services

```text
app/Services/Referral/InviteCodeService.php
app/Services/Referral/ReferralRegistrationService.php
app/Services/Referral/ReferralStatsService.php
```

---

## Models / Relationships

Update:

```text
App\Models\User
App\Models\ClientUser
```

Possible relationships:

```php
User has many invited client users
ClientUser belongs to invited admin
ClientUser belongs to parent agent/client user
ClientUser has many invited users
```

---

## Views

Admin:

```text
resources/views/pages/admin/referrals/index.blade.php
resources/views/pages/admin/referrals/show.blade.php
```

Client profile:

```text
resources/views/profile.blade.php
```

Registration:

```text
resources/views/auth/register.blade.php
```

or current client registration Blade.

---

## JavaScript

Suggested file:

```text
resources/js/referral.js
```

Responsibilities:

- copy admin invite link
- copy client invite link
- auto-fill referral code from URL if needed
- show toast/success message

---

# 11. Routes

Suggested admin routes:

```php
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/referrals', [AdminReferralController::class, 'index'])->name('referrals.index');
    Route::get('/referrals/{clientUser}', [AdminReferralController::class, 'show'])->name('referrals.show');
});
```

Suggested client profile route integration:

```php
Route::middleware(['auth:client'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('client.profile');
});
```

Registration route should accept:

```text
referral_code
```

or query:

```text
ref
```

---

# 12. Validation Rules

Referral code validation:

- nullable during registration
- string
- max length 50
- must exist if provided
- must match allowed format:
  - admin: `^[A-Z][0-9]{4}$`
  - agent: `^[A-Z][0-9]{4}-[A-Z][0-9]{4}$`

---

# 13. Security Rules

- Invitation codes must be unique.
- Admin users can only see their own referral tree unless super admin permission exists.
- Do not expose referral stats to unrelated admins.
- Do not trust frontend referral code.
- Validate referral code server-side.
- Prevent code collision by retrying generation.
- Do not allow users to manually change their referral owner after registration.

---

# 14. Required Clarifications Before Coding

Claude must confirm these before implementation:

1. Should users invited by agent codes also get their own invite code?
2. Should admin see only their own referral tree or all referral trees?
3. Should referral stats include only completed trades?
4. Which payout field should be used for total won:
   - `payout`
   - `final_payout`
   - other field
5. Does `trades.trading_fee` already exist?

If not confirmed, Claude should provide safe defaults and clearly mention them.

---

# 15. Expected Output From Claude

Claude must work step by step.

Do NOT generate all code at once.

First output:

1. Architecture plan
2. Database changes needed
3. File structure
4. Existing table assumptions
5. Questions / risks

After confirmation, generate implementation in this order:

1. Migrations
2. Model relationships
3. InviteCodeService
4. ReferralRegistrationService
5. ReferralStatsService
6. Registration update
7. Admin referral controller
8. Client profile update
9. Blade views
10. JavaScript copy-link logic
11. Lang messages
12. Manual test checklist

---

# Important Instruction

This feature must integrate into the existing Laravel project.

Do not create a new authentication system.

Do not mix admin users and client users.

Do not hardcode invitation links.

Use current app URL from config:

```php
config('app.url')
```

or Laravel URL helpers.
