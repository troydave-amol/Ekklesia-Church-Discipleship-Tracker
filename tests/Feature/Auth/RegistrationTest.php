<?php

namespace Tests\Feature\Auth;

use Tests\TenantTestCase;

class RegistrationTest extends TenantTestCase
{
    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'church_name' => 'Test Church',
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'subscription_plan' => 'basic',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('login', absolute: false));
    }
}

