<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IpApiService
{
    private const API_URL = 'http://ip-api.com/json/';

    /**
     * Get the country of an IP address.
     *
     * @param  string  $ipAddress  The IP address to query.
     * @return string|null The country name if found, otherwise null.
     */
    public function getCountryByIp(string $ipAddress): ?string
    {
        // Check if the result is already cached
        $cacheKey = 'ip_country_code_'.$ipAddress;
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::get( self::API_URL.$ipAddress);
            $data = json_decode($response->getBody(), true);

            if (isset($data['status']) && $data['status'] === 'success' && isset($data['countryCode'])) {
                Cache::put($cacheKey, $data['countryCode'], now()->addDay());
                Log::info('Get country code: '.$ipAddress.' - '.$data['countryCode']);

                return $data['countryCode'];
            }
            Log::warning('Could not get country code: '.$ipAddress.' - '.$data['message']);
            Cache::put($cacheKey, '', now()->addDays(5));

            return null;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return null;
        }
    }
}
