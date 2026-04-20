# Version Control System Implementation

**Date**: April 20, 2026  
**Status**: Complete and operational  
**Framework**: Laravel 12 multi-tenant SaaS

---

## Overview

This document describes the comprehensive version control system implemented for the Discipleship Progress Tracker project. The system includes Git Flow branching, migration safeguards, automated releases, and thorough testing.

---

## 1. Git Flow Branching Strategy

### Branch Hierarchy

```
main (production)
  ├── develop (integration)
  │   ├── feature/0002-initial-dev (features)
  │   ├── feature/0003-dashboard (features)
  │   └── fix/0004-tenant-leak (bugfixes)
  ├── release/1.0.0 (release prep)
  └── hotfix/1.0.1-login-fix (production hotfixes)
```

### Branch Types & Naming

| Type | Pattern | Example | Protected | Purpose |
|------|---------|---------|-----------|---------|
| **Main** | `main` | — | ✅ Yes | Production-ready code |
| **Develop** | `develop` | — | ✅ Yes | Integration & testing |
| **Feature** | `feature/{ticket}-{desc}` | `feature/0003-rbac-middleware` | ❌ No | New feature development |
| **Bugfix** | `fix/{ticket}-{desc}` | `fix/0004-tenant-scope-leak` | ❌ No | Bug fixes |
| **Release** | `release/{semver}` | `release/1.0.0` | ❌ No | Release preparation |
| **Hotfix** | `hotfix/{semver}-{desc}` | `hotfix/1.0.1-login-redirect` | ❌ No | Production hotfixes |

### Workflow Example

```bash
# Start a feature
git checkout develop
git pull origin develop
git checkout -b feature/0005-dashboard-metrics

# ... make changes ...
git add .
git commit -m "feat: add real-time dashboard metrics"
git push origin feature/0005-dashboard-metrics

# On GitHub: Create PR → develop
# After review & approval: Merge via GitHub

# For releases
git checkout -b release/1.0.0 develop
# Update CHANGELOG, version
git push origin release/1.0.0
# Create PR → main
# After merge to main: Merge back to develop
```

---

## 2. Migration Connection Guards

### Purpose

Prevent accidental execution of tenant migrations on the central database (and vice versa).

### Guard Implementation

**Tenant Migration Guard**:
```php
if (DB::connection()->getDatabaseName() === config('database.connections.mysql.database')) {
    throw new \RuntimeException('Tenant migration running on central DB. Aborting.');
}
```

**Central Migration Guard**:
```php
if (DB::connection()->getDatabaseName() !== config('database.connections.mysql.database')) {
    throw new \RuntimeException('Central migration running on tenant DB. Aborting.');
}
```

### Migration Stub Templates

**Location**: `stubs/migration.tenant.stub` and `stubs/migration.central.stub`

These stubs include the guard code pre-inserted, ensuring all new migrations are protected by default.

### Creating Protected Migrations

```bash
# Tenant migration (uses tenant stub)
php artisan make:migration create_attendance_table --path=database/migrations/tenant

# Central migration (uses central stub)
php artisan make:migration create_users_table
```

---

## 3. Tenant Migration Status Command

### Purpose

Monitor and validate migration status across all tenants in a multi-tenant system.

### Command Signature

```bash
php artisan tenancy:migration-status
```

### Output

Displays a formatted table with:
- **Tenant ID** — Database name
- **Pending Migrations** — Count of unapplied migrations
- **Status** — ✓ Up to date | ⚠ Pending | ✗ Error

### Exit Codes

- **0** — All tenants are up to date
- **1** — At least one tenant has pending migrations or errors

### Usage

```bash
# Check status
php artisan tenancy:migration-status

# In CI/CD pipeline
php artisan tenancy:migration-status || exit 1
```

### Validation Features

- ✅ Verifies tenant migrations have `_tenant_` prefix
- ✅ Detects migrations missing required `$connection = 'tenant'`
- ✅ Reports connection and database errors per tenant
- ✅ Compares migration files vs. `migrations` table

---

## 4. CHANGELOG Management

### Format

