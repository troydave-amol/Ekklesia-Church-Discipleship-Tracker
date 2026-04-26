<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Central audit log for all admin-panel actions.
 *
 * Replaces the old VersionHistory::logChange() calls with a dedicated
 * audit-trail model backed by the admin_audit_logs table.
 */
class AdminAuditLog extends Model
{
    protected $connection = 'central';

    protected $table = 'admin_audit_logs';

    protected $fillable = [
        'admin_user_id',
        'tenant_id',
        'action',
        'resource_type',
        'resource_id',
        'changes',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    // ── Relationships ────────────────────────────────────────────

    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class);
    }

    // ── Static Helper ────────────────────────────────────────────

    /**
     * Record an audit entry for an admin action.
     *
     * Drop-in replacement for the old VersionHistory::logChange() signature:
     *   logChange($resourceType, $resourceId, $adminId, $action, $description, $oldValues, $newValues)
     *
     * @param string      $resourceType  e.g. 'admin_user', 'announcement', 'ticket'
     * @param mixed       $resourceId    Primary key of the affected resource
     * @param int|null    $adminId       ID of the admin performing the action
     * @param string      $action        One of: create, update, delete, approve, reject, login, logout
     * @param string|null $description   Human-readable change description (stored in changes JSON)
     * @param mixed       $oldValues     Previous state (optional)
     * @param mixed       $newValues     New state (optional)
     * @param string|null $tenantId      Tenant ID when the action targets a specific tenant
     */
    public static function logChange(
        string  $resourceType,
        mixed   $resourceId,
        ?int    $adminId,
        string  $action,
        ?string $description = null,
        mixed   $oldValues = null,
        mixed   $newValues = null,
        ?string $tenantId = null,
    ): static {
        // Normalise the action to the enum values in the migration
        $actionMap = [
            'created' => 'create',
            'updated' => 'update',
            'deleted' => 'delete',
            'approved' => 'approve',
            'rejected' => 'reject',
        ];

        $normalisedAction = $actionMap[$action] ?? $action;

        return static::create([
            'admin_user_id' => $adminId,
            'tenant_id'     => $tenantId,
            'action'        => $normalisedAction,
            'resource_type' => $resourceType,
            'resource_id'   => (string) $resourceId,
            'changes'       => array_filter([
                'description' => $description,
                'old'         => $oldValues,
                'new'         => $newValues,
            ]),
            'ip_address'    => request()?->ip(),
            'user_agent'    => request()?->userAgent(),
        ]);
    }
}
