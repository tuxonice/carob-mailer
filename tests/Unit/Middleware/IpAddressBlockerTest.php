<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\IpAddressBlocker;
use App\Services\IpApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Mockery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class IpAddressBlockerTest extends TestCase
{
    protected $ipApiService;

    protected $middleware;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a mock of the IpApiService
        $this->ipApiService = Mockery::mock(IpApiService::class);

        // Create the middleware with the mocked service
        $this->middleware = new IpAddressBlocker($this->ipApiService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_allows_request_when_country_matches(): void
    {
        // Set the allowed country code
        Config::set('app.allow_country_code', 'us');

        // Create a request with a test IP
        $request = Request::create('/test', 'GET');
        $request->server->set('REMOTE_ADDR', '192.168.1.1');

        // Mock the IpApiService to return the allowed country code
        $this->ipApiService->shouldReceive('getCountryByIp')
            ->once()
            ->with('192.168.1.1')
            ->andReturn('us');

        // The middleware should allow the request to pass through
        $response = $this->middleware->handle($request, function ($req) {
            return response('OK');
        });

        // Assert that the response is what we expect
        $this->assertEquals('OK', $response->getContent());
    }

    public function test_blocks_request_when_country_does_not_match(): void
    {
        // Set the allowed country code
        Config::set('app.allow_country_code', 'us');

        // Create a request with a test IP
        $request = Request::create('/test', 'GET');
        $request->server->set('REMOTE_ADDR', '192.168.1.2');

        // Mock the IpApiService to return a different country code
        $this->ipApiService->shouldReceive('getCountryByIp')
            ->once()
            ->with('192.168.1.2')
            ->andReturn('ca');

        // The middleware should block the request with a 404
        $this->expectException(NotFoundHttpException::class);

        $this->middleware->handle($request, function ($req) {
            return response('OK');
        });
    }

    public function test_allows_request_when_no_country_restriction(): void
    {
        // Set no country restriction
        Config::set('app.allow_country_code', null);

        // Create a request with a test IP
        $request = Request::create('/test', 'GET');
        $request->server->set('REMOTE_ADDR', '192.168.1.3');

        // The IpApiService should not be called
        $this->ipApiService->shouldNotReceive('getCountryByIp');

        // The middleware should allow the request to pass through
        $response = $this->middleware->handle($request, function ($req) {
            return response('OK');
        });

        // Assert that the response is what we expect
        $this->assertEquals('OK', $response->getContent());
    }

    public function test_case_insensitive_country_code_comparison(): void
    {
        // Set the allowed country code in lowercase
        Config::set('app.allow_country_code', 'us');

        // Create a request with a test IP
        $request = Request::create('/test', 'GET');
        $request->server->set('REMOTE_ADDR', '192.168.1.4');

        // Mock the IpApiService to return the country code in uppercase
        $this->ipApiService->shouldReceive('getCountryByIp')
            ->once()
            ->with('192.168.1.4')
            ->andReturn('US');

        // The middleware should allow the request to pass through
        $response = $this->middleware->handle($request, function ($req) {
            return response('OK');
        });

        // Assert that the response is what we expect
        $this->assertEquals('OK', $response->getContent());
    }

    public function test_handles_null_country_code_from_service(): void
    {
        // Set the allowed country code
        Config::set('app.allow_country_code', 'us');

        // Create a request with a test IP
        $request = Request::create('/test', 'GET');
        $request->server->set('REMOTE_ADDR', '192.168.1.5');

        // Mock the IpApiService to return null (service error or unknown country)
        $this->ipApiService->shouldReceive('getCountryByIp')
            ->once()
            ->with('192.168.1.5')
            ->andReturn(null);

        // The middleware should block the request with a 404
        $this->expectException(NotFoundHttpException::class);

        $this->middleware->handle($request, function ($req) {
            return response('OK');
        });
    }
}
