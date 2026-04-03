<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Panel URL Prefix
    |--------------------------------------------------------------------------
    |
    | Change this in .env (ADMIN_PREFIX) to customize the admin URL. Never use
    | predictable values like "admin" in production without additional security.
    |
    */
    'prefix' => env('ADMIN_PREFIX', 'admin'),

    /*
    |--------------------------------------------------------------------------
    | Default project owner email (for staylbd:set-owner command)
    |--------------------------------------------------------------------------
    | Used when running php artisan staylbd:set-owner without email argument.
    | Do not put password in config; pass it as argument or when prompted.
    */
    'owner_email' => env('OWNER_EMAIL', 'digitalzero.com@gmail.com'),

    /*
    |--------------------------------------------------------------------------
    | Admin Login Captcha
    |--------------------------------------------------------------------------
    */
    'admin_login_captcha' => env('ADMIN_LOGIN_CAPTCHA', false),

    /*
    |--------------------------------------------------------------------------
    | Enable Admin Cache Clear Route
    |--------------------------------------------------------------------------
    | Set ENABLE_ADMIN_CLEAR=false in production to disable the /clear route.
    */
    'enable_clear' => env('ENABLE_ADMIN_CLEAR', true),

    /*
    |--------------------------------------------------------------------------
    | Admin 2FA (Owner/SuperAdmin mandatory)
    |--------------------------------------------------------------------------
    */
    'two_factor_mandatory_roles' => ['owner', 'super_admin'],
    'admin_2fa_attempt_limit'    => 5,
    'admin_2fa_attempt_decay'    => 60, // seconds
    // ±N 30s steps for clock skew (1–4). Higher = more tolerant of phone/server time drift.
    'admin_2fa_totp_window'      => (int) env('ADMIN_2FA_TOTP_WINDOW', 2),
    'admin_max_sessions'         => (int) env('ADMIN_MAX_SESSIONS', 5),
    'admin_invalidate_old_session_on_login' => env('ADMIN_INVALIDATE_OLD_SESSION_ON_LOGIN', true),

    /*
    |--------------------------------------------------------------------------
    | Zero-Trust Mode (Phase 6)
    |--------------------------------------------------------------------------
    */
    'zero_trust_mode' => filter_var(env('ZERO_TRUST_MODE', false), FILTER_VALIDATE_BOOLEAN),
    'reauth_required_minutes' => (int) env('REAUTH_REQUIRED_MINUTES', 15),
];
