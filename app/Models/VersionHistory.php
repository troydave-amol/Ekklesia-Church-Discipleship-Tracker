<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VersionHistory extends Model
{
    protected $table = 'central_version_history';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'changed_by',
        'change_description',
        'old_values',
        'new_values',
        'action',
    ];

    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
        'created_at' => 'datetime',
    ];

    public function changedByAdmin(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'changed_by');
    }

    public function scopeForEntity($query, string $entityType, int $entityId)
    {
        return $query->where('entity_type', $entityType)->where('entity_id', $entityId);
    }

    public static function logChange(
        string $entityType,
        int $entityId,
        ?int $changedBy,
        string $action,
        string $description,
        ?array $oldValues = null,
        ?array $newValues = null
    ): self {
        return static::create([
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'changed_by' => $changedBy,
            'action' => $action,
            'change_description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }
}
