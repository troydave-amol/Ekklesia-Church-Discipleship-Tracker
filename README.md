# Discipleship Progress Tracker

A multi-tenant SaaS platform enabling churches to track and manage disciple progression through structured training programs (Life Class, School of Leaders SOL 1-3) with role-based workflow, attendance monitoring, and devotional tracking.

**Status**: ~45% complete | **Framework**: Laravel 12 | **Tenancy**: Database-per-tenant (stancl/tenancy)

---

## Quick Start

### Prerequisites
- PHP 8.2+ with extensions: pdo_mysql, mbstring, openssl, curl
- MySQL 8.0+
- Node.js 18+ & npm
- Composer 2.4+

### Installation

```bash
# Clone repository
git clone https://github.com/your-org/church-discipleship-tracker.git
cd church-discipleship-tracker

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup central database
php artisan migrate --database=central

# Seed central data (roles, permissions, subscription plans)
php artisan db:seed --database=central

# Create a test tenant (creates isolated tenant database)
php artisan tinker
# Inside tinker shell:
# Tenant::create(['name' => 'Test Church', 'email' => 'admin@testchurch.com', 'subdomain' => 'testchurch', 'db_name' => 'church_testchurch'])
# Domain::create(['domain' => 'testchurch.localhost', 'tenant_id' => 1])

# Run tenant migrations for new tenant
php artisan tenancy:run migrate --tenants=1

# Seed tenant data
php artisan tenancy:run db:seed --tenants=1

# Run dev server
npm run dev &
php artisan serve
```

Visit: `http://testchurch.localhost:8000`

---

## Tenancy Architecture

### Database Separation

**Central Database** (shared across all tenants)
- `tenants` — Tenant registry
- `domains` — Custom domains & subdomains
- `cache` — Session/cache storage
- `migrations` — Central migration tracking

**Tenant Databases** (isolated per church)
- `users` — Church members with roles
- `church` — Church profile & subscription
- `programs` — Discipleship training programs
- `lessons` — Weekly program content
- `attendance` — Attendance records per user
- `devotionals` — Daily devotional entries
- `church_memberships` — User→Church relationships
- `roles`, `permissions`, `permission_role` — RBAC schema
- `billing_cycles`, `subscription_plans` — Subscription data

### Connection Configuration

All tenant models use:
```php
protected $connection = 'tenant';
```

This is automatically switched to the correct tenant database by stancl/tenancy based on URL/subdomain.

---

## Role Reference

| Role | Display Name | Key Permissions | Scope |
|------|--------------|-----------------|-------|
| `church_admin` | Church Administrator | All operations, subscription mgmt | Entire church |
| `program_leader` | Program Leader | Manage assigned disciples, attendance, devotionals | Assigned disciples only |
| `cell_leader` | Cell Leader | Support leaders, view assigned disciples | Support scope |
| `disciple` | Disciple | View own progress, mark devotionals, view lessons | Own records only |

### Permission Examples
- `attendance.manage` — Record/edit attendance
- `devotional.manage` — Record devotionals
- `disciple.create` — Add new disciples
- `reports.export` — Export data
- `subscription.manage` — Modify subscription plan

---

## Git Workflow & Branching

### Branch Strategy

All branches use **Git Flow** with strict naming conventions:

| Branch Type | Pattern | Example | Purpose |
|-------------|---------|---------|---------|
| **Main** | `main` | — | Production-ready code (protected) |
| **Develop** | `develop` | — | Integration branch (all PRs merge here) |
| **Feature** | `feature/{ticket-id}-{desc}` | `feature/0003-rbac-middleware` | New feature development |
| **Bugfix** | `fix/{ticket-id}-{desc}` | `fix/0004-tenant-scope-leak` | Bug fixes |
| **Release** | `release/{semver}` | `release/1.2.0` | Release preparation |
| **Hotfix** | `hotfix/{semver}-{desc}` | `hotfix/1.1.1-login-redirect` | Production hotfixes |

### Workflow Example

```bash
# Start a new feature
git checkout develop
git pull origin develop
git checkout -b feature/0005-dashboard-metrics

# ... make commits ...
# When ready, push and create Pull Request
git push origin feature/0005-dashboard-metrics

# On GitHub: Create PR → develop
# After review & approval, merge via GitHub (delete branch)

# For releases
git checkout -b release/1.2.0 develop
# Update version, CHANGELOG
git push origin release/1.2.0
# Create PR → main
# After merge to main, merge back to develop
```

### Branch Protection Rules

See `.github/branch-protection.md` for GitHub settings (requires admin setup).

---

## Migration Versioning

### Central Migrations
Standard Laravel migrations in `database/migrations/`:
```bash
php artisan make:migration create_users_table
```

### Tenant Migrations
Tenant-specific migrations in `database/migrations/tenant/`:
```bash
php artisan make:migration --path=database/migrations/tenant create_attendance_table
```

**Naming Convention**: `{timestamp}_tenant_{description}.php`

The migration file must include:
```php
protected $connection = 'tenant';

public function up(): void
{
    // Tenant database only
}
```

Guard against running central migrations in tenant context:
```php
// The tenancy:migration-status command will validate this
php artisan tenancy:migration-status
```

---

## Testing

```bash
# Run all tests
composer test

# Run specific test file
composer test tests/Unit/MigrationCheckerTest.php

# Run with coverage
composer test -- --coverage

# Watch mode
composer test -- --watch
```

---

## Release Process

### Automated Release (recommended)

```bash
./scripts/release.sh 1.2.0
```

This script:
1. ✅ Validates semver format
2. ✅ Moves `[Unreleased]` to CHANGELOG.md with today's date
3. ✅ Creates Git tag `v1.2.0`
4. ✅ Outputs GitHub release draft URL
5. ✅ Generates release notes from CHANGELOG

### Manual Release

1. Update `CHANGELOG.md` — move Unreleased → versioned section
2. Commit: `git commit -m "chore: release 1.2.0"`
3. Tag: `git tag v1.2.0 && git push origin v1.2.0`
4. Merge to main: `git checkout main && git merge release/1.2.0`

---

## Contributing

### Local Development Setup

```bash
# After installation, run migrations + seed
composer run setup

# Start dev server (Laravel, queue, vite)
composer run dev

# Watch logs
php artisan pail
```

### Code Standards

- PHP: PSR-12 (enforced via Laravel Pint)
- Database: Migrations required for schema changes
- Tests: Pest tests required for new features
- Commits: Semantic commit messages (`feat:`, `fix:`, `chore:`, etc.)

```bash
# Format code
./vendor/bin/pint

# Check for issues
./vendor/bin/pint --test
```

---

## Project Docs

- [RBAC Enforcement Guide](./Docs/RBAC%20ENFORCER.md)
- [Dashboard & Metrics Implementation](./Docs/Dashboard%20%26%20Metrics%20Completer.md)
- [Subscription & Feature Gating](./Docs/Subscription%20%26%20Feature%20Gating%20Injector.md)
- [Tenancy Integrity Guard](./Docs/Tenancy%20Integrity%20Guard.md)
- [System Auditor](./Docs/System%20Auditor.md)

---

## Support & Issues

For issues, feature requests, or questions:
1. Check existing [GitHub Issues](https://github.com/your-org/church-discipleship-tracker/issues)
2. Create a new issue with template (bug/feature)
3. For security issues, email: security@yourdomain.com

---

## License

MIT License — see LICENSE file for details

---

## Architecture Docs (For Developers)

- [Codebase Architecture](./Docs/CODEBASE-AWARE%20MASTER%20PROMPT.md)
- [Project Rules & Tenancy](./Rule.md)
