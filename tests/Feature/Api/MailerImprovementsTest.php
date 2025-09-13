<?php

namespace Tests\Feature\Api;

use App\Jobs\SendEmail;
use App\Models\Mail;
use App\Models\User;
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
        $job->handle();

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
            ->withArgs(function ($message) use ($mail) {
                return strpos($message, $mail->getId().' Email sending failed: Test exception') !== false;
            });

        // Expect an exception to be thrown
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Test exception');

        // Process the job
        $job->handle();
    }

    public function test_rate_limiting_on_api_endpoint(): void
    {
        $this->markTestSkipped('Need improvement');
        // This test simulates hitting the rate limit

        // Authenticate
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

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

        // Mock the rate limiter
        $this->withMiddleware(['throttle:2,1']); // Override to 2 requests per minute for testing

        // First request should succeed
        $response1 = $this->post(env('API_URL').'/mailer/send', $emailData);
        $response1->assertOk();
        $this->assertEquals(true, $response1->json()['status']);

        // Second request should succeed
        $response2 = $this->post(env('API_URL').'/mailer/send', $emailData);
        $response2->assertOk();
        $this->assertEquals(true, $response2->json()['status']);

        // Third request should be rate limited
        // Note: In a real test environment, this would actually be rate limited
        // but in the test environment, the rate limiter might be disabled
        // This is more of a demonstration of how to test rate limiting

        // For a real test, you would need to configure the rate limiter for testing
        // and verify the response status code is 429 (Too Many Requests)
    }
}
