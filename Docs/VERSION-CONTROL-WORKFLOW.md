# Version Control & Release Workflow Guide

This guide summarizes the complete version control workflow for the Discipleship Progress Tracker team.

---

## Quick Reference

### Files Created/Updated

| File | Purpose |
|------|---------|
| **README.md** | ✅ Resolved merge conflict; comprehensive project documentation |
| **.github/branch-protection.md** | ✅ GitHub branch protection settings (requires admin setup) |
| **CHANGELOG.md** | ✅ Keep a Changelog format; tracks Unreleased → versioned sections |
| **docs/MIGRATION-STRATEGY.md** | ✅ Comprehensive guide for central vs. tenant migrations |
| **scripts/release.sh** | ✅ POSIX-compliant automated release script |
| **app/Console/Commands/TenancyMigrationStatusCommand.php** | ✅ Artisan command: `tenancy:migration-status` |
| **tests/Unit/MigrationCheckerTest.php** | ✅ Pest tests for migration validation (5 test cases) |
| **.gitignore** | ✅ Updated with tenant-specific ignores |

---

## Branching Strategy (Git Flow)

```
main (production)
 ↑
 └─ release/* (staging)
 ↑
develop (integration)
 ↑
 ├─ feature/* (features)
 ├─ fix/* (bugfixes)
 └─ hotfix/* (production fixes)
```

### Branch Naming Conventions

| Type | Pattern | Example |
|------|---------|---------|
| Feature | `feature/{ticket-id}-{desc}` | `feature/0005-dashboard-metrics` |
| Bugfix | `fix/{ticket-id}-{desc}` | `fix/0004-tenant-scope-leak` |
| Release | `release/{semver}` | `release/1.2.0` |
| Hotfix | `hotfix/{semver}-{desc}` | `hotfix/1.1.1-login-redirect` |

### Workflow

```bash
# Start feature
git checkout develop
git pull origin develop
git checkout -b feature/0005-dashboard-metrics

# Make commits
git add .
git commit -m "feat: add pastor dashboard metrics"

# Push and create PR
git push origin feature/0005-dashboard-metrics
# → Create PR on GitHub (develop ← feature/0005-dashboard-metrics)

# After approval & merge:
# → Branch is deleted on GitHub
git checkout develop && git pull origin develop

# For releases
git checkout -b release/1.2.0 develop
# (Update version numbers if needed)
git push origin release/1.2.0
# → Create PR on GitHub (main ← release/1.2.0)
# → After merge, merge main back to develop
```

---

## Migration Versioning

### Central Database
- **Location**: `database/migrations/`
- **Pattern**: `{timestamp}_{description}.php`
- **Connection**: `protected $connection = 'central';`
- **Command**: `php artisan migrate --database=central`

### Tenant Database
- **Location**: `database/migrations/tenant/`
- **Pattern**: `{timestamp}_tenant_{description}.php` (REQUIRED `_tenant_` prefix)
- **Connection**: `protected $connection = 'tenant';`
- **Command**: `php artisan tenancy:run migrate --tenants=all`

### Create Migrations

```bash
# Central migration
php artisan make:migration create_users_table

# Tenant migration (auto-prefixed)
php artisan make:migration --path=database/migrations/tenant create_attendance_table
```

### Check Status

```bash
# Central DB
php artisan migrate:status --database=central

# All tenants
php artisan tenancy:migration-status
# Output:
# ╔════════════════════════════════╦════════════════════╦════════════════════╗
# ║ Tenant (db_name)               ║ Pending Migrations ║ Migration Status   ║
# ╠════════════════════════════════╬════════════════════╬════════════════════╣
# ║ church_firstbaptist            ║ 0                  ║ ✓ Up to date       ║
# ║ church_gracecommunity          ║ 0                  ║ ✓ Up to date       ║
# ║ church_hopevalley              ║ 1                  ║ ⚠ Pending          ║
# ╚════════════════════════════════╩════════════════════╩════════════════════╝
```

### Validate Migrations

The `tenancy:migration-status` command automatically:
- ✅ Checks for `_tenant_` prefix in filenames
- ✅ Validates `$connection = 'tenant'` is present
- ✅ Reports pending migrations per tenant
- ✅ Fails (exit 1) if issues found

---

## Release Process

### Automated Release (Recommended)

```bash
./scripts/release.sh 1.2.0
```

The script:
1. ✅ Validates semver format (X.Y.Z)
2. ✅ Updates CHANGELOG.md (moves Unreleased → versioned section)
3. ✅ Creates Git commit: `chore: release 1.2.0`
4. ✅ Creates Git tag: `v1.2.0`
5. ✅ Pushes tag to origin
6. ✅ Outputs GitHub release draft URL

### Manual Release

