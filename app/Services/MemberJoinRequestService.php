<?php

namespace App\Services;

use App\Mail\MemberJoinRequestApprovedMail;
use App\Mail\MemberJoinRequestRejectedMail;
use App\Models\Church;
use App\Models\MemberJoinRequest;
use App\Models\User;
use App\Notifications\MemberJoinRequestSubmitted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class MemberJoinRequestService
{
    public function __construct(
        private ProgramEnrollmentService $programEnrollmentService,
        private SubscriptionService $subscriptionService,
    ) {}

    public function submit(array $data): MemberJoinRequest
    {
        $church = Church::query()->first();

        if (! $church) {
            throw new RuntimeException('This church workspace is not yet set up.');
        }

        $email = strtolower(trim((string) $data['email']));

        if (User::query()->where('email', $email)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'An account with this email already exists for this church.',
            ]);
        }

        if (MemberJoinRequest::query()
            ->where('church_id', $church->id)
            ->where('email', $email)
            ->where('status', MemberJoinRequest::STATUS_PENDING)
            ->exists()) {
            throw ValidationException::withMessages([
                'email' => 'A join request for this email is already pending review.',
            ]);
        }

        $joinRequest = MemberJoinRequest::query()->create([
            'church_id' => $church->id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $email,
            'password_hash' => Hash::make($data['password']),
            'requested_role' => $data['requested_role'],
            'status' => MemberJoinRequest::STATUS_PENDING,
        ]);

        $admins = User::query()
            ->where('church_id', $church->id)
            ->whereIn('role', ['church_admin', 'pastor'])
            ->get();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new MemberJoinRequestSubmitted($joinRequest));
        }

        return $joinRequest;
    }

    public function approve(MemberJoinRequest $joinRequest, User $reviewer): User
    {
        $user = DB::transaction(function () use ($joinRequest, $reviewer) {
            $lockedRequest = MemberJoinRequest::query()
                ->whereKey($joinRequest->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedRequest->isPending()) {
                throw new RuntimeException('This join request is no longer pending.');
            }

            if (User::query()->where('email', $lockedRequest->email)->exists()) {
                throw new RuntimeException('A member with this email already exists.');
            }

            $this->assertSubscriptionCapacity($lockedRequest->requested_role);

            $user = $this->programEnrollmentService->createMember($lockedRequest->church_id, [
                'first_name' => $lockedRequest->first_name,
                'last_name' => $lockedRequest->last_name,
                'email' => $lockedRequest->email,
                'role' => $lockedRequest->requested_role,
                'password_hash' => $lockedRequest->password_hash,
                'status' => 'new_member',
                'joined_at' => now()->toDateString(),
            ]);

            $lockedRequest->forceFill([
                'status' => MemberJoinRequest::STATUS_APPROVED,
                'reviewed_by_user_id' => $reviewer->id,
                'reviewed_at' => now(),
                'approved_user_id' => $user->id,
                'rejection_note' => null,
            ])->save();

            $joinRequest->setRawAttributes($lockedRequest->getAttributes(), true);

            return $user;
        });

        $this->sendApprovalMail($joinRequest, $user);

        return $user;
    }

    public function reject(MemberJoinRequest $joinRequest, User $reviewer, ?string $rejectionNote = null): void
    {
        DB::transaction(function () use ($joinRequest, $reviewer, $rejectionNote) {
            $lockedRequest = MemberJoinRequest::query()
                ->whereKey($joinRequest->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedRequest->isPending()) {
                throw new RuntimeException('This join request is no longer pending.');
            }

            $lockedRequest->forceFill([
                'status' => MemberJoinRequest::STATUS_REJECTED,
                'reviewed_by_user_id' => $reviewer->id,
                'reviewed_at' => now(),
                'approved_user_id' => null,
                'rejection_note' => $rejectionNote,
            ])->save();

            $joinRequest->setRawAttributes($lockedRequest->getAttributes(), true);
        });

        $this->sendRejectionMail($joinRequest);
    }

    private function assertSubscriptionCapacity(string $requestedRole): void
    {
        if ($requestedRole === 'program_leader') {
            $leaderCount = User::query()
                ->whereIn('role', ['program_leader', 'cell_leader'])
                ->count();

            if (! $this->subscriptionService->checkLimit('max_leaders', $leaderCount)) {
                throw new RuntimeException('Leader limit reached for your plan.');
            }

            return;
        }

        $discipleCount = User::query()
            ->where('role', 'disciple')
            ->count();

        if (! $this->subscriptionService->checkLimit('max_disciples', $discipleCount)) {
            throw new RuntimeException('Disciple limit reached for your plan. Upgrade to approve more members.');
        }
    }

    private function sendApprovalMail(MemberJoinRequest $joinRequest, User $user): void
    {
        try {
            Mail::to($joinRequest->email)->send(new MemberJoinRequestApprovedMail(
                $joinRequest,
                $user,
                url(route('login', absolute: false)),
            ));
        } catch (Throwable $e) {
            Log::warning('Join request approval email failed to dispatch', [
                'join_request_id' => $joinRequest->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function sendRejectionMail(MemberJoinRequest $joinRequest): void
    {
        try {
            Mail::to($joinRequest->email)->send(new MemberJoinRequestRejectedMail(
                $joinRequest,
                url(route('member.register', absolute: false)),
            ));
        } catch (Throwable $e) {
            Log::warning('Join request rejection email failed to dispatch', [
                'join_request_id' => $joinRequest->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
