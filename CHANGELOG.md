# Changelog

All notable changes to the **Discipleship Progress Tracker** project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### Added
- Multi-tenant SaaS architecture with stancl/tenancy (database-per-tenant)
- User authentication with role-based access control (RBAC)
- Four core roles: Church Admin, Program Leader, Cell Leader, Disciple
- Attendance tracking system
- Devotional tracking system
- Program management (Life Class, SOL 1-3)
- Weekly lesson upload and management
- User profile management
- Church subscription plan support
- Basic dashboard views
- Report export functionality (partial)

### Changed
- Resolved README merge conflict
- Documented branch naming convention and Git workflow
- Established migration versioning strategy for central and tenant databases

### Fixed
- README.md now reflects project identity and setup instructions

### Removed
- Default Laravel boilerplate content from README

---

## Versioning Strategy

### When to Release

- **Major (X.0.0)**: Breaking changes (API, database schema changes, role changes)
- **Minor (0.Y.0)**: New features (backward compatible)
- **Patch (0.0.Z)**: Bug fixes and small improvements

### Release Frequency

- **Every 2 weeks**: Minor updates and bug fixes
- **Every quarter**: Major features
- **As needed**: Hotfixes for production issues

### Example Release Sections

```markdown
## [1.2.0] - 2026-04-25

### Added
- Dashboard metrics for pastors (disciples, leaders, completion rates)
- Bulk user import from CSV
- Export reports to Excel

### Fixed
- RBAC middleware not respecting role_id (now enforces role_id-based checks)
- Leader scoping leak (leaders could see all disciples, not just assigned)

### Changed
- Migration naming convention now requires `_tenant_` prefix for tenant migrations
```

---

## Maintenance Notes

- **Keep Unreleased updated**: Add items to Unreleased as PRs are merged
- **Use release.sh**: Automate changelog updates during releases
- **Date format**: YYYY-MM-DD (ISO 8601)
- **Review before release**: Ensure all items in Unreleased reflect actual changes

For release automation, see [scripts/release.sh](./scripts/release.sh)
