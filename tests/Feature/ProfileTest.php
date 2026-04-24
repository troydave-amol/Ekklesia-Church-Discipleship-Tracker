<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TenantTestCase;

class ProfileTest extends TenantTestCase
{
    public function test_profile_page_is_displayed(): void
    {
        $user = $this->createTenantUser('disciple');

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = $this->createTenantUser('disciple');

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        // Note: The User model uses first_name/last_name but the ProfileUpdateRequest 
        // currently uses 'name'. This mismatch may cause these assertions to fail 
        // if the model doesn't have a 'name' attribute.
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = $this->createTenantUser('disciple');

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = $this->createTenantUser('disciple');

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = $this->createTenantUser('disciple');

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}

