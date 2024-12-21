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
        $allowCountryCode = env('ALLOW_COUNTRY_CODE') ?: null;

        if ($allowCountryCode && strtolower($this->ipApiService->getCountryByIp($request->ip())) !== $allowCountryCode) {
            abort(404);
        }

        return $next($request);
    }
}
