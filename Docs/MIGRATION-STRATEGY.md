# Migration Versioning Strategy

This document outlines how to manage database migrations across the multi-tenant architecture of the Discipleship Progress Tracker.

---

## Architecture Overview

The project uses **database-per-tenant multi-tenancy** with two distinct migration paths:

### Central Database
- Shared across all tenants
- Tracks: tenants, domains, sessions, cache
- Location: `database/migrations/` (standard Laravel)
- Connection: `central` (via `config/database.php`)

### Tenant Databases
- Isolated per church
- Tracks: users, programs, lessons, attendance, devotionals, RBAC, etc.
- Location: `database/migrations/tenant/`
- Connection: `tenant` (dynamically switched per request by stancl/tenancy)

---

## Central Database Migrations

### Creation

Use standard Laravel migration commands:

```bash
php artisan make:migration create_users_table
php artisan make:migration add_status_to_tenants --table=tenants
```

Files are created in: `database/migrations/`

### Naming Convention

```
{timestamp}_{description}.php

Example:
2026_04_20_120000_create_users_table.php
2026_04_20_120015_add_subscription_to_tenants.php
```

### Requirements

**Must specify connection**:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'central';

    public function up(): void
    {
        Schema::connection('central')->create('tenants', function (Blueprint $table) {
            // ...
        });
    }

    public function down(): void
    {
        Schema::connection('central')->dropIfExists('tenants');
    }
};
```

### Execution

```bash
# Run central migrations only
php artisan migrate --database=central

# Rollback one batch
php artisan migrate:rollback --database=central
```

### Protection Guard

The framework automatically validates:
- ❌ Fails if migration without `$connection = 'central'` is run on central DB
- ❌ Fails if central migration is applied to tenant DB
- ✅ Central-only changes are isolated from tenant databases

---

## Tenant Database Migrations

### Creation

Create migrations in the `database/migrations/tenant/` directory:

```bash
php artisan make:migration --path=database/migrations/tenant create_attendance_table
```

### Naming Convention

**REQUIRED prefix**: `tenant_{description}`

```
{timestamp}_tenant_{description}.php

Examples:
2026_04_20_130000_tenant_create_attendance_table.php
2026_04_20_130015_tenant_add_completion_to_lessons.php
2026_04_21_090000_tenant_create_devotional_index.php
```

### Requirements

**Must specify connection**:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'tenant';

    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('attendance_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
```

### Execution

```bash
# Run migrations for all active tenants
php artisan tenancy:run migrate --tenants=all

# Run for specific tenant(s)
php artisan tenancy:run migrate --tenants=1,2,3

# Rollback (all tenants)
php artisan tenancy:run migrate:rollback --tenants=all
```

### Protection Guard

The framework validates:
- ❌ Fails if tenant migration lacks `$connection = 'tenant'`
- ❌ Fails if tenant migration is run on central DB
- ❌ Fails if `_tenant_` prefix is missing (enforcement in checker command)
- ✅ Tenant-only changes are isolated per church

---

## Migration Status Tracking

### Built-in Laravel Tracking

Laravel's `migrations` table tracks executed migrations per database:

```
Central DB:
- migrations table stores applied central migrations

Each Tenant DB:
- migrations table stores applied tenant migrations
```

### Checking Status

```bash
# Central migrations
php artisan migrate:status --database=central

# View all tenant migration statuses
php artisan tenancy:migration-status
```

Expected output:
```
╔════════════════════════════════╦════════════════════╦════════════════════╗
║ Tenant (db_name)               ║ Pending Migrations ║ Migration Status   ║
╠════════════════════════════════╬════════════════════╬════════════════════╣
║ church_firstbaptist            ║ 0                  ║ ✓ Up to date       ║
║ church_gracecommunity          ║ 0                  ║ ✓ Up to date       ║
║ church_hopevalley              ║ 1                  ║ ⚠ Pending          ║
╚════════════════════════════════╩════════════════════╩════════════════════╝
```

