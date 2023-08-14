<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_not_authenticated_user_regirects_to_login_page(): void
    {
        $response = $this->get('/');

        $response->assertStatus(302);
    }
}
