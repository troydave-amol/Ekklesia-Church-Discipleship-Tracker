# GitHub Branch Protection Rules

This document defines the branch protection strategy for the **Discipleship Progress Tracker** repository. These settings must be configured by repository administrators in GitHub Settings.

---

## Overview

The project uses **Git Flow** with two main protected branches:
- **`main`** — production-ready code, must be stable and tested
- **`develop`** — integration branch, where all features converge before release

All other branches (`feature/*`, `fix/*`, `release/*`, `hotfix/*`) are temporary and may be deleted after merge.

---

## Branch Protection Rule: `main` (Production)

### Settings to Enable in GitHub

**Path Pattern**: `main`

#### 1. Require Pull Request Reviews
- ✅ **Require pull request reviews before merging**: ON
- ✅ **Dismiss stale pull request approvals when new commits are pushed**: ON
- ✅ **Require review from Code Owners**: ON (if `CODEOWNERS` file exists)
- **Number of required approvals**: `1` (minimum; adjust for larger team)

#### 2. Require Status Checks to Pass
- ✅ **Require status checks to pass before merging**: ON
- ✅ **Require branches to be up to date before merging**: ON
- **Status checks that must pass**:
  - `phpunit` (or test runner)
  - `lint` (PHP code style)
  - `security-scan` (if available)

#### 3. Require Branches to Be Up To Date
- ✅ **Require branches to be up to date before merging**: ON

#### 4. Restrict Who Can Push & Merge
- ✅ **Restrict who can push to matching branches**: ON
  - **Allow specified actors to bypass required pull requests**: `@church-ops`, `@lead-dev`
  - (Adjust as needed; this allows emergency hotfixes)
- ✅ **Allow force pushes**: OFF
- ✅ **Allow deletions**: OFF

#### 5. Require Commit Signature Verification
- ⚠️ **Require signed commits**: ON (optional, if team uses GPG)

---

## Branch Protection Rule: `develop` (Integration)

### Settings to Enable in GitHub

**Path Pattern**: `develop`

#### 1. Require Pull Request Reviews
- ✅ **Require pull request reviews before merging**: ON
- ✅ **Dismiss stale pull request approvals when new commits are pushed**: ON
- **Number of required approvals**: `1`

#### 2. Require Status Checks to Pass
- ✅ **Require status checks to pass before merging**: ON
- ✅ **Require branches to be up to date before merging**: ON
- **Status checks that must pass**:
  - `phpunit`
  - `lint`

#### 3. Restrict Who Can Push
- ✅ **Restrict who can push to matching branches**: OFF (allows feature branch developers to push)
- ✅ **Allow force pushes**: OFF
- ✅ **Allow deletions**: OFF

---

## Branch Protection Rule: `release/*` (Optional)

### Settings to Enable in GitHub

**Path Pattern**: `release/*`

This rule is less strict than `main` but still requires review.

#### 1. Require Pull Request Reviews
- ✅ **Require pull request reviews before merging**: ON
- **Number of required approvals**: `1`

#### 2. Require Status Checks
- ✅ **Require status checks to pass before merging**: ON

#### 3. Allow Force Push for Release Prep
- ⚠️ **Allow force pushes**: ON (only for version bumps and changelog)
  - **Restrict force push access to**: `@lead-dev`

---

## Branch Protection Rule: `hotfix/*` (Optional)

### Settings to Enable in GitHub

**Path Pattern**: `hotfix/*`

Hotfix branches have higher bypass permissions for emergencies.

#### 1. Require Pull Request Reviews
- ✅ **Require pull request reviews before merging**: ON (but allow bypass)
- **Number of required approvals**: `1`

#### 2. Bypass Restrictions
- ✅ **Allow specified actors to bypass required pull requests**: ON
  - **Allowed actors**: `@lead-dev` (for critical production fixes)

---

## Dismissing Pull Request Reviews

When stale PR reviews are dismissed:
- Any new commits automatically dismiss old reviews
- Reviewers must re-approve after code changes
- This prevents approvals from becoming outdated

---

## CODEOWNERS File (Optional)

If enforcing code ownership, create `.github/CODEOWNERS`:

```
# Core infrastructure
/app/Providers/              @lead-dev
/config/tenancy.php          @lead-dev
/database/migrations/        @lead-dev

# RBAC and Security
/app/Policies/               @rbac-team
/app/Http/Middleware/        @rbac-team

# Features
/app/Http/Controllers/       @team
/app/Services/               @team

# Tests
/tests/                      @qa-team
```

---

## Merging Strategy

### Preferred: Squash & Merge (for feature branches)

Keeps `main` history clean by squashing feature commits into one.

```bash
# GitHub UI: Use "Squash and merge" option
```

### For Releases: Create Merge Commit

Preserves full history of release-related changes.

```bash
# GitHub UI: Use "Create a merge commit" option
```

---

## Emergency Bypass Procedures

### Hotfix to Production (Critical Bug)

1. Create `hotfix/semver-description` from `main`
2. Make minimal fix
3. **If review is blocked**: Use "Bypass branch protections" (only for critical issues)
4. Merge to `main` with force-push approval
5. Create PR back to `develop` immediately after

---

## Automated Actions (GitHub Actions)

For CI/CD integration, ensure these status checks are enabled:

### Example: Laravel Test Workflow

```yaml
name: Tests
on: [pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      - name: Install dependencies
        run: composer install
      - name: Run tests
        run: php artisan test
```

---

## Troubleshooting

### "Required status checks are failing"
- Review CI logs in the PR
- Fix failing tests locally: `composer test`
- Push updated code

### "Waiting for required reviews"
- Assign reviewers in the PR
- Follow up in team chat/Slack

### "Branch is out of date with base branch"
- Click "Update branch" in PR UI, or
- Run locally: `git pull origin develop && git push`

---

## Review Schedule

- **Monthly**: Review branch protection settings for effectiveness
- **Quarterly**: Assess merge conflicts and workflow friction
- **Annually**: Update CODEOWNERS and approver list as team changes