---

## Common Workflows

### Adding a New Feature (with DB Changes)

1. **Create migration** for central (if needed):
   ```bash
   php artisan make:migration add_feature_flag_to_tenants --table=tenants
   ```

2. **Create migration** for tenant:
   ```bash
   php artisan make:migration --path=database/migrations/tenant add_feature_data_to_users
   ```
   (Automatically prefixed in the command)

3. **Add `protected $connection`** to both migrations

4. **Test locally**:
   ```bash
   # Central
   php artisan migrate --database=central

   # Tenant (test)
   php artisan tenancy:run migrate --tenants=1
   ```

5. **Commit**:
   ```bash
   git add database/migrations/
   git commit -m "feat: add feature data schema"
   ```

6. **On production**:
   ```bash
   # Central first
   php artisan migrate --database=central

   # Then all tenants
   php artisan tenancy:run migrate --tenants=all
   ```

### Reverting a Broken Migration

```bash
# Central
php artisan migrate:rollback --database=central --step=1

# Tenants
php artisan tenancy:run migrate:rollback --tenants=all --step=1
```

### Checking Migration Status Before Deployment

```bash
# Check central
php artisan migrate:status --database=central

# Check all tenants
php artisan tenancy:migration-status
```

---

## CI/CD Considerations

### Pre-Deployment Checks

```bash
#!/bin/bash
# Deploy script

echo "Checking migration status..."

# Fail if pending migrations exist
php artisan migrate:status --database=central | grep -q "No migrations to run"
if [ $? -ne 0 ]; then
    echo "❌ Pending central migrations. Run migration first."
    exit 1
fi

php artisan tenancy:migration-status | grep -q "Up to date"
if [ $? -ne 0 ]; then
    echo "❌ Pending tenant migrations. Run migration first."
    exit 1
fi

echo "✓ All migrations applied"
```

### Post-Deployment Validation

```bash
# After deployment, verify migrations
php artisan migrate:status --database=central
php artisan tenancy:migration-status
```

---

## Naming Conventions at a Glance

| Scope | Location | Pattern | Example |
|-------|----------|---------|---------|
| **Central** | `database/migrations/` | `{timestamp}_{desc}.php` | `2026_04_20_120000_create_users_table.php` |
| **Tenant** | `database/migrations/tenant/` | `{timestamp}_tenant_{desc}.php` | `2026_04_20_130000_tenant_create_attendance_table.php` |

---

## Troubleshooting

### Migration Won't Run: "Unknown Connection"

**Problem**: Specified connection doesn't exist in `config/database.php`

**Solution**:
```php
// Check config/database.php
'central' => [ /* config */ ],
'tenant' => [ /* config */ ],
```

### Tenant Migration Applied to Central DB

**Problem**: Migration executed on wrong connection

**Solution**: Verify `protected $connection = 'tenant'` is set in migration file

### Migration Stuck / Incomplete

**Problem**: Migration partially executed (table created but failed at index)

**Solution**:
```bash
# Manually check table
php artisan tinker
>>> Schema::connection('tenant')->getColumns('table_name')

# Remove partial migration from migrations table
>>> DB::table('migrations')->where('migration', 'like', '%tenant_name%')->delete()

# Re-run after fixing migration file
php artisan tenancy:run migrate --tenants=1
```

---

## Best Practices

1. **Always specify `$connection`** in migration files
2. **Prefix tenant migrations** with `_tenant_` in filename
3. **Test both paths**: Run central → Run tenant
4. **Keep migrations small** — one feature per migration
5. **Document schema changes** in commit message
6. **Review migrations** before merging (especially central changes)
7. **Back up production** before running migrations

---

## References

- [Laravel Migrations](https://laravel.com/docs/migrations)
- [stancl/tenancy Documentation](https://tenancyforlaravel.com/docs/v3)
