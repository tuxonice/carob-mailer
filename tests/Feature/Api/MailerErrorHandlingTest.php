<?php

namespace Tests\Feature\Api;

use App\Jobs\SendEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MailerErrorHandlingTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // Set up fake storage for attachments
        Storage::fake('attachments');

        // Fake the bus so jobs aren't actually processed
        Bus::fake();

        // Authenticate for all tests
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );
    }

    public function test_handles_very_long_subject(): void
    {
        // Create a subject that exceeds the max length (255 chars)
        $longSubject = str_repeat('A', 300);

        $response = $this->post(
            env('API_URL').'/mailer/send',
            [
                'from' => [
                    'name' => 'Acme Inc.',
                ],
                'to' => [
                    'name' => 'John Doe',
                    'email' => 'john.doe@example.com',
                ],
                'subject' => $longSubject,
                'body' => [
                    'text' => 'Test body',
                    'html' => '<p>Test body</p>',
                ],
            ]
        );

        $response->assertStatus(422);
        $this->assertEquals([
            'error' => 'The subject field must not be greater than 255 characters.',
            'status' => false,
        ], $response->json());
    }

    public function test_handles_very_long_sender_name(): void
    {
        // Create a sender name that exceeds the max length (128 chars)
        $longName = str_repeat('A', 150);

        $response = $this->post(
            env('API_URL').'/mailer/send',
            [
                'from' => [
                    'name' => $longName,
                ],
                'to' => [
                    'name' => 'John Doe',
                    'email' => 'john.doe@example.com',
                ],
                'subject' => 'Test Subject',
                'body' => [
                    'text' => 'Test body',
                    'html' => '<p>Test body</p>',
                ],
            ]
        );

        $response->assertStatus(422);
        $this->assertEquals([
            'error' => 'The from.name field must not be greater than 128 characters.',
            'status' => false,
        ], $response->json());
    }

    public function test_handles_missing_attachment_filename(): void
    {
        $response = $this->post(
            env('API_URL').'/mailer/send',
            [
                'from' => [
                    'name' => 'Acme Inc.',
                ],
                'to' => [
                    'name' => 'John Doe',
                    'email' => 'john.doe@example.com',
                ],
                'subject' => 'Test Subject',
                'body' => [
                    'text' => 'Test body',
                    'html' => '<p>Test body</p>',
                ],
                'attachments' => [
                    [
                        'base64Content' => 'VGhpcyBpcyBhIGJhc2UgNjQgc3RyaW5n',
                        // Missing originalFileName
                    ],
                ],
            ]
        );

        $response->assertStatus(422);
        $this->assertEquals([
            'error' => 'The attachments.0.originalFileName field is required.',
            'status' => false,
        ], $response->json());
    }

    public function test_handles_malformed_request_structure(): void
    {
        // Send a request with completely wrong structure
        $response = $this->post(
            env('API_URL').'/mailer/send',
            [
                'message' => 'This is not the right format',
                'recipient' => 'john.doe@example.com',
            ]
        );

        $response->assertStatus(422);
        // The validation should catch multiple missing required fields
        $this->assertStringContainsString('error', json_encode($response->json()));
        $this->assertEquals(false, $response->json()['status']);
    }

    public function test_handles_empty_request_body(): void
    {
        // Send an empty request
        $response = $this->post(
            env('API_URL').'/mailer/send',
            []
        );

        $response->assertStatus(422);
        $this->assertStringContainsString('error', json_encode($response->json()));
        $this->assertEquals(false, $response->json()['status']);
    }

    public function test_handles_large_attachment(): void
    {
        // Create a large base64 string (approximately 1MB)
        $largeContent = base64_encode(str_repeat('A', 1024 * 1024));

        $response = $this->post(
            env('API_URL').'/mailer/send',
            [
                'from' => [
                    'name' => 'Acme Inc.',
                ],
                'to' => [
                    'name' => 'John Doe',
                    'email' => 'john.doe@example.com',
                ],
                'subject' => 'Test Subject',
                'body' => [
                    'text' => 'Test body',
                    'html' => '<p>Test body</p>',
                ],
                'attachments' => [
                    [
                        'base64Content' => $largeContent,
                        'originalFileName' => 'large_file.txt',
                    ],
                ],
            ]
        );

        // The request should still be processed successfully
        $response->assertOk();
        $this->assertEquals([
            'error' => '',
            'status' => true,
        ], $response->json());

        // Verify the job was dispatched
        Bus::assertDispatched(SendEmail::class);
    }
}
