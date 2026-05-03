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
    const SUCCESS = 'SUCCESS';

    const REGISTER_SUCCESS = 'REGISTER_SUCCESS';

    const LOGIN_SUCCESS = 'LOGIN_SUCCESS';

    const LOGOUT_SUCCESS = 'LOGOUT_SUCCESS';

    const EMAIL_VERIFICATION_SENT = 'EMAIL_VERIFICATION_SENT';

    const EMAIL_VERIFIED_SUCCESS = 'EMAIL_VERIFIED_SUCCESS';

    const PHONE_OTP_SENT = 'PHONE_OTP_SENT';

    const PHONE_VERIFIED_SUCCESS = 'PHONE_VERIFIED_SUCCESS';

    const PASSWORD_RESET_LINK_SENT = 'PASSWORD_RESET_LINK_SENT';

    const PASSWORD_RESET_SUCCESS = 'PASSWORD_RESET_SUCCESS';

    // -------------------------------------------------------------------------
    // Auth / registration errors
    // -------------------------------------------------------------------------
    const AUTH_VALIDATION_ERROR = 'AUTH_VALIDATION_ERROR';

    const AUTH_INVALID_CREDENTIALS = 'AUTH_INVALID_CREDENTIALS';

    const AUTH_ACCOUNT_NOT_FOUND = 'AUTH_ACCOUNT_NOT_FOUND';

    const AUTH_ACCOUNT_ALREADY_EXISTS = 'AUTH_ACCOUNT_ALREADY_EXISTS';

    const AUTH_EMAIL_ALREADY_USED = 'AUTH_EMAIL_ALREADY_USED';

    const AUTH_PHONE_ALREADY_USED = 'AUTH_PHONE_ALREADY_USED';

    const AUTH_NICKNAME_ALREADY_USED = 'AUTH_NICKNAME_ALREADY_USED';

    const AUTH_UNVERIFIED_ACCOUNT = 'AUTH_UNVERIFIED_ACCOUNT';

    const AUTH_EMAIL_VERIFICATION_REQUIRED = 'AUTH_EMAIL_VERIFICATION_REQUIRED';

    const AUTH_PHONE_VERIFICATION_REQUIRED = 'AUTH_PHONE_VERIFICATION_REQUIRED';

    const AUTH_UNAUTHORIZED = 'AUTH_UNAUTHORIZED';

    const AUTH_FORBIDDEN = 'AUTH_FORBIDDEN';

    // -------------------------------------------------------------------------
    // OTP errors
    // -------------------------------------------------------------------------
    const AUTH_INVALID_OTP = 'AUTH_INVALID_OTP';

    const AUTH_OTP_EXPIRED = 'AUTH_OTP_EXPIRED';

    const AUTH_OTP_TOO_MANY_REQUESTS = 'AUTH_OTP_TOO_MANY_REQUESTS';

    // -------------------------------------------------------------------------
    // Password reset errors
    // -------------------------------------------------------------------------
    const AUTH_INVALID_RESET_TOKEN = 'AUTH_INVALID_RESET_TOKEN';

    const AUTH_RESET_PASSWORD_FAILED = 'AUTH_RESET_PASSWORD_FAILED';

    // -------------------------------------------------------------------------
    // Referral
    // -------------------------------------------------------------------------
    const AUTH_REFERRAL_CODE_INVALID = 'AUTH_REFERRAL_CODE_INVALID';

    // -------------------------------------------------------------------------
    // System
    // -------------------------------------------------------------------------
    const SYSTEM_INTERNAL_ERROR = 'SYSTEM_INTERNAL_ERROR';

    // -------------------------------------------------------------------------
    // Profile / KYC
    // -------------------------------------------------------------------------
    const PROFILE_UPDATED = 'PROFILE_UPDATED';

    const PASSWORD_UPDATED = 'PASSWORD_UPDATED';

    const CURRENT_PASSWORD_INVALID = 'CURRENT_PASSWORD_INVALID';

    // Deposit
    const DEPOSIT_AMOUNT_REQUIRED = 'DEPOSIT_AMOUNT_REQUIRED';

    const DEPOSIT_QR_GENERATED = 'DEPOSIT_QR_GENERATED';

    const DEPOSIT_CONFIRMED = 'DEPOSIT_CONFIRMED';

    // Withdraw
    const WITHDRAW_REQUESTED = 'WITHDRAW_REQUESTED';

    const WITHDRAW_PROCESSED = 'WITHDRAW_PROCESSED';

    const WITHDRAW_INSUFFICIENT_BALANCE = 'WITHDRAW_INSUFFICIENT_BALANCE';

    const WITHDRAW_BANK_INFO_MISSING = 'WITHDRAW_BANK_INFO_MISSING';

    // KYC
    const KYC_UPLOAD_REQUIRED = 'KYC_UPLOAD_REQUIRED';

    const KYC_QR_SCAN_FAILED = 'KYC_QR_SCAN_FAILED';

    const KYC_DATA_MISMATCH = 'KYC_DATA_MISMATCH';

    const KYC_VERIFIED_SUCCESS = 'KYC_VERIFIED_SUCCESS';

    const KYC_ALREADY_VERIFIED = 'KYC_ALREADY_VERIFIED';

    // -------------------------------------------------------------------------
    // Prevent instantiation — this is a pure constants class
    // -------------------------------------------------------------------------
    private function __construct() {}

    // -------------------------------------------------------------------------
    // Chart — success codes
    // -------------------------------------------------------------------------

    const CHART_CANDLES_FETCHED = 'CHART_CANDLES_FETCHED';

    const CHART_FUTURE_DIRECTION_UPDATED = 'CHART_FUTURE_DIRECTION_UPDATED';

    const CHART_RANGE_REWRITTEN = 'CHART_RANGE_REWRITTEN';

    // -------------------------------------------------------------------------
    // Chart — error codes
    // -------------------------------------------------------------------------
    const CHART_INVALID_REQUEST = 'CHART_INVALID_REQUEST';

    const CHART_INVALID_SYMBOL = 'CHART_INVALID_SYMBOL';

    const CHART_INVALID_INTERVAL = 'CHART_INVALID_INTERVAL';

    const CHART_INVALID_DIRECTION = 'CHART_INVALID_DIRECTION';

    const CHART_INVALID_TIMESTAMP_RANGE = 'CHART_INVALID_TIMESTAMP_RANGE';

    const CHART_RANGE_TOO_LARGE = 'CHART_RANGE_TOO_LARGE';

    const CHART_CANDLE_NOT_FOUND = 'CHART_CANDLE_NOT_FOUND';

    const CHART_GENERATION_FAILED = 'CHART_GENERATION_FAILED';

    const CHART_INTERNAL_ERROR = 'CHART_INTERNAL_ERROR';

    // Trading
    const TRADE_SESSION_NOT_FOUND = 'TRADE_SESSION_NOT_FOUND';

    const TRADE_SESSION_NOT_OPEN = 'TRADE_SESSION_NOT_OPEN';

    const TRADE_SESSION_LOCKED = 'TRADE_SESSION_LOCKED';

    const TRADE_ALREADY_PLACED = 'TRADE_ALREADY_PLACED';

    const TRADE_INSUFFICIENT_BALANCE = 'TRADE_INSUFFICIENT_BALANCE';

    const TRADE_INVALID_AMOUNT = 'TRADE_INVALID_AMOUNT';

    const TRADE_PLACE_SUCCESS = 'TRADE_PLACE_SUCCESS';

    const TRADE_RESULT_FETCHED = 'TRADE_RESULT_FETCHED';

    const TRADE_SESSION_FETCHED = 'TRADE_SESSION_FETCHED';

    const TRADE_CANDLE_NOT_FOUND = 'TRADE_CANDLE_NOT_FOUND';

    const USER_NOT_FULLY_VERIFIED = 'USER_NOT_FULLY_VERIFIED';
}
