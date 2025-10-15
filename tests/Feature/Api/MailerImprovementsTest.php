<?php

namespace Tests\Feature\Api;

use App\Jobs\SendEmail;
use App\Models\Mail;
use App\Models\User;
use App\Services\MailDeliveryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail as MailFacade;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MailerImprovementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_is_marked_as_sent(): void
    {
        // Create a mail record
        $mail = new Mail();
        $mail->setFromName('Test Sender')
            ->setToName('Test Recipient')
            ->setToEmail('recipient@example.com')
            ->setSubject('Test Subject')
            ->setBodyText('Test plain text body')
            ->setBodyHtml('<p>Test HTML body</p>')
            ->setAttachments('[]')
            ->save();

        // Create the job
        $job = new SendEmail($mail);

        // Mock the Mail facade
        MailFacade::fake();

        // Process the job
        $mailDeliveryService = app(MailDeliveryService::class);
        $job->handle($mailDeliveryService);

        // Refresh the mail model from the database
        $mail->refresh();

        // Assert that the mail was marked as sent
        $this->assertTrue($mail->isSent());
    }

    public function test_error_handling_in_send_email_job(): void
    {
        // Create a mail record
        $mail = new Mail();
        $mail->setFromName('Test Sender')
            ->setToName('Test Recipient')
            ->setToEmail('recipient@example.com')
            ->setSubject('Test Subject')
            ->setBodyText('Test plain text body')
            ->setBodyHtml('<p>Test HTML body</p>')
            ->setAttachments('[]')
            ->save();

        // Create the job
        $job = new SendEmail($mail);

        // Mock the Mail facade to throw an exception
        MailFacade::shouldReceive('send')
            ->once()
            ->andThrow(new \Exception('Test exception'));

        // Mock the Log facade
        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message, $context) use ($mail) {
                return strpos($message, sprintf('Email #%d sending failed: Test exception', $mail->getId())) !== false;
            });

        // Expect an exception to be thrown
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Test exception');

        // Process the job
        $mailDeliveryService = app(MailDeliveryService::class);
        $job->handle($mailDeliveryService);
    }

    public function test_rate_limiting_on_api_endpoint(): void
    {
        // Fake the queue to prevent actual job processing
        \Illuminate\Support\Facades\Bus::fake();

        // Authenticate
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        // Create a valid email request
        $emailData = [
            'from' => [
                'name' => 'Acme Inc.',
            ],
            'to' => [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
            ],
            'subject' => 'Email subject',
            'body' => [
                'text' => 'Test body',
                'html' => '<p>Test body</p>',
            ],
        ];

        // The route is configured with throttle:20,1 (20 requests per minute)
        // Make 20 successful requests
        for ($i = 0; $i < 20; $i++) {
            $response = $this->postJson(env('API_URL').'/mailer/send', $emailData);
            $response->assertOk();
            $this->assertEquals(true, $response->json()['status']);
        }

        // The 21st request should be rate limited
        $response = $this->postJson(env('API_URL').'/mailer/send', $emailData);
        $response->assertStatus(429); // Too Many Requests
    }
}
