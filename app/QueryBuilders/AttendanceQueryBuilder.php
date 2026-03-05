<?php

namespace App\QueryBuilders;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Builder;

class AttendanceQueryBuilder
{
    public function forChurch(int $churchId): Builder
    {
        return Attendance::query()->where('church_id', $churchId);
    }

    public function forMonth(Builder $query, string $month): Builder
    {
        return $query->whereMonth('attended_at', $month);
    }
}