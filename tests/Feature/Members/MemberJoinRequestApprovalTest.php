<?php

namespace Tests\Feature\Members;

use App\Mail\MemberJoinRequestApprovedMail;
use App\Mail\MemberJoinRequestRejectedMail;
use App\Models\MemberJoinRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TenantTestCase;

class MemberJoinRequestApprovalTest extends TenantTestCase
{
    public function test_church_admin_can_view_pending_join_requests_in_members_page(): void
    {
        $admin = $this->createTenantUser('church_admin');
        $joinRequest = $this->createPendingJoinRequest();

        $response = $this->actingAs($admin)->get('/users?tab=pending');

        $response->assertOk();
        $response->assertSee('Pending Join Requests');
        $response->assertSee($joinRequest->email);
    }

    public function test_approving_join_request_creates_member_records_and_marks_request_approved(): void
    {
        Mail::fake();

        $admin = $this->createTenantUser('church_admin', [
            'email' => 'admin@example.com',
        ]);
        $joinRequest = $this->createPendingJoinRequest([
            'email' => 'approved@example.com',
            'requested_role' => 'program_leader',
        ]);

        $response = $this->actingAs($admin)->post(route('join-requests.approve', $joinRequest, false));

        $response->assertRedirect(route('users.index', ['tab' => 'pending'], false));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('member_join_requests', [
            'id' => $joinRequest->id,
            'status' => MemberJoinRequest::STATUS_APPROVED,
            'reviewed_by_user_id' => $admin->id,
        ]);
        $this->assertDatabaseHas('users', [
            'church_id' => $this->church->id,
            'email' => 'approved@example.com',
            'role' => 'program_leader',
        ]);

        $member = \App\Models\User::query()->where('email', 'approved@example.com')->firstOrFail();

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $member->id,
        ]);
        $this->assertDatabaseHas('church_membership', [
            'user_id' => $member->id,
            'church_id' => $this->church->id,
            'status' => 'new_member',
        ]);

        Mail::assertQueued(MemberJoinRequestApprovedMail::class);
    }

    public function test_approval_rechecks_subscription_limits_before_creating_member(): void
    {
        $this->tenant->update(['subscription_plan' => 'free']);

        for ($i = 1; $i <= 5; $i++) {
            $this->createTenantUser('program_leader', [
                'email' => "leader{$i}@example.com",
            ]);
        }

        $admin = $this->createTenantUser('church_admin', [
            'email' => 'admin@example.com',
        ]);
        $joinRequest = $this->createPendingJoinRequest([
            'email' => 'overflow@example.com',
            'requested_role' => 'program_leader',
        ]);

        $response = $this->actingAs($admin)->post(route('join-requests.approve', $joinRequest, false));

        $response->assertRedirect(route('users.index', ['tab' => 'pending'], false));
        $response->assertSessionHas('error', 'Leader limit reached for your plan.');
        $this->assertDatabaseMissing('users', [
            'email' => 'overflow@example.com',
        ]);
        $this->assertDatabaseHas('member_join_requests', [
            'id' => $joinRequest->id,
            'status' => MemberJoinRequest::STATUS_PENDING,
        ]);
    }

    public function test_rejecting_join_request_does_not_create_user_and_allows_resubmission(): void
    {
        Mail::fake();

        $admin = $this->createTenantUser('church_admin', [
            'email' => 'admin@example.com',
        ]);
        $joinRequest = $this->createPendingJoinRequest([
            'email' => 'retry@example.com',
        ]);

        $response = $this->actingAs($admin)->post(route('join-requests.reject', $joinRequest, false), [
            'rejection_note' => 'Please contact the church office first.',
        ]);

        $response->assertRedirect(route('users.index', ['tab' => 'pending'], false));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('member_join_requests', [
            'id' => $joinRequest->id,
            'status' => MemberJoinRequest::STATUS_REJECTED,
            'rejection_note' => 'Please contact the church office first.',
        ]);
        $this->assertDatabaseMissing('users', [
            'email' => 'retry@example.com',
        ]);

        Mail::assertQueued(MemberJoinRequestRejectedMail::class);

        $resubmit = $this->post('/member/register', [
            'first_name' => 'Retry',
            'last_name' => 'Member',
            'email' => 'retry@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'requested_role' => 'disciple',
        ]);

        $resubmit->assertRedirect(route('login', absolute: false));
        $this->assertDatabaseCount('member_join_requests', 2);
        $this->assertDatabaseHas('member_join_requests', [
            'email' => 'retry@example.com',
            'status' => MemberJoinRequest::STATUS_PENDING,
        ]);
    }

    public function test_non_admin_users_cannot_approve_or_reject_join_requests(): void
    {
        $disciple = $this->createTenantUser('disciple', [
            'email' => 'disciple@example.com',
        ]);
        $joinRequest = $this->createPendingJoinRequest([
            'email' => 'blocked@example.com',
        ]);

        $approve = $this->actingAs($disciple)->post(route('join-requests.approve', $joinRequest, false));
        $approve->assertRedirect(route('tenant.dashboard', absolute: false));

        $reject = $this->actingAs($disciple)->post(route('join-requests.reject', $joinRequest, false));
        $reject->assertRedirect(route('tenant.dashboard', absolute: false));

        $this->assertDatabaseHas('member_join_requests', [
            'id' => $joinRequest->id,
            'status' => MemberJoinRequest::STATUS_PENDING,
        ]);
    }

    private function createPendingJoinRequest(array $attributes = []): MemberJoinRequest
    {
        return MemberJoinRequest::query()->create(array_merge([
            'church_id' => $this->church->id,
            'first_name' => 'Troy',
            'last_name' => 'Leader',
            'email' => 'troy@example.com',
            'password_hash' => Hash::make('password123'),
            'requested_role' => 'disciple',
            'status' => MemberJoinRequest::STATUS_PENDING,
        ], $attributes));
    }
}
