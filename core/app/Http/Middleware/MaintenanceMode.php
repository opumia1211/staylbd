<?php

namespace App\Http\Middleware;

use App\Constants\Status;
use App\Models\Frontend;
use Closure;

class MaintenanceMode
{
    public function handle($request, Closure $next)
    {
        if (gs('maintenance_mode') == Status::ENABLE) {
            // Allow whitelisted IPs to bypass maintenance
            if ($this->isIpWhitelisted($request->ip())) {
                return $next($request);
            }

            // Allow subscribe form on maintenance page to work
            if ($request->isMethod('POST') && $request->is('subscribe')) {
                return $next($request);
            }

            if ($request->is('api/*')) {
                $notify[] = 'Our application is currently in maintenance mode';
                return response()->json([
                    'remark'=>'maintenance_mode',
                    'status'=>'error',
                    'message'=>['error'=>$notify]
                ]);
            }

            return to_route('maintenance');
        }
        return $next($request);
    }

    /**
     * Check if the given IP is in the whitelist.
     */
    protected function isIpWhitelisted(string $ip): bool
    {
        $maintenance = Frontend::where('data_keys', 'maintenance.data')->first();
        if (!$maintenance || empty($maintenance->data_values->ip_whitelist)) {
            return false;
        }

        $whitelist = array_map('trim', explode(',', $maintenance->data_values->ip_whitelist));
        $whitelist = array_filter($whitelist);

        return in_array($ip, $whitelist);
    }
}
