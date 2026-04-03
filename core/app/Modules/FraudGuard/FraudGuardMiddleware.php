<?php

namespace App\Modules\FraudGuard;

use Closure;
use Illuminate\Http\Request;

class FraudGuardMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $service = app(FraudGuardService::class);
            if ($service->isBlockedIp($request->ip())) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => __('Access denied.')], 403);
                }
                return redirect()->back()->with('notify', [['error', __('Access denied.')]]);
            }
            if (auth()->check() && $service->isBlockedPhone(auth()->user()->mobile)) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => __('Account restricted.')], 403);
                }
                return redirect()->back()->with('notify', [['error', __('Account restricted. Contact support.')]]);
            }
        } catch (\Throwable $e) {
            \Log::debug('FraudGuard middleware: ' . $e->getMessage());
        }
        return $next($request);
    }
}
