# RULE.md — Discipleship Progress Tracker (Google Antigravity)

## 1) Project identity (do not change)
Build a multi-tenant SaaS web app called **Discipleship Progress Tracker**.

Purpose:
- Each **Church** is one tenant.
- Churches run **Destiny Training** programs:
  - Life Class
  - School of Leaders: SOL 1, SOL 2, SOL 3
- The system lets churches:
  - Upload weekly lessons per program
  - Track daily attendance
  - Track daily devotionals
  - Monitor disciple progress using a role-based workflow

Non-negotiable:
- **Church A must never see Church B data.**

## 2) Tenancy rules (hard requirements)
- Use **multi-database tenancy** design powered by `stancl/tenancy`.
- Each tenant (church) has its own isolated database.
- Central database maintains a `tenants` and `domains` table.
- Read/write queries for tenant data naturally fall under the tenant DB connection (`protected $connection = 'tenant';`).
- Even though true database isolation is used, some legacy relation tables maintain a `church_id` index logically.

## 3) Roles and permissions (RBAC)
Database-backed RBAC using `roles`, `permissions`, and `permission_role` tables.
There are four core roles implemented:
- church_admin (replaces pastor)
- program_leader (replaces leader)
- cell_leader 
- disciple

### Pastor permissions
- Manage church profile
- Manage subscription (plan and status)
- Create and manage programs
- Upload and edit weekly lessons
- Add leaders and disciples
- Assign leaders to disciples
- View full dashboard
- View reports

### Leader permissions
- Manage only assigned disciples
- Record attendance
- Record devotionals
- Track disciple progress
- View limited dashboard (leader scope only)

### Disciple permissions
- View lessons (only within their church and assigned program)
- Mark devotional complete (daily)
- View personal progress
- Update own profile

Permission enforcement rules:
- Leaders must only access disciples where the disciple is assigned to them (via leader_id).
- Disciples must only access their own data.
- Pastors can access all data within their church.

## 4) Subscription plans and feature gating (hard requirements)
The system must enforce subscription-based limits per church.

Plan: Free Tier
- Programs: 1 program only (Life Class)
- Max disciples: 20
- Max leaders: 5
- Features: basic attendance and lesson tracking
- Progress tracking: manual progress tracking only

Plan: Basic Subscription (₱800 / month per church)
- Programs: all 4 programs
- Max disciples: 50
- Max leaders: 5
- Features: attendance and devotional tracking
- Dashboard: basic hierarchical dashboard

Plan: Standard Subscription (₱1,500 / month per church)
- Programs: all 4 programs
- Disciples: unlimited (small church)
- Max leaders: 10
- Dashboard: program completion summary
- Exports: attendance and devotionals export (Google Sheets compatible)

Plan: Premium Subscription (₱2,500 / month)
- Disciples: unlimited
- Leaders: unlimited
- Bulk import: disciples bulk import
- Reports: historical reports per program
- Priority support flag/label (in-app message or plan badge)

Gating rules:
- If subscription_status is not active, restrict paid features (treat as Free or lock paid modules depending on product decision, but must be consistent everywhere).
- Plan limits must be checked on:
  - creating leaders
  - creating disciples
  - activating/creating programs
  - using exports
  - using bulk import
  - viewing reports

## 5) Required database entities (follow this structure)
Use these core tables and fields (names should match unless the stack forces a slight naming change).

1. churches
- id
- name
- subscription_plan
- subscription_status
- created_at

2. users (auth + role)
- id
- church_id
- role_id (foreign key to roles table)
- first_name
- last_name
- nickname
- email
- password
- role (church_admin, program_leader, cell_leader, disciple)
- created_at

3. user_profiles (personal + address)
- id
- user_id
- mobile_number
- gender
- civil_status
- date_of_birth
- address
- city
- state
- country
- postal_code

4. church_memberships (church-specific member data)
- id
- user_id
- church_id
- status (active, inactive, visitor, new_member)
- joined_at
- baptized_at
- leader_id (FK to users.id)
- satellite
- destiny_training_level (life_class, sol1, sol2, sol3, graduate)

5. programs
- id
- church_id
- name (Life Class, SOL 1–3)
- description

6. lessons
- id
- church_id
- program_id
- week_number
- title
- content

7. attendance
- id
- church_id
- user_id
- lesson_id
- date
- status (present/absent)

8. devotionals
- id
- church_id
- user_id
- date
- completed (boolean, tracked per day)

Data integrity rules:
- All tenant tables must include church_id and be consistent with user.church_id.
- Enforce foreign keys where possible.
- Add indexes for church_id + date-heavy queries (attendance/devotionals).

## 6) Modules to implement (must exist)
MODULE 1: Authentication
- Login
- Registration where Pastor creates a church
- Role-based redirect after login
- Password reset

MODULE 2: Church Management (Pastor only)
- Edit church info
- Manage subscription
- View plan limits

MODULE 3: User Management (Pastor)
- Add leaders
- Add disciples
- Assign leader to disciple
- Edit member profile

MODULE 4: Destiny Training (Programs)
Pastor:
- Create program
- Edit program
- Activate/deactivate program
- Assign disciple to program
Leader:
- Assign a disciple to the program (within church, and only for their assigned disciples)

MODULE 5: Lesson Management (Pastor)
- Upload weekly lessons
- Edit lessons
- Ensure lessons are tied to program + church

MODULE 6: Attendance Tracking (Leader/Pastor)
- Select lesson
- Mark disciples present/absent
- Save attendance record

MODULE 7: Devotional Tracking (Leader or Disciple)
- Disciple marks devotional complete (daily)
- Leader verifies (store verification in a minimal way if needed, but do not break the required devotionals table)
- Dashboard reflects compliance

MODULE 8: Dashboard
Pastor dashboard:
- Total disciples
- Active leaders
- Completion per program
- Attendance summary

Leader dashboard:
- Assigned disciples
- Attendance rate
- Devotional rate

Disciple dashboard:
- Current program
- Lessons completed
- Devotional streak

MODULE 9: Reports (Standard & Premium only)
- Attendance export (Google Sheets compatible export)
- Devotional export (Google Sheets compatible export)
- Program completion report
- Historical report per program (Premium)

## 7) Core workflows (must be supported)
- Pastor registers and creates a church tenant.
- Pastor adds leaders and disciples (enforce plan caps).
- Pastor assigns a leader to each disciple (via church_memberships.leader_id).
- Pastor or leader assigns disciples to programs.
- Pastor uploads weekly lessons per program.
- Leader/pastor marks attendance per lesson per date.
- Disciple marks devotional completion daily.
- Leader verifies devotionals (required behavior; storage can be implemented minimally).
- Dashboards compute metrics by role scope.
- Reports/export obey plan gating.

## 8) UI/UX rules (keep it clean, role-based)
- Show only what the role needs (pastor full, leader scoped, disciple personal).
- Always display plan name and limits somewhere for the pastor (Church Management).
- When a plan limit is reached, show a clear message and block the action.

## 9) Quality bar (do not skip)
- Strong authorization checks on every route/action.
- Tenant isolation tests (attempt cross-church access must fail).
- Consistent validation (dates, enum fields, required relations).
- Avoid duplicated attendance entries for the same user + lesson + date (enforce uniqueness or app-level protection).
- Make dashboards and reports accurate and consistent with the stored records.

## 10) Out of scope (unless explicitly added later)
- Messaging/SMS/email automation
- Public multi-church discovery directory
- Anything that breaks the established RBAC role model
- Any feature that allows cross-tenant visibility