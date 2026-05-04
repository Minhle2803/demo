# Client User Authentication Feature Specification for Existing Laravel Project

## 1. Objective

Implement a fully isolated client user authentication system in the current Laravel project.

IMPORTANT:
- This client user system must be completely separate from the existing admin authentication system.
- Do NOT reuse the default Laravel `users` table if it is already used for admin.
- Use a separate table, model, guard, password broker, and business logic for client users.
- The implementation must be scalable for future trading-related features.

---

## 2. Scope

This feature must include:

1. Client user registration
2. Client user login
3. Client user logout
4. Forgot password
5. Reset password
6. Email verification
7. Phone verification via OTP
8. Referral code handling
9. Bank information fields
10. KYC image fields
11. Wallet balance fields
12. Unified API response structure
13. Centralized error code definition
14. Multi-language error message strategy (Vietnamese and English)

---

## 3. Main Rule About Current Project

This feature is for client users only.

It must NOT affect:
- admin authentication
- admin guards
- admin middleware
- admin database tables
- existing admin login flow

If the project already has admin auth, keep it unchanged.

---

## 4. Database Design

Create a dedicated table for client users.

Recommended table name:

`client_users`

### Required Columns

| Field | Type | Nullable | Notes |
|------|------|----------|------|
| id | bigint / uuid | no | primary key |
| user_id | string | no | unique business identifier |
| email | string | no | unique |
| nickname | string | no | unique |
| password | string | no | hashed |
| phone_number | string | no | unique |
| is_verified | boolean | no | default false |
| verified_at | timestamp | yes | set when fully verified |
| referral_code | string | yes | code entered by user during registration |
| account_name | string | yes | bank account owner name |
| bank_account | string | yes | bank name |
| bank_number | string | yes | bank account number |
| kyc_front_url | string | yes | front side of national ID |
| kyc_back_url | string | yes | back side of national ID |
| trading_account | string | yes | trading account id |
| balance | decimal(18,2) | no | default 0 |
| trading_balance | decimal(18,2) | no | default 0 |
| email_verification_token | string | yes | optional if custom verification flow is used |
| phone_otp_code | string | yes | optional if stored directly |
| phone_otp_expired_at | timestamp | yes | OTP expiration time |
| created_at | timestamp | no | |
| updated_at | timestamp | no | |

---

## 5. Business Rules

1. `email` must be unique
2. `nickname` must be unique
3. `phone_number` must be unique
4. `password` must always be hashed
5. `is_verified` defaults to `false`
6. `verified_at` is only set when BOTH email and phone are verified
7. `referral_code` is optional
8. `referral_code` means the referral code entered by the registering user, not a self-generated code
9. `balance` defaults to `0`
10. `trading_balance` defaults to `0`
11. `trading_balance` must never exceed `balance`
12. client users must authenticate through a dedicated guard, for example `client`
13. forgot password must use a separate password broker for client users

---

## 6. Registration Feature

### Required Input Fields

- nickname
- email
- phone_number
- password
- password_confirmation

### Optional Input Fields

- referral_code

### Validation Rules

- nickname:
  - required
  - string
  - min 3
  - max 50
  - unique in client_users.nickname

- email:
  - required
  - valid email format
  - max 255
  - unique in client_users.email

- phone_number:
  - required
  - string
  - valid phone format based on project rule
  - unique in client_users.phone_number

- password:
  - required
  - min 8
  - confirmed
  - should support strong password rule if possible

- referral_code:
  - nullable
  - string
  - if provided, must exist according to business logic or referral source rule

### Registration Process

1. Validate request
2. Generate `user_id`
3. Hash password
4. Create client user
5. Set default values:
   - is_verified = false
   - verified_at = null
   - balance = 0
   - trading_balance = 0
6. Trigger email verification flow
7. Trigger phone OTP sending flow
8. Return success response

---

## 7. Email Verification Feature

### Requirements

- Email verification is required
- Can use Laravel built-in email verification or a custom verification token flow
- The implementation must work with the dedicated client user model, not admin user

