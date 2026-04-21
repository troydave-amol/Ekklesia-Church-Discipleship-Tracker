# Admin Subsystem Documentation

## Overview
The Admin Subsystem is a central management console for Church Discipleship Tracker administrators. It provides features for tenant management, signup approval, support ticket handling, and system monitoring.

## Features

### 1. Authentication & Authorization
- **Admin Users**: Separate authentication for admin staff with role-based access control (RBAC)
- **Roles**:
  - `super_admin`: Full system access
  - `admin`: Standard admin access (manage tenants, signups, tickets)
  - `support`: Limited to support tickets and announcements
  - `viewer`: Read-only access to dashboards
- **Session Management**: Secure session handling with remember-me functionality

### 2. Central Dashboard
The admin dashboard provides:
- **Key Metrics**:
  - Pending Approvals (new signups)
  - Active Tenants
  - Total Tenants
  - Subscription Plan Distribution
- **Quick Actions**: Fast access to common tasks
- **System Overview**: High-level system health and statistics

### 3. Tenant Management
- **View Tenants**: Browse all church workspaces
- **Manage Tenants**: Update tenant status, billing cycles, settings
- **Audit Trail**: All changes logged via version control system
- **Metrics**: View per-tenant usage and statistics

### 4. Signup Management
- **Review Signups**: Queue of pending church registrations
- **Approval Workflow**: Accept/reject with automated notifications
- **Tenant Provisioning**: Auto-creation of tenant database and storage
- **Notification System**: Email notifications to requesters

### 5. Support Management
- **Ticket Tracking**: Central hub for customer support tickets
- **Ticket Routing**: Assign to appropriate admin staff
- **Response Management**: Track ticket status and communications
- **Analytics**: Support metrics and performance tracking

### 6. Announcements
- **Broadcast System**: Send announcements to all or specific tenants
- **Template System**: Reusable announcement templates
- **Scheduling**: Schedule announcements for future delivery
- **Tracking**: View announcement statistics and read receipts

### 7. Version Control & Audit
- **Change Logging**: All admin actions logged with timestamps
- **Tenant History**: Complete history of tenant changes
- **Permission Audit**: Track RBAC changes over time
- **Rollback Capability**: Restore previous configurations

## Architecture

### Database Schema
```
admin_users
├── id (primary key)
├── name
├── email (unique)
├── password (hashed)
├── role (enum: super_admin, admin, support, viewer)
├── status (enum: active, inactive)
├── last_login_at
├── created_at
└── updated_at

admin_audit_logs
├── id (primary key)
├── admin_user_id (foreign key)
├── action (create, update, delete, approve, reject)
├── resource_type (tenant, signup, ticket, announcement)
├── resource_id
├── changes (JSON)
├── ip_address
├── user_agent
├── created_at
```

### Routing
- **Prefix**: `/admin`
- **Middleware**: `auth:admin`, `admin:RBAC`
- **Guard**: `admin`

### Controllers
- `AuthController`: Login, logout, password reset
- `CentralDashboardController`: Dashboard metrics
- `TenantController`: Tenant CRUD & management
- `SignupController`: Signup approval workflow
- `TicketController`: Support ticket management
- `AnnouncementController`: System announcements
- `AdminUserController`: Admin staff management (super_admin only)

## Security

### Authentication
- Secure password hashing (bcrypt)
- Session-based authentication with optional remember-me
- Automatic session timeout
- Password reset flow with email verification

### Authorization
- Role-based access control (RBAC)
- Resource-level permissions
- Tenant isolation
- Admin action logging

### Best Practices
- Never expose admin credentials in logs
- All sensitive operations require confirmation
- Audit trail for all modifications
- IP whitelisting for sensitive operations (optional)
- Two-factor authentication (future enhancement)

## Usage

### Creating Your First Admin User

```bash
php artisan app:create-admin
# or with flags:
php artisan app:create-admin --name="John Doe" --email="admin@example.com" --password="secure_password"
```

### Accessing Admin Panel
1. Navigate to `https://your-domain.com/admin/login`
2. Enter your credentials
3. You'll be redirected to the dashboard

### Common Admin Tasks

#### Approving a Signup
1. Go to "Signups" in sidebar
2. Review pending signup details
3. Click "Approve" or "Reject"
4. Automatic tenant provisioning occurs on approval
5. Church admin receives welcome email

#### Managing a Tenant
1. Go to "Tenants" in sidebar
2. Click on a tenant to view details
3. Update status, billing cycle, or settings
4. All changes are logged in audit trail

#### Creating an Announcement
1. Go to "Announcements" in sidebar
2. Click "New Announcement"
3. Enter content, select recipient scope
4. Preview and publish
5. Track delivery and reads

#### Viewing Audit Logs
1. Go to "System" → "Audit Logs"
2. Filter by date, admin user, resource type, or action
3. View detailed change history
4. Export logs as CSV

## Configuration

### `.env` Variables
```env
ADMIN_SESSION_TIMEOUT=1440  # minutes
ADMIN_REMEMBER_TOKEN_EXPIRY=30  # days
ADMIN_REQUIRE_2FA=false  # future enhancement
ADMIN_IP_WHITELIST=  # comma-separated list (optional)
```

### Routes Configuration
Admin routes are protected and require the `admin` guard. See `routes/admin.php` for route definitions.

## API Endpoints (Future)

The admin subsystem will expose REST APIs for programmatic access:
```
GET    /api/admin/tenants
POST   /api/admin/tenants/:id/approve
POST   /api/admin/tenants/:id/reject
GET    /api/admin/audit-logs
POST   /api/admin/announcements
```

## Error Handling

### Common Errors
- **403 Forbidden**: You don't have permission for this action
- **401 Unauthorized**: Session expired, please login again
- **404 Not Found**: Resource doesn't exist
- **422 Unprocessable Entity**: Validation errors

### Logging
All errors are logged to `storage/logs/admin.log` with full context.

## Performance Considerations

- Dashboard queries are optimized with indexing
- Pagination on large lists (tenants, tickets, audit logs)
- Caching of frequently accessed data
- Database connection pooling

## Future Enhancements

- [ ] Two-Factor Authentication (2FA)
- [ ] API Authentication (API keys/tokens)
- [ ] Advanced Reporting & Analytics
- [ ] Bulk Operations (bulk approve/reject)
- [ ] Scheduled Tasks Management
- [ ] Real-time Notifications
- [ ] System Health Monitoring
- [ ] Custom Admin Roles
- [ ] Webhook Integration

## Troubleshooting

### Admin can't login
1. Verify admin user exists: `php artisan tinker` → `AdminUser::where('email', 'admin@example.com')->first()`
2. Check if account is active: `admin->status === 'active'`
3. Reset password using command

### Missing permissions
1. Check admin role: `auth('admin')->user()->role`
2. Verify RBAC middleware is applied
3. Review admin user's assigned permissions

### Audit logs not appearing
1. Check if event listeners are registered
2. Verify `AdminAuditLog` migration ran successfully
3. Review error logs in `storage/logs/admin.log`

## Support
For issues or questions, create an issue in the main repository or contact the development team.
