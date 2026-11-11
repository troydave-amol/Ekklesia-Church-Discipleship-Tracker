# Ekklesia — Smart Church Discipleship & Progression Platform

> **A Multi-Tenant Cloud SaaS Platform for Structured Discipleship Tracking, Spiritual Growth Analytics, and Congregational Leadership Pipelines.**

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php)](https://php.net)
[![Multi--Tenancy](https://img.shields.io/badge/Tenancy-Stancl%20v3.10-4A90E2?style=flat-square)](https://tenancyforlaravel.com/)
[![Database](https://img.shields.io/badge/Database-MySQL%20Isolated-00758F?style=flat-square&logo=mysql)](https://mysql.com)
[![Frontend](https://img.shields.io/badge/Vite-Tailwind%20CSS-38B2AC?style=flat-square&logo=tailwind-css)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=flat-square)](LICENSE)

---

## 1. Executive Summary & Purpose

While generic Church Management Systems (ChMS) focus heavily on financial tithing, member directories, or broad event announcements, they rarely address the core spiritual engine of the church: **disciple development and intentional mentoring**. In traditional church environments, discipleship progress, cell group attendance, and devotional habits are typically scattered across paper journals, chat apps, or unstandardized spreadsheets.

**Ekklesia (Church Discipleship Tracker)** solves this challenge by providing local churches with a dedicated, cloud-native discipleship platform. It bridges the gap between casual Sunday attendance and active spiritual leadership through structured training stages, accountability tracking, and quantitative engagement insights.

---

## 2. Project Objectives

* **Standardize Spiritual Milestones:** Transition informal discipleship into structured, trackable progression phases (e.g., *Life Class*, *School of Leaders 1, 2, and 3*).
* **Guaranteed Data Isolation:** Implement a strict **database-per-tenant architecture** ensuring zero cross-tenant data contamination or shared-table privacy risks.
* **Streamline Mentorship & Accountability:** Equip cell leaders with direct visibility into their disciples' attendance consistency, lesson prerequisites, and daily devotional habits.
* **White-Label Customization:** Provide church administrators with accessible, brand-aligned interfaces featuring custom church logos and mathematically computed, WCAG-compliant color palettes.
* **Scalable Central Governance:** Enable SaaS operators to review church registrations, automatically provision independent tenant databases, and push modular feature updates seamlessly.

---

## 3. Target Audience & Stakeholders

```
┌────────────────────────────────────────────────────────────────────────┐
│                        CENTRAL PLATFORM OPERATOR                       │
│    (SaaS Super Admin · Tenant Review · Automated DB Provisioning)      │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │ Dedicated Subdomains
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│                        LOCAL CHURCH WORKSPACE                          │
│                                                                        │
│  [Church Admin / Senior Pastor]  ── Full church oversight & settings  │
│  [Program Leader / Director]     ── Curriculum & cohort coordination   │
│  [Cell Leader / Small Group]     ── Discipleship mentorship & habit review │
│  [Disciple / Member]             ── Personal spiritual progress portal │
└────────────────────────────────────────────────────────────────────────┘
```

* **Senior Pastors & Church Boards:** High-level dashboard visibility into congregational maturity, retention trends, and leadership readiness.
* **Ministry Directors & Program Leaders:** Tools to schedule cohorts, manage lesson outlines, and track graduation prerequisites.
* **Cell Group Mentors:** Scoped access to mentor assigned disciples, log weekly cell meetings, and endorse devotional consistency.
* **Church Members & Disciples:** A self-service portal to review class lessons, log spiritual quiet time, and monitor their pathway toward leadership.
* **SaaS Service Providers & Partner Organizations:** An enterprise-ready multi-tenant codebase built for sustainable subscription models and multi-site network deployments.

---

## 4. Key System Features

### Multi-Tenant Architecture & Automated Provisioning
* **Dedicated Subdomain Routing:** Dynamic tenant resolution (`churchname.domain.com` or `churchname.localhost`).
* **Database-per-Tenant Separation:** Powered by `stancl/tenancy`, every church is allocated its own database, ensuring enterprise-grade privacy and zero foreign-key collisions.
* **Zero-Touch Provisioning Engine:** Automatically executes migrations, seeds default roles, sets up four foundational curricula, and configures storage upon admin approval.

### Discipleship Progression Pathway
* **Structured Curricula:** Pre-configured four-stage training pipeline:
  1. **Life Class:** Foundations of faith and newcomer integration.
  2. **School of Leaders 1 (SOL 1):** Personal spiritual disciplines and evangelism.
  3. **School of Leaders 2 (SOL 2):** Cell group planting and small-group leadership.
  4. **School of Leaders 3 (SOL 3):** Senior leadership, counseling, and church administration.
* **Lesson Builder & Prerequisites:** Teachers can author lesson outlines, attach resource materials, and mandate prerequisite completions before disciples advance.

### Attendance & Devotional Tracking Engine
* **Session Attendance Logging:** Fast, mobile-responsive recording for Sunday worship services and mid-week cell meetings.
* **Devotional Journaling:** Disciples log daily Bible reflections and prayer requests, shifting the focus from mere physical presence to holistic spiritual health.
* **Verification Workflow:** Leaders review and confirm journal entries to validate disciple consistency.

### Granular Role-Based Access Control (RBAC)
* Built-in hierarchical permissions across five tiers: **Super Admin**, **Church Admin**, **Program Leader**, **Cell Leader**, and **Disciple**.
* Strict data scoping ensures leaders only view and manage members within their assigned sphere of care.

### Dynamic White-Labeling & Accessible Theming
* **WCAG-Compliant Palette Engine:** Admins enter a primary hex color; the system programmatically generates an 11-shade palette (`50` through `950`) and evaluates luminance contrast ratios.
* **Logo Management:** Secure upload and storage pipeline supporting PNG, JPG, SVG, and WebP, dynamically branding sidebars and communication emails.

### Central SaaS Control Plane & Feature Deployment
* **Tenant Lifecycle Management:** Comprehensive registration approval queue, tenant suspension controls, and billing tier oversight.
* **Integrated Support Desk:** In-app ticket routing connecting church leaders directly with platform support administrators.
* **In-App Product Changelogs & Feature Pipelines:** System-wide release notifications and self-service feature package installers for tenants.

---

## 5. Technology Stack

| Layer | Technologies | Purpose |
| :--- | :--- | :--- |
| **Backend Framework** | PHP 8.2+, Laravel 12.x | High-performance application core, validation, routing, and ORM. |
| **Multi-Tenancy** | Stancl Tenancy v3.10 | Subdomain identification and automated multi-database lifecycle management. |
| **Database** | MySQL 8.0+ / MariaDB | Dual-connection architecture: Central catalog DB + isolated Tenant DBs. |
| **Frontend & UI** | Blade, Tailwind CSS, Vanilla JS | Lightweight, server-rendered views with zero heavy frontend framework overhead. |
| **Build & Assets** | Vite 7.x, PostCSS | Page-split asset bundling (`admin.css`, `auth.css`, `base.css`, `landing.css`). |
| **Testing** | Pest PHP, PHPUnit | End-to-end multi-tenant feature and unit test coverage. |

---

## 6. System Architecture & Workflow

```
                                 [ Incoming Request ]
                                          │
                        ┌─────────────────┴─────────────────┐
                        │ Stancl Subdomain / Host Resolver  │
                        └─────────────────┬─────────────────┘
                                          │
                  ┌───────────────────────┴───────────────────────┐
                  ▼                                               ▼
         [ Central Domain ]                              [ Tenant Subdomain ]
       (app.io / localhost)                           (smhs.io / smhs.localhost)
                  │                                               │
          ┌───────┴───────┐                               ┌───────┴───────┐
          │ routes/central│                               │ routes/tenant │
          └───────┬───────┘                               └───────┬───────┘
                  ▼                                               ▼
      ┌───────────────────────┐                       ┌───────────────────────┐
      │  Central Database     │                       │ Dedicated Tenant DB   │
      │  • Tenants & Domains  │                       │ • Church Profile      │
      │  • Admin Users & RBAC │                       │ • Members & Roles     │
      │  • Global Changelog   │                       │ • Classes & Lessons   │
      │  • Support Desk       │                       │ • Attendance & Habits │
      └───────────────────────┘                       └───────────────────────┘
```

### End-to-End Workflow

1. **Church Registration:** A church representative registers via the public landing page with desired church details, pastor credentials, and chosen subdomain.
2. **Review & Approval:** The platform Super Admin reviews the application in the Central Admin console and clicks **Approve**.
3. **Automated Provisioning:** The provisioning service creates a dedicated MySQL database, executes tenant migrations, seeds standard roles/permissions/curricula, and emits a welcome notification.
4. **Onboarding & White-Labeling:** The pastor logs in, uploads the church emblem, configures their brand color, and defines program schedules.
5. **Active Discipleship:** Leaders onboard disciples, log attendance, review daily quiet times, and advance mature members through the leadership pipeline.

---

## 7. Expected Outcomes & Impact

* **Increased Member Retention:** Structured milestone tracking significantly mitigates the "back-door" attrition common in growing churches.
* **Pastoral Leadership Clarity:** Replaces anecdotal impressions with empirical attendance rates and spiritual habit engagement metrics.
* **Institutional Memory:** Preserves multi-year discipleship and baptismal records regardless of leader turnover.
* **Uncompromising Security:** Complete database separation guarantees church records remain strictly confidential and isolated from neighboring tenants.

---

## 8. Quick Start & Local Deployment

### Prerequisites
* **PHP 8.2+** with extensions: `pdo_mysql`, `openssl`, `mbstring`, `fileinfo`, `curl`
* **Composer 2.x**
* **Node.js (v18+) & NPM**
* **MySQL / MariaDB** (e.g., via XAMPP on port `3306`)

### Step-by-Step Installation

```bash
# 1. Clone the repository
git clone https://github.com/your-org/church-discipleship-tracker.git
cd church-discipleship-tracker

# 2. Install dependencies
composer install
npm install

# 3. Environment configuration
cp .env.example .env
php artisan key:generate
php artisan storage:link

# 4. Database initialization
# Ensure central DB exists: 'church_discipleship_tracker'
php artisan migrate
php artisan db:seed --class=AdminSeeder
php artisan tenancy:migrate

# 5. Compile production frontend assets
npm run build
```

### Running the System
Because Apache/XAMPP serves directly from the `public/` directory or via Artisan:
```bash
# Start Laravel development server (if not using Apache)
php artisan serve --host=0.0.0.0 --port=80
```

### Default Access Points

| Portal | URL | Demo Credentials | Description |
| :--- | :--- | :--- | :--- |
| **Public Landing** | `http://localhost/` | *Public Access* | Registration wizard, pricing plans, and overview. |
| **Central Admin** | `http://localhost/admin/login` | `admin@church.local`<br>`admin@123` | Platform-wide control plane, tenant approvals, and releases. |
| **Tenant Portal** | `http://smhs.localhost/login` | `kaelithsuperstar@gmail.com`<br>`password123` | Church Pastor / Administrator workspace. |
| **Program Leader** | `http://smhs.localhost/login` | `programleader@smhs.local`<br>`password123` | Curriculum coordinator and cohort oversight. |
| **Disciple Portal** | `http://smhs.localhost/login` | `disciple@smhs.local`<br>`password123` | Personal progress, lessons, and quiet time logs. |

---

## 9. Quality Assurance & Testing

The project maintains test suites covering role-based permission gates, multi-tenant isolation, and service workflows:

```bash
# Run unit and feature test suites
php artisan test

# Verify code styling standards
./vendor/bin/pint --test
```

---

## 10. Project Documentation Directory

For deeper architectural guidelines, refer to the project documentation:
* [Architecture Master Specifications](Docs/CODEBASE-AWARE%20MASTER%20PROMPT.md)
* [Central Admin Control Subsystem](Docs/ADMIN-SYSTEM.md)
* [Database Migration & Tenancy Strategy](Docs/MIGRATION-STRATEGY.md)
* [Multi-Tenant RBAC Enforcement Guide](Docs/RBAC%20ENFORCER.md)
* [Version Control & Release Pipeline](Docs/VERSION-CONTROL-WORKFLOW.md)

---

## 11. License

This project is licensed under the **MIT License**. See the [LICENSE](LICENSE) file for complete licensing terms.
