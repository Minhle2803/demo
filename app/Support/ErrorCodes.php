<?php

namespace App\Support;

/**
 * Centralized business error / success code registry.
 *
 * ALL controllers and services must import and use constants from this class.
 * Never scatter raw string codes across the codebase.
 *
 * Frontend maps these codes to localized messages (see lang/en/errors.php, lang/vi/errors.php).
 */
final class ErrorCodes
{
    // -------------------------------------------------------------------------
    // Success codes
    // -------------------------------------------------------------------------
    const SUCCESS                     = 'SUCCESS';
    const REGISTER_SUCCESS            = 'REGISTER_SUCCESS';
    const LOGIN_SUCCESS               = 'LOGIN_SUCCESS';
    const LOGOUT_SUCCESS              = 'LOGOUT_SUCCESS';
    const EMAIL_VERIFICATION_SENT     = 'EMAIL_VERIFICATION_SENT';
    const EMAIL_VERIFIED_SUCCESS      = 'EMAIL_VERIFIED_SUCCESS';
    const PHONE_OTP_SENT              = 'PHONE_OTP_SENT';
    const PHONE_VERIFIED_SUCCESS      = 'PHONE_VERIFIED_SUCCESS';
    const PASSWORD_RESET_LINK_SENT    = 'PASSWORD_RESET_LINK_SENT';
    const PASSWORD_RESET_SUCCESS      = 'PASSWORD_RESET_SUCCESS';

    // -------------------------------------------------------------------------
    // Auth / registration errors
    // -------------------------------------------------------------------------
    const AUTH_VALIDATION_ERROR           = 'AUTH_VALIDATION_ERROR';
    const AUTH_INVALID_CREDENTIALS        = 'AUTH_INVALID_CREDENTIALS';
    const AUTH_ACCOUNT_NOT_FOUND          = 'AUTH_ACCOUNT_NOT_FOUND';
    const AUTH_ACCOUNT_ALREADY_EXISTS     = 'AUTH_ACCOUNT_ALREADY_EXISTS';
    const AUTH_EMAIL_ALREADY_USED         = 'AUTH_EMAIL_ALREADY_USED';
    const AUTH_PHONE_ALREADY_USED         = 'AUTH_PHONE_ALREADY_USED';
    const AUTH_NICKNAME_ALREADY_USED      = 'AUTH_NICKNAME_ALREADY_USED';
    const AUTH_UNVERIFIED_ACCOUNT         = 'AUTH_UNVERIFIED_ACCOUNT';
    const AUTH_EMAIL_VERIFICATION_REQUIRED = 'AUTH_EMAIL_VERIFICATION_REQUIRED';
    const AUTH_PHONE_VERIFICATION_REQUIRED = 'AUTH_PHONE_VERIFICATION_REQUIRED';
    const AUTH_UNAUTHORIZED               = 'AUTH_UNAUTHORIZED';
    const AUTH_FORBIDDEN                  = 'AUTH_FORBIDDEN';

    // -------------------------------------------------------------------------
    // OTP errors
    // -------------------------------------------------------------------------
    const AUTH_INVALID_OTP            = 'AUTH_INVALID_OTP';
    const AUTH_OTP_EXPIRED            = 'AUTH_OTP_EXPIRED';
    const AUTH_OTP_TOO_MANY_REQUESTS  = 'AUTH_OTP_TOO_MANY_REQUESTS';

    // -------------------------------------------------------------------------
    // Password reset errors
    // -------------------------------------------------------------------------
    const AUTH_INVALID_RESET_TOKEN    = 'AUTH_INVALID_RESET_TOKEN';
    const AUTH_RESET_PASSWORD_FAILED  = 'AUTH_RESET_PASSWORD_FAILED';

    // -------------------------------------------------------------------------
    // Referral
    // -------------------------------------------------------------------------
    const AUTH_REFERRAL_CODE_INVALID  = 'AUTH_REFERRAL_CODE_INVALID';

    // -------------------------------------------------------------------------
    // System
    // -------------------------------------------------------------------------
    const SYSTEM_INTERNAL_ERROR       = 'SYSTEM_INTERNAL_ERROR';

    // -------------------------------------------------------------------------
    // Prevent instantiation — this is a pure constants class
    // -------------------------------------------------------------------------
    private function __construct() {}
}
