<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies;

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;

    /**
     * Behind Cloudflare, nginx ingress, or load balancer: set TRUSTED_PROXIES=* or a comma IP list.
     */
    protected function proxies()
    {
        $v = env('TRUSTED_PROXIES');
        if ($v === null || $v === '') {
            return $this->proxies;
        }
        if ($v === '*' || $v === '**') {
            return '*';
        }

        return array_values(array_filter(array_map('trim', explode(',', $v))));
    }
}