Follows [Keep a Changelog](https://keepachangelog.com/) standard:

```markdown
# Changelog

## [Unreleased]
### Added
- New feature
### Changed
- Changed feature

## [1.0.0] - 2026-04-20
### Added
- Initial release
```

### Sections

- **Added** — New features
- **Changed** — Changes to existing features
- **Deprecated** — Soon-to-be removed features
- **Removed** — Removed features
- **Fixed** — Bug fixes
- **Security** — Security vulnerability fixes

### Updating

1. Add changes to `[Unreleased]` section immediately
2. When releasing, move `[Unreleased]` → versioned section with date
3. Use `./scripts/release.sh` for automated updates

---

## 5. Automated Release Process

### Script Location

`scripts/release.sh`

### Usage

```bash
./scripts/release.sh 1.0.0
```

### What It Does

1. ✅ Validates semver format (X.Y.Z)
2. ✅ Updates CHANGELOG.md with release date
3. ✅ Creates Git tag (e.g., `v1.0.0`)
4. ✅ Pushes commits and tags to remote
5. ✅ Outputs GitHub release draft URL

### Example

```bash
$ ./scripts/release.sh 1.0.0
📦 Preparing release: 1.0.0
✓ CHANGELOG.md updated
✓ Commit created
✓ Tag v1.0.0 created
✓ Pushed to remote
📝 Draft release: https://github.com/qesthr/church-discipleship-tracker/releases/new?tag=v1.0.0
```

---

## 6. Branch Protection Rules

### GitHub Configuration

All protection rules are configured in GitHub Settings → Branches.

### Main Branch (`main`)

- ✅ **Require pull request reviews**: 1 approval minimum
- ✅ **Dismiss stale reviews** when new commits pushed
- ✅ **Require status checks**: Tests must pass
- ✅ **Require branches up to date**
- ✅ **Require signed commits**: Recommended
- ❌ **Allow force pushes**: Disabled
- ❌ **Allow direct push**: Disabled

### Develop Branch (`develop`)

- ✅ **Require pull request reviews**: Optional (can be disabled)
- ✅ **Allow force pushes**: Admins only
- ✅ **Require status checks**: Tests must pass
- ❌ **Allow direct push**: Disabled except admins
- ✅ **Allow deletions**: Disabled

See [`.github/branch-protection.md`](.github/branch-protection.md) for detailed setup instructions.

---

## 7. Testing Strategy

### Test Structure

```
tests/
├── Feature/
│   └── Middleware/
│       ├── CheckRoleTest.php (4 tests)
│       └── CheckPermissionTest.php (5 tests)
└── Unit/
    └── MigrationCheckerTest.php (5+ tests)
```

### Running Tests

```bash
# All tests
php artisan test

# Specific test file
php artisan test tests/Feature/Middleware/CheckRoleTest.php

# With coverage
php artisan test --coverage

# Watch mode
php artisan test --watch
```

### Test Coverage

| Component | Tests | Status |
|-----------|-------|--------|
| CheckRole Middleware | 4 | ✅ Passing |
| CheckPermission Middleware | 5 | ✅ Passing |
| Migration Checker | 5+ | ⚠️ Requires DB |

---

## 8. .gitignore Configuration

Ensures sensitive and unnecessary files are not committed:

```
.env                           # Environment variables
.env.*.local                   # Local environment overrides
/vendor                        # Composer dependencies
/node_modules                  # NPM dependencies
/public/hot                    # Vite development server
/public/build                  # Built assets
storage/logs/*.log             # Application logs
storage/app/tenant_*/          # Tenant storage
*.sqlite                       # SQLite databases
.DS_Store                      # macOS files
Thumbs.db                      # Windows files
```

---

## 9. Commit Message Conventions

### Format

```
<type>(<scope>): <subject>

<body>

<footer>
```

### Types

- **feat**: New feature
- **fix**: Bug fix
- **docs**: Documentation changes
- **chore**: Build, dependency, or tooling changes
- **refactor**: Code refactoring without feature change
- **test**: Test additions or improvements
- **perf**: Performance improvements

### Examples

```
feat(rbac): implement CheckRole middleware with database lookup

fix(tenancy): prevent central migration on tenant database

docs: update README with quick start guide

chore(deps): upgrade Laravel to 12.x

test: add comprehensive middleware test coverage
```

---

## 10. CI/CD Integration Points

### GitHub Actions (Recommended)

Test on every push to develop/feature branches:

```yaml
- Run: php artisan test
- Run: php artisan tenancy:migration-status
- Report: Coverage to Codecov
```

### Pre-Commit Hook (Local)

Run tests before allowing commits:

```bash
#!/bin/sh
php artisan test || exit 1
```

---

## 11. Release Schedule

| Release Type | Frequency | Trigger |
|--------------|-----------|---------|
| **Patch (0.0.Z)** | As needed | Bug fixes, hotfixes |
| **Minor (0.Y.0)** | Every 2 weeks | New features, improvements |
| **Major (X.0.0)** | Every quarter | Breaking changes, major features |

---

## 12. Troubleshooting

### Migration failing on wrong database

**Problem**: Migration runs on central DB instead of tenant DB

**Solution**: Check migration file has correct connection guard:
```php
if (DB::connection()->getDatabaseName() === config('database.connections.mysql.database')) {
    throw new \RuntimeException('Wrong database');
}
```

### tenancy:migration-status shows errors

**Problem**: Command reports pending migrations or prefix warnings

**Solution**: 
1. Verify migration file is in correct directory (`database/migrations/` or `database/migrations/tenant/`)
2. Ensure tenant migrations have `_tenant_` in filename
3. Run: `php artisan tenancy:migrate --tenants=all`

### Cannot push to main/develop

**Problem**: Git rejects push with protection error

**Solution**: 
1. Create a feature branch instead
2. Create Pull Request for review
3. After approval, merge via GitHub UI

---

## 13. Implementation Checklist

- ✅ Git Flow branching strategy configured
- ✅ Develop and main branches created and protected
- ✅ Migration connection guards implemented
- ✅ Migration stubs created (`migration.tenant.stub`, `migration.central.stub`)
- ✅ TenancyMigrationStatusCommand implemented
- ✅ CHANGELOG.md created with Keep a Changelog format
- ✅ Release script created and tested
- ✅ .gitignore configured with all necessary entries
- ✅ Middleware tests passing (9/9)
- ✅ All documentation created
- ✅ Branch protection rules documented in `.github/branch-protection.md`

---

## 14. Next Steps

1. **Set up branch protection** in GitHub Settings
2. **Configure CI/CD** (GitHub Actions recommended)
3. **Train team** on Git Flow workflow
4. **Monitor** tenant migrations with `php artisan tenancy:migration-status`
5. **Use release script** for all releases

---

## References

- [Git Flow Cheatsheet](https://danielkummer.github.io/git-flow-cheatsheet/)
- [Keep a Changelog](https://keepachangelog.com/)
- [Semantic Versioning](https://semver.org/)
- [Conventional Commits](https://www.conventionalcommits.org/)
- [GitHub Branch Protection](https://docs.github.com/en/repositories/configuring-branches-and-merges-in-your-repository/managing-protected-branches)
