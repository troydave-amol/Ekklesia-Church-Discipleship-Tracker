<?php

namespace App\Services;

use App\Models\Attendance;
use App\QueryBuilders\AttendanceQueryBuilder;

class AttendanceService
{
    public function __construct(protected AttendanceQueryBuilder $queryBuilder) {}

    public function list(int $churchId): \Illuminate\Pagination\LengthAwarePaginator
    {
        return $this->queryBuilder->forChurch($churchId)->latest()->paginate(20);
    }

    public function create(int $churchId, array $data): Attendance
    {
        return Attendance::create(array_merge($data, ['church_id' => $churchId]));
    }

    public function delete(int $churchId, int $id): void
    {
        Attendance::where('church_id', $churchId)->findOrFail($id)->delete();
    }
}