### Logic

1. User receives verification email
2. User clicks verification link
3. Server validates token or signed URL
4. Mark email as verified internally
5. If phone is also verified:
   - set is_verified = true
   - set verified_at = current timestamp

---

## 8. Phone Verification Feature

### Requirements

- Phone verification is required
- OTP-based verification
- OTP can be mocked for now if no SMS provider is available
- Design the code in a way that a real SMS provider can be plugged in later

### Send OTP Flow

1. Client sends request to send OTP
2. Server generates OTP
3. Save OTP or OTP hash
4. Save expiration time
5. Send OTP or mock OTP
6. Return success response

### Verify OTP Flow

1. Client submits phone_number and otp
2. Server validates OTP
3. If valid, mark phone as verified internally
4. If email is already verified:
   - set is_verified = true
   - set verified_at = current timestamp

### OTP Rules

- OTP must expire
- OTP resend should be rate limited
- Invalid OTP must return structured error response

---

## 9. Login Feature

### Allowed Login Methods

- email + password
- phone_number + password

### Request Fields

- login
- password

`login` can be either email or phone number.

### Login Logic

1. Detect whether `login` is email or phone number
2. Find matching client user
3. Validate password
4. Authenticate with client guard only
5. Return success response

### Notes

- Must not use admin guard
- Must not log into admin session
- Can optionally restrict unverified accounts depending on project policy
- If unverified accounts are allowed to login, they should still be blocked from sensitive features later

---

## 10. Logout Feature

### Logic

- Logout current client user session or token
- Must affect only client auth context

---

## 11. Forgot Password Feature

### Requirements

Implement full forgot password flow for client users only.

### Includes

1. Request reset password
2. Send reset link email
3. Show reset form or accept reset API payload
4. Reset password
5. Hash new password

### Important

- Use separate password broker for client users
- Must not use admin password reset flow

---

## 12. Wallet Fields

The client user table must include:

- balance
- trading_balance

### Rules

- both default to 0
- both stored as decimal(18,2)
- `trading_balance <= balance`

These fields are only for initial auth/account setup and future trading integration.

---

## 13. KYC Fields

The client user table must include:

- kyc_front_url
- kyc_back_url

For now:
- file upload implementation is optional
- field structure must exist
- code should be extensible for future KYC upload flow

---

## 14. Bank Fields

The client user table must include:

- account_name
- bank_account
- bank_number

For now:
- not required during registration
- only need schema, model fillable/casts, and future-ready structure

---

## 15. Recommended Laravel Architecture

### Model

Use a dedicated model, for example:

`App\Models\ClientUser`

### Auth Configuration

Update `config/auth.php` to include:

- a dedicated provider for client users
- a dedicated guard for client users
- a dedicated password broker for client users

### Suggested Files

- `app/Models/ClientUser.php`
- `database/migrations/...create_client_users_table.php`
- `app/Http/Controllers/Auth/ClientRegisterController.php`
- `app/Http/Controllers/Auth/ClientLoginController.php`
- `app/Http/Controllers/Auth/ClientLogoutController.php`
- `app/Http/Controllers/Auth/ClientForgotPasswordController.php`
- `app/Http/Controllers/Auth/ClientResetPasswordController.php`
- `app/Http/Controllers/Auth/ClientEmailVerificationController.php`
- `app/Http/Controllers/Auth/ClientPhoneVerificationController.php`
- `app/Http/Requests/Auth/ClientRegisterRequest.php`
- `app/Http/Requests/Auth/ClientLoginRequest.php`
- `app/Services/OtpService.php`
- `app/Support/ErrorCodes.php` or equivalent centralized error definition file
- `lang/en/errors.php`
- `lang/vi/errors.php`

---

## 16. Suggested Routes

### Auth Routes

- `POST /client/register`
- `POST /client/login`
- `POST /client/logout`

### Verification Routes

- `POST /client/email/send-verification`
- `GET /client/email/verify/{id}/{hash}`
- `POST /client/phone/send-otp`
- `POST /client/phone/verify-otp`

