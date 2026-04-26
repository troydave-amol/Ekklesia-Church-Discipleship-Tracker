<?php

namespace Tests\Feature\Auth;

use App\Models\MemberJoinRequest;
use App\Notifications\MemberJoinRequestSubmitted;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TenantTestCase;

class RegistrationTest extends TenantTestCase
{
    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/member/register');

        $response->assertStatus(200);
    }

    public function test_join_request_submission_creates_pending_request_only(): void
    {
        $response = $this->post('/member/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'requested_role' => 'disciple',
        ]);

        $response->assertRedirect(route('login', absolute: false));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('member_join_requests', [
            'church_id' => $this->church->id,
            'email' => 'test@example.com',
            'requested_role' => 'disciple',
            'status' => MemberJoinRequest::STATUS_PENDING,
        ]);
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('user_profiles', 0);
        $this->assertDatabaseCount('church_membership', 0);
    }

    public function test_join_request_submission_notifies_church_admins(): void
    {
        Notification::fake();

        $admin = $this->createTenantUser('church_admin', [
            'email' => 'admin@example.com',
        ]);

        $this->post('/member/register', [
            'first_name' => 'Troy',
            'last_name' => 'Leader',
            'email' => 'troy@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'requested_role' => 'program_leader',
        ])->assertRedirect(route('login', absolute: false));

        Notification::assertSentTo($admin, MemberJoinRequestSubmitted::class);
    }

    public function test_duplicate_pending_join_requests_are_blocked(): void
    {
        MemberJoinRequest::query()->create([
            'church_id' => $this->church->id,
            'first_name' => 'Troy',
            'last_name' => 'Leader',
            'email' => 'troy@example.com',
            'password_hash' => Hash::make('password123'),
            'requested_role' => 'program_leader',
            'status' => MemberJoinRequest::STATUS_PENDING,
        ]);

        $response = $this->from('/member/register')->post('/member/register', [
            'first_name' => 'Troy',
            'last_name' => 'Leader',
            'email' => 'troy@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'requested_role' => 'program_leader',
        ]);

        $response->assertRedirect('/member/register');
        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('member_join_requests', 1);
    }

    public function test_existing_member_email_blocks_join_request_submission(): void
    {
        $this->createTenantUser('disciple', [
            'email' => 'member@example.com',
        ]);

        $response = $this->from('/member/register')->post('/member/register', [
            'first_name' => 'Existing',
            'last_name' => 'Member',
            'email' => 'member@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'requested_role' => 'disciple',
        ]);

        $response->assertRedirect('/member/register');
        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('member_join_requests', 0);
    }

    public function test_pending_applicant_cannot_log_in_and_sees_pending_message(): void
    {
        MemberJoinRequest::query()->create([
            'church_id' => $this->church->id,
            'first_name' => 'Pending',
            'last_name' => 'Member',
            'email' => 'pending@example.com',
            'password_hash' => Hash::make('password123'),
            'requested_role' => 'disciple',
            'status' => MemberJoinRequest::STATUS_PENDING,
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'pending@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'email' => 'Your join request is still awaiting church approval.',
        ]);
        $this->assertGuest();
    }

    public function test_rejected_applicant_sees_rejected_message_on_login(): void
    {
        MemberJoinRequest::query()->create([
            'church_id' => $this->church->id,
            'first_name' => 'Rejected',
            'last_name' => 'Member',
            'email' => 'rejected@example.com',
            'password_hash' => Hash::make('password123'),
            'requested_role' => 'disciple',
            'status' => MemberJoinRequest::STATUS_REJECTED,
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'rejected@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'email' => 'Your previous request was not approved. Submit a new request to try again.',
        ]);
        $this->assertGuest();
    }
}
