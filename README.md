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

## Central Admin Dashboard Setup

After initial setup, you need to create a super admin user to access the central control plane at `/admin/login`.

### Creating Your First Admin User

**Option 1: Using Artisan Command (Recommended)**
```bash
php artisan app:create-admin
```
This will prompt you for:
- Admin name
- Admin email
- Admin password

**Option 2: Using Flags**
```bash
php artisan app:create-admin --name="John Doe" --email="admin@church.local" --password="secure-password"
```

**Option 3: Seeding (Fresh Installation)**
```bash
php artisan db:seed --class=AdminSeeder
```
This creates a default super admin:
- Email: `admin@church.local`
- Password: `admin@123` (⚠️ Change immediately!)

### Accessing the Admin Dashboard

1. Navigate to: `http://your-domain.local/admin/login`
2. Log in with your admin credentials
3. You'll see the central control plane with:
   - Tenant management
   - Support ticket handling
   - System announcements
   - Admin user management

### Initial Configuration Tasks

After logging in:
1. ✅ Create additional admin users (Admin Staff section)
2. ✅ Approve pending tenant signups (Approvals section)
3. ✅ Configure system announcements (if needed)
4. ✅ Monitor support tickets

---

## Asset Pipeline

The project uses **Vite** for asset bundling. CSS is split per-page for optimization:

| File | Usage | Rebuilt |
|------|-------|---------|
| `auth.css` | Admin & tenant login pages | `npm run dev` |
| `admin.css` | Central admin dashboard | `npm run dev` |
| `app.css` | Tenant application (empty) | `npm run dev` |
| `base.css` | Shared utility styles | `npm run dev` |

To rebuild assets:
```bash
npm run dev    # Development (watch mode)
npm run build  # Production (minified)
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

## Troubleshooting

### Admin Login Issues

**Problem: 404 Error at `/admin/login`**
- ✅ Verify routes: `php artisan route:list | grep admin`
- ✅ Clear cache: `php artisan route:cache && php artisan config:cache`
- ✅ Check `.env` for correct `APP_URL`

**Problem: Login fails with valid credentials**
- ✅ Verify admin user exists: `php artisan tinker` then `AdminUser::active()->first()`
- ✅ Check default admin created: `php artisan db:seed --class=AdminSeeder`
- ✅ Verify password is hashed: `AdminUser::where('email', 'admin@church.local')->first()->password`

**Problem: Login page has no CSS styling**
- ✅ Rebuild assets: `npm run dev` (wait for compilation)
- ✅ Check Vite manifest: `ls -la public/build/manifest.json`
- ✅ Clear browser cache: Ctrl+Shift+Delete (Chrome) or Cmd+Shift+Delete (Safari)
- ✅ Verify `auth.css` is included: Check Vite config for `resources/css/auth.css`

### Asset Pipeline Issues

**Problem: Styles not appearing on dashboard**
- ✅ Verify admin.css is built: `npm run dev`
- ✅ Check assets in public/build folder: `ls public/build/assets/`
- ✅ Inspect network tab in browser DevTools (check for 404 on CSS files)

**Problem: JavaScript not working**
- ✅ Verify Vite is running: Check terminal for `http://localhost:5173`
- ✅ Restart Vite: Stop `npm run dev`, then start again
- ✅ Clear node_modules: `rm -rf node_modules && npm install`

### Database Issues

**Problem: Admin user creation fails**
- ✅ Verify database connection: `php artisan db:show`
- ✅ Check migration status: `php artisan migrate:status`
- ✅ Verify table exists: `php artisan tinker` then `DB::table('admin_users')->count()`

---

## Architecture Docs (For Developers)

- [Codebase Architecture](./Docs/CODEBASE-AWARE%20MASTER%20PROMPT.md)
- [Project Rules & Tenancy](./Rule.md)
