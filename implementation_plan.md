# Fixing Tenant Dashboard Notifications

This document outlines the strategic analysis and implementation plan to build out and fix the notification functionality in the tenant dashboard.

## Root Cause Investigation

After reviewing the codebase (frontend Blade templates, package files, and backend routes/controllers), I have identified why notifications are currently not working:

1. **Missing Backend Notification Logic**: The `app/Notifications` directory does not exist. While the database table `notifications` is now set up correctly, there are no Laravel Notification classes implemented in the system, nor is there any application logic (like adding a disciple or logging attendance) that triggers sending a notification.
2. **Missing Real-Time Infrastructure**: Despite earlier attempts mentioned in the session history, the required packages for real-time broadcasting (`laravel/reverb`, `pusher/pusher-php-server` for the backend, and `laravel-echo`, `pusher-js` for the frontend) are **not installed** in `composer.json` or `package.json`.
3. **Frontend Not Wired for Real-Time**: The `topbar.blade.php` properly renders existing notifications from the database on page load, but it is not wired with Laravel Echo to listen for incoming real-time events.

> [!WARNING]
> The previous session's Reverb setup was likely lost due to an uncommitted state or crash. We need to reinstall the dependencies.

## User Review Required

> [!IMPORTANT]
> **Trigger Points**: I need to know *what* actions should trigger notifications for the user. 
> 
> Typical examples include:
> - When a new disciple is assigned to a leader.
> - When a disciple submits a devotional.
> - When a support ticket is replied to.
> 
> Please specify which trigger(s) you would like me to implement first as a proof-of-concept for the notification system.

## Proposed Changes

We need to implement this across the entire stack.

### 1. Infrastructure & Real-Time Services (Backend)

- **Install Dependencies**: Install `laravel/reverb` and `pusher/pusher-php-server`.
- **Configure Broadcasting**: Run `php artisan install:broadcasting` to set up Reverb, configure `config/broadcasting.php`, and update the `.env` with Reverb credentials.

### 2. Frontend Infrastructure (Real-Time)

- **Install JS Packages**: Install `laravel-echo` and `pusher-js` via npm.
- **Configure Echo**: Update `resources/js/echo.js` to connect to the local Reverb server.
- **Wire Topbar**: Update the Alpine.js component in `topbar.blade.php` to listen on the `App.Models.User.{id}` private channel and prepend new notifications to the dropdown dynamically.

### 3. Backend Implementation (Notification Classes)

- **Create Notification Class**: Create a base notification class (e.g., `app/Notifications/SystemAlert.php` or a specific one like `NewDiscipleAssigned`).
- **Implement ShouldBroadcast**: Ensure the notification implements `ShouldBroadcast` so it goes to both the database and the WebSocket server.
- **Trigger Logic**: Hook the notification into a controller (based on your feedback above) to actually send it via `$user->notify(...)`.

## Step-by-Step Implementation Process

1. **Phase 1: Setup Infrastructure**
   - Run Composer and NPM installations for Reverb and Echo.
   - Configure `.env` and `broadcasting.php`.
2. **Phase 2: Backend Logic**
   - Generate a sample Notification class (`php artisan make:notification`).
   - Implement the `toDatabase` and `toBroadcast` methods.
3. **Phase 3: Frontend Integration**
   - Update `topbar.blade.php` to include Laravel Echo listeners.
   - Update the UI dynamically when a broadcast event is received.
4. **Phase 4: Trigger Implementation**
   - Add the `$user->notify()` call to the agreed-upon application action.

## Best Practices for Scalability

- **Queueing**: Notifications should be pushed to a background queue (`ShouldQueue`) so that the web request isn't delayed while broadcasting to Reverb or saving to the DB.
- **Event-Driven**: Rather than scattering `$user->notify()` everywhere, we should fire Laravel Events (e.g., `DiscipleAssigned`) and have an Event Listener handle sending the notification.
- **Tenancy Context**: Ensure Reverb and queued notifications properly resolve the active tenant context.

## Verification Plan

### Automated Tests
- N/A (We will verify manually in the browser first).

### Manual Verification
1. Open the tenant dashboard in the browser.
2. In a separate terminal or tinker session, manually trigger a notification to the logged-in user.
3. Verify that the notification bell indicator updates automatically without a page refresh.
4. Open the dropdown and verify the new notification appears at the top.
5. Verify that clicking "Mark all read" correctly clears the unread indicator.
