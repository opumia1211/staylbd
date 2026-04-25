<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * Payment IPN and external webhooks use Route::withoutMiddleware(VerifyCsrfToken::class).
     * Admin routes must send a valid session CSRF token (forms include @csrf).
     *
     * @var array<int, string>
     */
    protected $except = [
        '*/frontend/header/preview',
    ];
}