### Password Routes

- `POST /client/forgot-password`
- `POST /client/reset-password`

Route naming and middleware should follow the current project style.

---

## 17. Unified API Response Standard

All endpoints must return a unified response structure.

### Success Response Format

```json
{
  "success": true,
  "status_code": 200,
  "code": "SUCCESS",
  "message": "This field is optional and may be omitted if frontend resolves message by code",
  "data": {}
}```

###Fail Response Format
```json
{
  "success": false,
  "status_code": 422,
  "code": "AUTH_VALIDATION_ERROR",
  "message": "This field is optional and may be omitted if frontend resolves message by code",
  "errors": {
    "email": [
      "The email field is required."
    ]
  }
}```

###Response Rules

-success must be boolean
-status_code must match the actual HTTP status code
-code must be a centralized business error code
-message may be returned as fallback text, but frontend should primarily resolve UI message using code
-errors is optional and used mainly for validation errors

---

## 18. Centralized Error Code Strategy
All business and validation-related error codes must be defined in one centralized place.

Recommended file:

-`app/Support/ErrorCodes.php`
Example structure:

`SUCCESS`
`AUTH_VALIDATION_ERROR`
`AUTH_INVALID_CREDENTIALS`
`AUTH_ACCOUNT_NOT_FOUND`
`AUTH_ACCOUNT_ALREADY_EXISTS`
`AUTH_EMAIL_ALREADY_USED`
`AUTH_PHONE_ALREADY_USED`
`AUTH_NICKNAME_ALREADY_USED`
`AUTH_UNVERIFIED_ACCOUNT`
`AUTH_EMAIL_VERIFICATION_REQUIRED`
`AUTH_PHONE_VERIFICATION_REQUIRED`
`AUTH_INVALID_OTP`
`AUTH_OTP_EXPIRED`
`AUTH_OTP_TOO_MANY_REQUESTS`
`AUTH_INVALID_RESET_TOKEN`
`AUTH_RESET_PASSWORD_FAILED`
`AUTH_REFERRAL_CODE_INVALID`
`AUTH_UNAUTHORIZED`
`AUTH_FORBIDDEN`
`SYSTEM_INTERNAL_ERROR`

Claude must implement all error codes in one place and reuse them consistently.

---

## 19. Multi-language Error Message Strategy

The system must support at least:

- English
- Vietnamese

### Requirement

The backend should return:

- HTTP status code
- business code

The frontend should use:

- current active language
- returned code

to resolve and display the proper localized message.

### Important Rule

Do NOT rely only on raw backend message strings for UI display.

Instead:

1 backend returns:
 - status_code
 - code
2 frontend maps code to localized message based on current locale

### Example

Server response:

```json
{
  "success": false,
  "status_code": 401,
  "code": "AUTH_INVALID_CREDENTIALS"
}
```

Frontend language map:

### English
- AUTH_INVALID_CREDENTIALS => Invalid email/phone number or password.
### Vietnamese
- AUTH_INVALID_CREDENTIALS => Email/số điện thoại hoặc mật khẩu không đúng.

### Backend Translation Files

Also prepare Laravel translation files to keep backend messages organized:

- lang/en/errors.php
- lang/vi/errors.php

Example keys:

- SUCCESS
- AUTH_INVALID_CREDENTIALS
- AUTH_INVALID_OTP
- AUTH_OTP_EXPIRED
- AUTH_VALIDATION_ERROR
- SYSTEM_INTERNAL_ERROR

## Message Handling Recommendation

Preferred priority on frontend:

1. use code
2. map to current locale dictionary
3. if no mapping exists, fallback to backend message
4. if no message exists, fallback to generic unknown error

--- 

## 20. Suggested HTTP Status Code Convention

Use the following status code approach consistently:

