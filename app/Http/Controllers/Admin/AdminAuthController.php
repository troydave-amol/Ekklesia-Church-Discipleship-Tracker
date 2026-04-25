<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminAuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $admin = AdminUser::where('email', $credentials['email'])->first();

        // Reject inactive accounts before attempting login to avoid creating session
        if ($admin && !$admin->is_active) {
            return back()->withErrors([
                'email' => 'Your account is inactive.',
            ]);
        }

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            // Log the login
            $admin = Auth::guard('admin')->user();
            $admin->update(['last_login_at' => now()]);

            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /**
     * Show all admin users (superadmin only)
     */
    public function index(): View
    {
        if (!auth('admin')->user()?->canManageAdmins()) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.users.index', [
            'admins' => AdminUser::latest()->paginate(15),
        ]);
    }

    /**
     * Show create admin form (superadmin only)
     */
    public function create(): View
    {
        if (!auth('admin')->user()?->canManageAdmins()) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.users.create');
    }

    /**
     * Store new admin user (superadmin only)
     */
    public function store(Request $request): RedirectResponse
    {
        if (!auth('admin')->user()?->canManageAdmins()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admin_users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:superadmin,manager,viewer',
        ]);

        $admin = AdminUser::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        // Log version history
        \App\Models\AdminAuditLog::logChange(
            'admin_user',
            $admin->id,
            auth('admin')->id(),
            'created',
            "Created admin user: {$admin->name} ({$validated['role']})",
            null,
            $admin->toArray()
        );

        return redirect()->route('admin.users.index')->with('success', 'Admin user created.');
    }

    /**
     * Show edit admin form (superadmin only)
     */
    public function edit(AdminUser $user): View
    {
        if (!auth('admin')->user()?->canManageAdmins()) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.users.edit', ['admin' => $user]);
    }

    /**
     * Update admin user (superadmin only)
     */
    public function update(Request $request, AdminUser $user): RedirectResponse
    {
        if (!auth('admin')->user()?->canManageAdmins()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admin_users,email,' . $user->id,
            'role' => 'required|in:superadmin,manager,viewer',
            'is_active' => 'required|boolean',
        ]);

        $oldValues = $user->toArray();
        $user->update($validated);

        // Log audit trail
        \App\Models\AdminAuditLog::logChange(
            'admin_user',
            $user->id,
            auth('admin')->id(),
            'updated',
            "Updated admin user: {$user->name}",
            $oldValues,
            $user->toArray()
        );

        return redirect()->route('admin.users.index')->with('success', 'Admin updated.');
    }

    /**
     * Delete admin user (superadmin only)
     */
    public function destroy(AdminUser $user): RedirectResponse
    {
        if (!auth('admin')->user()?->canManageAdmins()) {
            abort(403, 'Unauthorized action.');
        }

        $name = $user->name;
        $user->delete();

        \App\Models\AdminAuditLog::logChange(
            'admin_user',
            $user->id,
            auth('admin')->id(),
            'deleted',
            "Deleted admin user: {$name}"
        );

        return redirect()->route('admin.users.index')->with('success', 'Admin deleted.');
    }
}
