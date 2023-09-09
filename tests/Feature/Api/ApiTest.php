<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_reach_api_end_point(): void
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $response = $this->post(
            env('API_URL').'/mailer/send',
            [
                'from' => [
                    'name' => 'Acme Inc.',
                ],
                'to' => [
                    'name' => 'Jonh Doe',
                    'email' => 'john.doe@example.com',
                ],
                'subject' => 'Email subject',
                'body' => [
                    'text' => 'Simplicity is the essence of happiness.',
                    'html' => '<i>Simplicity</i> is the essence of <b>happiness.</b>',
                ],
            ]
        );
        $response->assertOk();
        $this->assertEquals([
            'error' => '',
            'status' => true,

        ], $response->json());
    }

    public function test_api_return_error_on_missing_from_name(): void
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $response = $this->post(
            env('API_URL').'/mailer/send',
            [
                'from' => [
                    'name' => '',
                ],
                'to' => [
                    'name' => 'Jonh Doe',
                    'email' => 'john.doe@example.com',
                ],
                'subject' => 'Email subject',
                'body' => [
                    'text' => 'Simplicity is the essence of happiness.',
                    'html' => '<i>Simplicity</i> is the essence of <b>happiness.</b>',
                ],
            ]
        );
        $response->assertOk();
        $this->assertEquals([
            'error' => 'The from.name field is required.',
            'status' => false,

        ], $response->json());
    }

    public function test_api_return_error_on_missing_to_name(): void
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $response = $this->post(
            env('API_URL').'/mailer/send',
            [
                'from' => [
                    'name' => 'Acme Inc',
                ],
                'to' => [
                    'name' => '',
                    'email' => 'john.doe@example.com',
                ],
                'subject' => 'Email subject',
                'body' => [
                    'text' => 'Simplicity is the essence of happiness.',
                    'html' => '<i>Simplicity</i> is the essence of <b>happiness.</b>',
                ],
            ]
        );
        $response->assertOk();
        $this->assertEquals([
            'error' => 'The to.name field is required.',
            'status' => false,

        ], $response->json());
    }

    public function test_api_return_error_on_invalid_to_email(): void
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $response = $this->post(
            env('API_URL').'/mailer/send',
            [
                'from' => [
                    'name' => 'Acme Inc.',
                ],
                'to' => [
                    'name' => 'Jonh Doe',
                    'email' => 'john.doe',
                ],
                'subject' => 'Email subject',
                'body' => [
                    'text' => 'Simplicity is the essence of happiness.',
                    'html' => '<i>Simplicity</i> is the essence of <b>happiness.</b>',
                ],
            ]
        );
        $response->assertOk();
        $this->assertEquals([
            'error' => 'The to.email field must be a valid email address.',
            'status' => false,

        ], $response->json());
    }

    public function test_api_return_error_on_missing_html_body(): void
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $response = $this->post(
            env('API_URL').'/mailer/send',
            [
                'from' => [
                    'name' => 'Acme Inc.',
                ],
                'to' => [
                    'name' => 'Jonh Doe',
                    'email' => 'john.doe@example.com',
                ],
                'subject' => 'Email subject',
                'body' => [
                    'text' => 'Simplicity is the essence of happiness.',
                    'html' => '',
                ],
            ]
        );
        $response->assertOk();
        $this->assertEquals([
            'error' => 'The body.html field is required.',
            'status' => false,

        ], $response->json());
    }
}
