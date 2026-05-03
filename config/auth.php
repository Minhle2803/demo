<?php

use App\Models\ClientUser;

/**
 * config/auth.php — CLIENT AUTH ADDITIONS
 * =========================================
 * Copy the marked sections into your existing config/auth.php.
 * Do NOT replace the file entirely — merge carefully to leave admin auth intact.
 *
 * ✅ Safe to merge: only adds new keys under 'guards', 'providers', 'passwords'.
 * ✅ Existing 'web', 'api', 'admin' entries are untouched.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults  ← leave whatever default is already here
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'guard' => 'web',   // ← keep your existing default, don't change it
        'passwords' => 'users', // ← keep your existing default
    ],

    /*
    |--------------------------------------------------------------------------
    | Guards  ← ADD the 'client' block below; keep all existing guards
    |--------------------------------------------------------------------------
    */
    'guards' => [
        // ... your existing guards (web, api, admin, etc.) stay here unchanged ...

        // ✅ NEW — client guard (session-based; swap driver to 'token' or
        //    'sanctum' if this is a pure API project)
        'client' => [
            'driver' => 'session',
            'provider' => 'client_users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers  ← ADD the 'client_users' block; keep existing providers
    |--------------------------------------------------------------------------
    */
    'providers' => [
        // ... your existing providers stay here unchanged ...

        // ✅ NEW — maps the 'client' guard to the ClientUser model
        'client_users' => [
            'driver' => 'eloquent',
            'model' => ClientUser::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords  ← ADD the 'client_users' broker; keep existing ones
    |--------------------------------------------------------------------------
    */
    'passwords' => [
        // ... your existing password brokers stay here unchanged ...

        // ✅ NEW — dedicated broker so client password resets are completely
        //    isolated from admin/web password resets (separate DB table too)
        'client_users' => [
            'provider' => 'client_users',
            'table' => 'client_password_reset_tokens', // separate table from 'password_reset_tokens'
            'expire' => 60,   // minutes
            'throttle' => 60,   // seconds between resend requests
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout  ← keep whatever you already have
    |--------------------------------------------------------------------------
    */
    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
