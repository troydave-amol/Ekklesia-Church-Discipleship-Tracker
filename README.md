# Discipleship Progress Tracker

A multi-tenant SaaS platform enabling churches to track and manage disciple progression through structured training programs with role-based access control, attendance monitoring, and devotional tracking.

---

## Local Setup

1. **Clone and install**
   ```bash
   git clone https://github.com/your-org/church-discipleship-tracker.git
   cd church-discipleship-tracker
   composer install && npm install
   ```

2. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Migrate central database**
   ```bash
   php artisan migrate
   ```

4. **Migrate tenant databases**
   ```bash
   php artisan tenancy:migrate
   ```

5. **Seed database**
   ```bash
   php artisan db:seed
   ```

6. **Start development server**
   ```bash
   npm run dev
   ```

---

## Architecture

| Database | Tables |
|----------|--------|
| **Central** | tenants, domains, cache, migrations |
| **Tenant** | users, church, programs, lessons, attendance, devotionals, roles, permissions, permission_role |

---

## Roles

| Slug | Display Name | Key Permissions |
|------|--------------|-----------------|
| `church_admin` | Church Administrator | manage-users, manage-programs, manage-lessons, manage-subscription, view-reports |
| `program_leader` | Program Leader | manage-programs, manage-lessons, record-attendance |
| `cell_leader` | Cell Leader | record-attendance |
| `disciple` | Disciple | dashboard-view |

---

## Branch Naming

Follow Git Flow conventions:
- `main` — Production (protected)
- `develop` — Integration (protected)
- `feature/{ticket}-{desc}` — Features
- `fix/{ticket}-{desc}` — Bugfixes
- `release/{semver}` — Release prep
- `hotfix/{semver}-{desc}` — Production hotfixes

---

## Running Tests

```bash
php artisan test
```

## Running Tests

```bash
php artisan test
```

---

## Contributing

For detailed contribution guidelines and setup instructions, see:
- [Version Control Workflow](Docs/VERSION-CONTROL-WORKFLOW.md)
- [Migration Strategy](Docs/MIGRATION-STRATEGY.md)
- [Branch Protection Rules](Docs/branch-protection.md)

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
