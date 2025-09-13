<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
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
        $response->assertStatus(422);
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
        $response->assertStatus(422);
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
        $response->assertStatus(422);
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
        $response->assertStatus(422);
        $this->assertEquals([
            'error' => 'The body.html field is required.',
            'status' => false,
        ], $response->json());
    }

    public function test_api_send_email_with_attachment(): void
    {
        Storage::fake('attachments');

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
                    'html' => '<b>Simplicity is the essence of happiness</b>',
                ],
                'attachments' => [
                    [
                        'base64Content' => 'VGhpcyBpcyBhIGJhc2UgNjQgc3RyaW5n',
                        'originalFileName' => 'sample.txt',
                    ],
                    [
                        'base64Content' => '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QR4jeaB3rDfog+E+WOSxkucBmgfm4WnZ0yCP4TweGi8aeFrxhI8Rnw4+HfPoreHeWvTQcg8N/I34w6cs/Oi31+Xn5zn9natwybmMlAyzzBJP0ZDod96nceIq+f0AJ9rLOQplbmRzdHJlYW0KZW5kb2JqCjE2IDAgb2JqCiAgIDQwMgplbmRvYmoKMTcgMCBvYmoKPDwgL1R5cGUgL0ZvbnREZXNjcmlwdG9yCiAgIC9Gb250TmFtZSAvTElZUUJaK0RlamFWdVNhbnNNb25vCiAgIC9Gb250RmFtaWx5IChEZWphVnUgU2FucyBNb25vKQogICAvRmxhZ3MgMzIKICAgL0ZvbnRCQm94IFsgLTU1NyAtMzc0IDcxNyAxMDI3IF0KICAgL0l0YWxpY0FuZ2xlIDAKICAgL0FzY2VudCA5MjgKICAgL0Rlc2NlbnQgLTIzNQogICAvQ2FwSGVpZ2h0IDEwMjcKICAgL1N0ZW1WIDgwCiAgIC9TdGVtSCA4MAogICAvRm9udEZpbGUyIDEzIDAgUgo+PgplbmRvYmoKNyAwIG9iago8PCAvVHlwZSAvRm9udAogICAvU3VidHlwZSAvVHJ1ZVR5cGUKICAgL0Jhc2VGb250IC9MSVlRQlorRGVqYVZ1U2Fuc01vbm8KICAgL0ZpcnN0Q2hhciAzMgogICAvTGFzdENoYXIgMTIwCiAgIC9Gb250RGVzY3JpcHRvciAxNyAwIFIKICAgL0VuY29kaW5nIC9XaW5BbnNpRW5jb2RpbmcKICAgL1dpZHRocyBbIDYwMiAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgNjAyIDAgNjAyIDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDAgMCAwIDYwMiAwIDYwMiA2MDIgMCA2MDIgMCAwIDYwMiAwIDAgNjAyIDYwMiA2MDIgNjAyIDYwMiA2MDIgMCA2MDIgMCA2MDIgNjAyIDAgMCAwIDAgMCAwIDAgMCAwIDAgNjAyIDYwMiA2MDIgNjAyIDYwMiA2MDIgNjAyIDYwMiA2MDIgNjAyIDAgNjAyIDYwMiA2MDIgNjAyIDYwMiA2MDIgNjAyIDYwMiA2MDIgNjAyIDYwMiAwIDYwMiBdCiAgICAvVG9Vbmljb2RlIDE1IDAgUgo+PgplbmRvYmoKMSAwIG9iago8PCAvVHlwZSAvUGFnZXMKICAgL0tpZHMgWyAyIDAgUiBdCiAgIC9Db3VudCAxCj4+CmVuZG9iagoxOCAwIG9iago8PCAvUHJvZHVjZXIgKGNhaXJvIDEuMTYuMCAoaHR0cHM6Ly9jYWlyb2dyYXBoaWNzLm9yZykpCiAgIC9DcmVhdGlvbkRhdGUgKEQ6MjAyMzA5MjMxODA1MDIrMDEnMDApCj4+CmVuZG9iagoxOSAwIG9iago8PCAvVHlwZSAvQ2F0YWxvZwogICAvUGFnZXMgMSAwIFIKPj4KZW5kb2JqCnhyZWYKMCAyMAowMDAwMDAwMDAwIDY1NTM1IGYgCjAwMDAwMTcwOTEgMDAwMDAgbiAKMDAwMDAwMzc1NyAwMDAwMCBuIAowMDAwMDAzNjI5IDAwMDAwIG4gCjAwMDAwMDAwMTUgMDAwMDAgbiAKMDAwMDAwMzYwNiAwMDAwMCBuIAowMDAwMDA4OTM5IDAwMDAwIG4gCjAwMDAwMTY2MTcgMDAwMDAgbiAKMDAwMDAwMzk4OSAwMDAwMCBuIAowMDAwMDA4MjI0IDAwMDAwIG4gCjAwMDAwMDgyNDcgMDAwMDAgbiAKMDAwMDAwODY0NSAwMDAwMCBuIAowMDAwMDA4NjY4IDAwMDAwIG4gCjAwMDAwMDkzNjUgMDAwMDAgbiAKMDAwMDAxNTgxMCAwMDAwMCBuIAowMDAwMDE1ODM0IDAwMDAwIG4gCjAwMDAwMTYzMTUgMDAwMDAgbiAKMDAwMDAxNjMzOCAwMDAwMCBuIAowMDAwMDE3MTU2IDAwMDAwIG4gCjAwMDAwMTcyNzMgMDAwMDAgbiAKdHJhaWxlcgo8PCAvU2l6ZSAyMAogICAvUm9vdCAxOSAwIFIKICAgL0luZm8gMTggMCBSCj4+CnN0YXJ0eHJlZgoxNzMyNgolJUVPRgo=',
                        'originalFileName' => 'sample.pdf',
                    ],
                ],
            ]
        );
        $response->assertOk();
        $this->assertEquals([
            'error' => '',
            'status' => true,

        ], $response->json());
    }

    public function test_api_send_email_with_invalid_attachment(): void
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
                    'html' => '<b>Simplicity is the essence of happiness</b>',
                ],
                'attachments' => [
                    [
                        'base64Content' => 'wrong-base-64-string',
                        'originalFileName' => 'sample.txt',
                    ],
                ],
            ]
        );
        $response->assertStatus(422);
        $this->assertEquals([
            'error' => 'The attachments.0.base64Content must be a base 64 encoded string.',
            'status' => false,
        ], $response->json());
    }
}
