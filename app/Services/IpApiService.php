<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
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

            $client = new Client();

            // Send a GET request to the API
            $response = $client->request('GET', self::API_URL.$ipAddress);

            // Decode the JSON response
            $data = json_decode($response->getBody(), true);

            if (! isset($data['status']) || $data['status'] != 'success') {
                return null;
            }

            // Check if the country is set in the response
            if (isset($data['countryCode'])) {
                // Cache the result for 24 hours
                Cache::put($cacheKey, $data['countryCode'], now()->addDay());

                return $data['countryCode'];
            }

            return null;
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return null;
        }
    }
}
