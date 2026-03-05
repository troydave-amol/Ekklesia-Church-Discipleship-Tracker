<?php   

namespace App\Services;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\ChurchMembership;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProgramEnrollmentService
{
    /**
     * Create a new member and enroll them in a program level.
     */
    public function enroll(int $churchId, array $data): User
    {
        return DB::transaction(function () use ($churchId, $data) {

            $user = User::create([
                'church_id'  => $churchId,
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'nickname'   => $data['nickname'] ?? null,
                'email'      => $data['email'],
                'password'   => Hash::make($data['password'] ?? 'Welcome1!'),
                'role'       => $data['role'] ?? 'disciple',
            ]);

            UserProfile::create([
                'user_id'       => $user->id,
                'mobile_number' => $data['mobile_number'] ?? null,
                'gender'        => $data['gender'] ?? null,
                'civil_status'  => $data['civil_status'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'address'       => $data['address'] ?? null,
                'city'          => $data['city'] ?? null,
                'state'         => $data['state'] ?? null,
                'postal_code'   => $data['postal_code'] ?? null,
            ]);

            ChurchMembership::withoutGlobalScopes()->create([
                'user_id'                => $user->id,
                'church_id'              => $churchId,
                'leader_id'              => $data['leader_id'] ?? null,
                'status'                 => $data['status'] ?? 'new_member',
                'destiny_training_level' => $data['destiny_training_level'] ?? 'life_class',
                'satellite'              => $data['satellite'] ?? null,
                'joined_at'              => $data['joined_at'] ?? now(),
                'baptized_at'            => $data['baptized_at'] ?? null,
            ]);

            return $user;
        });
    }

    /**
     * Advance a disciple to the next training level.
     */
    public function advance(int $userId, string $newLevel): void
    {
        ChurchMembership::withoutGlobalScopes()
            ->where('user_id', $userId)
            ->update(['destiny_training_level' => $newLevel]);
    }

    /**
     * Bulk CSV import. Premium plan only.
     */
    public function bulkImport(int $churchId, string $csvPath): array
    {
        $created = 0; $skipped = 0; $errors = [];
        $rows    = array_map('str_getcsv', file($csvPath));
        $headers = array_shift($rows);

        foreach ($rows as $i => $row) {
            try {
                $data = array_combine($headers, $row);
                if (User::where('email', $data['email'])->exists()) {
                    $skipped++; continue;
                }
                $this->enroll($churchId, $data);
                $created++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($i + 2) . ": " . $e->getMessage();
            }
        }

        return compact('created', 'skipped', 'errors');
    }
}
