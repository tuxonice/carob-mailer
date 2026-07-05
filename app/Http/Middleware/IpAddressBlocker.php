<?php

namespace App\Http\Middleware;

use App\Services\IpApiService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IpAddressBlocker
{
    public function __construct(private IpApiService $ipApiService) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestIp = $request->ip();
        $allowedIps = config('app.allow_ips', []);

        if (in_array($requestIp, $allowedIps, true)) {
            return $next($request);
        }

        $allowCountryCode = config('app.allow_country_code') ?: null;

        if ($allowCountryCode && strtolower($this->ipApiService->getCountryByIp($requestIp)) !== $allowCountryCode) {
            abort(404);
        }

        return $next($request);
    }
}