1. 200 OK
- successful login
- successful verification
- successful logout
- successful password reset
2. 201 Created
- successful registration
3. 400 Bad Request
- invalid business request
- invalid referral logic
- malformed OTP flow request
4. 401 Unauthorized
- invalid credentials
- unauthenticated access
5. 403 Forbidden
- authenticated but not allowed
- account blocked or forbidden
6. 404 Not Found
- user not found
- target resource not found
7. 409 Conflict
- email already exists
- phone number already exists
- nickname already exists
8. 410 Gone
- expired OTP or expired verification link if appropriate
9. 422 Unprocessable Entity
- validation errors
10. 429 Too Many Requests
- too many OTP requests
- too many login attempts
11. 500 Internal Server Error
- unexpected server error

Claude should implement and document these clearly.

---

## 21. Example Error Mapping Table

###Success Codes
- SUCCESS
- REGISTER_SUCCESS
- LOGIN_SUCCESS
- LOGOUT_SUCCESS
- EMAIL_VERIFICATION_SENT
- EMAIL_VERIFIED_SUCCESS
- PHONE_OTP_SENT
- PHONE_VERIFIED_SUCCESS
- PASSWORD_RESET_LINK_SENT
- PASSWORD_RESET_SUCCESS
###Error Codes
- AUTH_VALIDATION_ERROR
- AUTH_INVALID_CREDENTIALS
- AUTH_ACCOUNT_NOT_FOUND
- AUTH_ACCOUNT_ALREADY_EXISTS
- AUTH_EMAIL_ALREADY_USED
- AUTH_PHONE_ALREADY_USED
- AUTH_NICKNAME_ALREADY_USED
- AUTH_UNVERIFIED_ACCOUNT
- AUTH_EMAIL_VERIFICATION_REQUIRED
- AUTH_PHONE_VERIFICATION_REQUIRED
- AUTH_INVALID_OTP
- AUTH_OTP_EXPIRED
- AUTH_OTP_TOO_MANY_REQUESTS
- AUTH_INVALID_RESET_TOKEN
- AUTH_RESET_PASSWORD_FAILED
- AUTH_REFERRAL_CODE_INVALID
- AUTH_UNAUTHORIZED
- AUTH_FORBIDDEN
- SYSTEM_INTERNAL_ERROR

Claude should implement this list in a single centralized place and keep all controllers consistent.

---

## 22. Security Requirements
- Passwords must be hashed
- OTP must expire
- OTP resend must be rate limited
- Login attempts should be throttled
- Sensitive routes must use proper auth middleware
- Client auth must remain isolated from admin auth
- Validation must be strict
- Business errors must never expose sensitive system details

---

##23. Expected Output from Claude

Claude must generate the following in order:

- Short implementation plan
- List of files to create or modify
- Migration for client_users
- ClientUser model
- config/auth.php updates for client guard/provider/broker
- Request validation classes
- Controllers
- OTP service
- Centralized error code file
- lang/en/errors.php
- lang/vi/errors.php
- Routes
- Example response helper or response formatter
- Testing instructions

---

##24. Coding Style Requirements
- Follow Laravel best practices
- Keep code clean and maintainable
- Avoid modifying unrelated admin code
- Use service classes where suitable
- Keep business error handling centralized
- Use dedicated request validation classes
- Prefer clear naming over overly clever abstractions

---

##25. Important Implementation Notes for Claude
- If the current project already has admin auth, do not break it
- If config/auth.php must be updated, clearly isolate client settings
- If using Blade, implement controllers and Blade-friendly flow
- If using API, implement JSON responses with the unified response format
- If project structure is unclear, prefer an API-first backend structure with reusable services

---

##26. Acceptance Criteria

The feature is considered complete when:

- a separate client_users table exists
- registration works
- login works with email or phone number
- logout works
- forgot password works
- email verification works
- phone OTP verification works
- is_verified and verified_at behave correctly
- balance and trading_balance exist and default correctly
- response format is unified
- error codes are centralized
- English and Vietnamese error mappings exist
- frontend can rely on status_code + code to display localized message
- admin auth remains untouched

---

##27. Final Instruction

Implement this feature in the safest and least disruptive way for the current Laravel project.

The solution must be:

- isolated from admin auth
- scalable
- secure
- maintainable
- ready for future trading platform integration