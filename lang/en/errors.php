<?php

/**
 * lang/en/errors.php
 *
 * En translation file for all client auth error and success codes.
 * Keys map 1-to-1 with App\Support\ErrorCodes constants.
 */

return [
    // Thành công
    'SUCCESS' => 'Success.',
    'REGISTER_SUCCESS' => 'Registration completed. Verification required.',
    'LOGIN_SUCCESS' => 'Login successful.',
    'LOGOUT_SUCCESS' => 'Logout successful.',
    'EMAIL_VERIFICATION_SENT' => 'Verification email sent.',
    'EMAIL_VERIFIED_SUCCESS' => 'Email verified.',
    'PHONE_OTP_SENT' => 'OTP sent.',
    'PHONE_VERIFIED_SUCCESS' => 'Phone verified.',
    'PASSWORD_RESET_LINK_SENT' => 'Password reset link sent.',
    'PASSWORD_RESET_SUCCESS' => 'Password reset completed.',

    // Authentication Errors
    'AUTH_VALIDATION_ERROR' => 'Invalid request parameters.',
    'AUTH_INVALID_CREDENTIALS' => 'Invalid credentials.',
    'AUTH_ACCOUNT_NOT_FOUND' => 'Account not found.',
    'AUTH_ACCOUNT_ALREADY_EXISTS' => 'Account already exists.',
    'AUTH_EMAIL_ALREADY_USED' => 'Email already registered.',
    'AUTH_PHONE_ALREADY_USED' => 'Phone number already registered.',
    'AUTH_NICKNAME_ALREADY_USED' => 'Nickname already in use.',
    'AUTH_ACCOUNT_BLOCKED' => 'Your account has been blocked. Please contact support.',
    'AUTH_UNVERIFIED_ACCOUNT' => 'Account not verified.',
    'AUTH_EMAIL_VERIFICATION_REQUIRED' => 'Email verification required.',
    'AUTH_PHONE_VERIFICATION_REQUIRED' => 'Phone verification required.',
    'AUTH_UNAUTHORIZED' => 'Unauthorized.',
    'AUTH_FORBIDDEN' => 'Forbidden.',

    // OTP Errors
    'AUTH_INVALID_OTP' => 'Invalid OTP.',
    'AUTH_OTP_EXPIRED' => 'OTP expired.',
    'AUTH_OTP_TOO_MANY_REQUESTS' => 'Too many requests.',

    // Password Reset Errors
    'AUTH_INVALID_RESET_TOKEN' => 'Invalid or expired reset token.',
    'AUTH_RESET_PASSWORD_FAILED' => 'Password reset failed.',

    // Referral
    'AUTH_REFERRAL_CODE_INVALID' => 'Invalid referral code.',

    // System
    'SYSTEM_INTERNAL_ERROR' => 'Internal server error.',

    // Trade Errors
    'TRADE_SESSION_NOT_FOUND' => 'Trading session not found.',
    'TRADE_SESSION_NOT_OPEN' => 'Trading session is not open.',
    'TRADE_SESSION_LOCKED' => 'Trading is locked for this session.',
    'TRADE_ALREADY_PLACED' => 'You have already placed a trade in this session.',
    'TRADE_INSUFFICIENT_BALANCE' => 'Insufficient balance.',
    'TRADE_INVALID_AMOUNT' => 'Invalid trade amount.',
    'TRADE_PLACE_SUCCESS' => 'Trade placed successfully.',
    'TRADE_RESULT_FETCHED' => 'Session result fetched.',
    'TRADE_SESSION_FETCHED' => 'Session fetched.',
    'TRADE_CANDLE_NOT_FOUND' => 'Candle data not found for this session.',
    'USER_NOT_FULLY_VERIFIED' => 'Your account is not fully verified. Please complete KYC.',

    // Profile / KYC
    'PROFILE_UPDATED' => 'Profile updated successfully.',
    'PASSWORD_UPDATED' => 'Password changed successfully.',
    'CURRENT_PASSWORD_INVALID' => 'Current password is incorrect.',

    // Deposit
    'DEPOSIT_AMOUNT_REQUIRED' => 'Please enter a deposit amount.',
    'DEPOSIT_QR_GENERATED' => 'Deposit QR code generated.',
    'DEPOSIT_CONFIRMED' => 'Deposit confirmed. Balance updated.',

    // Withdraw
    'WITHDRAW_REQUESTED' => 'Withdrawal request submitted. Please wait for admin approval.',
    'WITHDRAW_PROCESSED' => 'Withdrawal processed successfully.',
    'WITHDRAW_INSUFFICIENT_BALANCE' => 'Insufficient balance for this withdrawal.',
    'WITHDRAW_BANK_INFO_MISSING' => 'Please add your bank account information before withdrawing.',

    // KYC
    'KYC_UPLOAD_REQUIRED' => 'Please upload both front and back of your ID.',
    'KYC_QR_SCAN_FAILED' => 'Could not scan QR code from the uploaded ID. Please ensure the image is clear.',
    'KYC_DATA_MISMATCH' => 'ID information does not match your account information.',
    'KYC_VERIFIED_SUCCESS' => 'KYC verification completed successfully.',
    'KYC_ALREADY_VERIFIED' => 'Your account is already KYC verified.',

    // Spot Trading
    'SPOT_ORDER_CREATED' => 'Order placed successfully.',
    'SPOT_ORDER_CANCELLED' => 'Order cancelled successfully.',
    'SPOT_INVALID_SYMBOL' => 'Unsupported trading pair.',
    'SPOT_INVALID_PRICE' => 'Invalid price. Price must be greater than 0.',
    'SPOT_INVALID_QUANTITY' => 'Invalid quantity. Quantity must be greater than 0.',
    'SPOT_MIN_NOTIONAL_NOT_MET' => 'Order value is below the minimum notional.',
    'SPOT_INSUFFICIENT_BALANCE' => 'Insufficient balance.',
    'SPOT_ORDER_NOT_FOUND' => 'Order not found.',
    'SPOT_ORDER_ALREADY_FILLED' => 'Order has already been filled.',
    'SPOT_ORDER_ALREADY_CANCELLED' => 'Order has already been cancelled.',
    'SPOT_ORDER_CANCEL_FAILED' => 'Order cannot be cancelled.',
    'SPOT_MATCH_FAILED' => 'Order matching failed.',
    'SPOT_ADMIN_MATCH_SUCCESS' => 'Manual match executed successfully.',
    'SPOT_ADMIN_MATCH_FAILED' => 'Admin manual match failed.',
    'SPOT_UNAUTHORIZED' => 'Unauthorized.',
    'SPOT_USER_NOT_FULLY_VERIFIED' => 'KYC verification required to trade.',
    'SPOT_WALLET_NOT_FOUND' => 'Wallet not found for this asset.',
    'SPOT_NEGATIVE_BALANCE_BLOCKED' => 'Operation blocked to prevent negative balance.',

];