```bash
# 1. Update CHANGELOG.md manually
# 2. Commit
git commit -m "chore: release 1.2.0"
# 3. Tag
git tag v1.2.0
git push origin v1.2.0
# 4. Merge to main (via PR)
```

### GitHub Release Draft

After running `release.sh`, visit the generated URL to:
1. Review CHANGELOG entries
2. Add release notes
3. Publish release

---

## Changelog Management

### Format

Follows [Keep a Changelog](https://keepachangelog.com/):

```markdown
## [Unreleased]

### Added
- New feature description

### Changed
- Changed behavior description

### Fixed
- Bug fix description

## [1.1.0] - 2026-04-15

### Added
- Previous release additions

### Fixed
- Previous release fixes
```

### During Development

Add items to `[Unreleased]` as PRs are merged:

```markdown
## [Unreleased]

### Added
- Feature 1 (#123)
- Feature 2 (#125)

### Fixed
- Bug fix (#124)
```

### At Release Time

```bash
./scripts/release.sh 1.2.0
```

This moves `[Unreleased]` → `[1.2.0] - 2026-04-20` and creates a new `[Unreleased]` section.

---

## Git Configuration for Team

### Setup Local Hooks (Optional)

Create `.git/hooks/pre-push` to validate before push:

```bash
#!/bin/sh
# Validate branch name before push
BRANCH=$(git rev-parse --abbrev-ref HEAD)

if [ "$BRANCH" != "main" ] && [ "$BRANCH" != "develop" ]; then
    if ! echo "$BRANCH" | grep -qE '^(feature|fix|release|hotfix)/'; then
        echo "❌ Invalid branch name: $BRANCH"
        echo "Use: feature/*, fix/*, release/*, hotfix/*"
        exit 1
    fi
fi

exit 0
```

Make executable: `chmod +x .git/hooks/pre-push`

---

## CI/CD Integration Points

The following should be checked before merge:

1. ✅ **Tests Pass**: `composer test` (Pest)
2. ✅ **Code Style**: `./vendor/bin/pint --test` (Laravel Pint)
3. ✅ **Migrations Valid**: `php artisan tenancy:migration-status` (exit code 0)
4. ✅ **No Merge Conflicts**: GitHub checks

Example GitHub Actions workflow:

```yaml
name: Tests
on: [pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - uses: shivammathur/setup-php@v2
        with: { php-version: '8.2' }
      - run: composer install
      - run: ./vendor/bin/pint --test
      - run: php artisan test
      - run: php artisan tenancy:migration-status
```

---

## Testing the Workflow

### Test Migration Checker

```bash
# Run tests
php artisan test tests/Unit/MigrationCheckerTest.php

# Or with Pest directly
./vendor/bin/pest tests/Unit/MigrationCheckerTest.php
```

### Test Release Script

```bash
# Dry run (no actual git operations)
bash -x ./scripts/release.sh 0.0.1-test

# Verify CHANGELOG was updated
git diff CHANGELOG.md

# Revert test
git reset --hard HEAD~1
```

---

## Best Practices

### Commits
- Use semantic messages: `feat:`, `fix:`, `chore:`, `docs:`, `test:`
- Keep commits focused (one feature per commit)
- Reference ticket IDs: `feat: add dashboard (#0123)`

### Pull Requests
- Limit to one feature/fix per PR
- Add description & screenshot (for UI changes)
- Request review before merging
- Use squash & merge for features (keep history clean)

### Releases
- Tag every release: `v1.2.0`
- Update CHANGELOG before release
- Test release branch thoroughly before merging to main

### Migrations
- Test both central and tenant migrations locally
- Run `tenancy:migration-status` before committing
- Always specify `$connection` in migration files
- Use `_tenant_` prefix for tenant migrations

---

## Troubleshooting

### "Invalid branch name"
```bash
git branch -m feature/fix-name
```

### "Migrations validation failed"
```bash
# Check what's wrong
php artisan tenancy:migration-status

# Fix migration files, then rerun
php artisan tenancy:run migrate --tenants=all
```

### "Release script failed"
```bash
# Ensure CHANGELOG.md exists
touch CHANGELOG.md

# Ensure git is in clean state
git status

# Try again
./scripts/release.sh 1.2.0
```

---

## Resources

- [README.md](../README.md) — Project overview & setup
- [.github/branch-protection.md](../.github/branch-protection.md) — GitHub settings
- [docs/MIGRATION-STRATEGY.md](../docs/MIGRATION-STRATEGY.md) — Migration deep-dive
- [CHANGELOG.md](../CHANGELOG.md) — Release history
- [scripts/release.sh](../scripts/release.sh) — Release automation
- [Git Flow Cheatsheet](https://danielkummer.github.io/git-flow-cheatsheet/)
- [Keep a Changelog](https://keepachangelog.com/)
- [Semantic Versioning](https://semver.org/)

