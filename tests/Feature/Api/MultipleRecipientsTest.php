<?php

namespace Tests\Feature\Api;

use App\Jobs\SendEmail;
use App\Models\Mail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MultipleRecipientsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_send_to_multiple_recipients(): void
    {
        // Fake the queue to prevent actual job processing
        Bus::fake();

        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        // First recipient
        $response1 = $this->post(
            env('API_URL').'/mailer/send',
            [
                'from' => [
                    'name' => 'Acme Inc.',
                ],
                'to' => [
                    'name' => 'John Doe',
                    'email' => 'john.doe@example.com',
                ],
                'subject' => 'Email subject 1',
                'body' => [
                    'text' => 'Email to first recipient.',
                    'html' => '<p>Email to first recipient.</p>',
                ],
            ]
        );

        // Second recipient
        $response2 = $this->post(
            env('API_URL').'/mailer/send',
            [
                'from' => [
                    'name' => 'Acme Inc.',
                ],
                'to' => [
                    'name' => 'Jane Smith',
                    'email' => 'jane.smith@example.com',
                ],
                'subject' => 'Email subject 2',
                'body' => [
                    'text' => 'Email to second recipient.',
                    'html' => '<p>Email to second recipient.</p>',
                ],
            ]
        );

        $response1->assertOk();
        $response2->assertOk();

        // Assert that both Mail records were created
        $this->assertDatabaseHas('emails', [
            'to_name' => 'John Doe',
            'to_email' => 'john.doe@example.com',
            'subject' => 'Email subject 1',
        ]);

        $this->assertDatabaseHas('emails', [
            'to_name' => 'Jane Smith',
            'to_email' => 'jane.smith@example.com',
            'subject' => 'Email subject 2',
        ]);

        // Assert that the SendEmail jobs were dispatched for both recipients
        Bus::assertDispatched(SendEmail::class, function ($job) {
            return $job->mail->getToEmail() === 'john.doe@example.com';
        });

        Bus::assertDispatched(SendEmail::class, function ($job) {
            return $job->mail->getToEmail() === 'jane.smith@example.com';
        });
    }

    public function test_can_handle_batch_sending(): void
    {
        // This test simulates sending multiple emails in quick succession
        Bus::fake();

        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        // Send 5 emails in a loop
        $recipients = [
            ['name' => 'User One', 'email' => 'user1@example.com'],
            ['name' => 'User Two', 'email' => 'user2@example.com'],
            ['name' => 'User Three', 'email' => 'user3@example.com'],
            ['name' => 'User Four', 'email' => 'user4@example.com'],
            ['name' => 'User Five', 'email' => 'user5@example.com'],
        ];

        foreach ($recipients as $index => $recipient) {
            $response = $this->post(
                env('API_URL').'/mailer/send',
                [
                    'from' => [
                        'name' => 'Batch Sender',
                    ],
                    'to' => $recipient,
                    'subject' => "Batch Email #{$index}",
                    'body' => [
                        'text' => "This is batch email #{$index}",
                        'html' => "<p>This is batch email #{$index}</p>",
                    ],
                ]
            );

            $response->assertOk();
            $this->assertEquals([
                'error' => '',
                'status' => true,
            ], $response->json());
        }

        // Check that all 5 emails were created in the database
        $this->assertEquals(5, Mail::count());

        // Check that all 5 jobs were dispatched
        Bus::assertDispatchedTimes(SendEmail::class, 5);
    }
}
