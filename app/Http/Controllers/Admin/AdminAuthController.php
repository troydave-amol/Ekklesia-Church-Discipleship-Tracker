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
            'role' => 'required|in:super_admin,admin,support,viewer',
        ]);

        $admin = AdminUser::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        // Log version history
        \App\Models\VersionHistory::logChange(
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
    public function edit(AdminUser $admin): View
    {
        if (!auth('admin')->user()?->canManageAdmins()) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.users.edit', compact('admin'));
    }

    /**
     * Update admin user (superadmin only)
     */
    public function update(Request $request, AdminUser $admin): RedirectResponse
    {
        if (!auth('admin')->user()?->canManageAdmins()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admin_users,email,' . $admin->id,
            'role' => 'required|in:super_admin,admin,support,viewer',
            'status' => 'required|in:active,inactive',
        ]);

        $oldValues = $admin->toArray();
        $admin->update($validated);

        // Log version history
        \App\Models\VersionHistory::logChange(
            'admin_user',
            $admin->id,
            auth('admin')->id(),
            'updated',
            "Updated admin user: {$admin->name}",
            $oldValues,
            $admin->toArray()
        );

        return redirect()->route('admin.users.index')->with('success', 'Admin updated.');
    }

    /**
     * Delete admin user (superadmin only)
     */
    public function destroy(AdminUser $admin): RedirectResponse
    {
        if (!auth('admin')->user()?->canManageAdmins()) {
            abort(403, 'Unauthorized action.');
        }

        $name = $admin->name;
        $admin->delete();

        \App\Models\VersionHistory::logChange(
            'admin_user',
            $admin->id,
            auth('admin')->id(),
            'deleted',
            "Deleted admin user: {$name}"
        );

        return redirect()->route('admin.users.index')->with('success', 'Admin deleted.');
    }
}
