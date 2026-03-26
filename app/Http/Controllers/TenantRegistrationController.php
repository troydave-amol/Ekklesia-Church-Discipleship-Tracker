<?php

namespace App\Http\Controllers;

use App\Models\Tenants;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TenantRegistrationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'church_name'    => 'required|string|max:255',
            'church_address' => 'required|string|max:500',
            'pastor_name'    => 'required|string|max:255',
            'pastor_email'   => 'required|email',
            'subdomain'      => 'required|string|alpha_num|max:32',
            'plan'           => 'required|in:free,basic,standard,premium',
        ]);

        // Check subdomain not already taken
        $taken = \Stancl\Tenancy\Database\Models\Domain::where(
            'domain', 'like', $validated['subdomain'] . '.%'
        )->exists();

        if ($taken) {
            return response()->json([
                'success' => false,
                'message' => 'That subdomain is already taken.'
            ], 422);
        }

        // Create the tenant record
        $tenant = Tenants::create([
            'id'   => Str::uuid(),
            'data' => [
                'church_name'    => $validated['church_name'],
                'church_address' => $validated['church_address'],
                'pastor_name'    => $validated['pastor_name'],
                'pastor_email'   => $validated['pastor_email'],
                'subdomain'      => $validated['subdomain'],
                'plan'           => $validated['plan'],
            ],
        ]);

        // Attach the subdomain domain
        $tenant->domains()->create([
            'domain' => $validated['subdomain'] . '.ekklesia.test',
        ]);

        return response()->json([
            'success'   => true,
            'subdomain' => $validated['subdomain'],
            'url'       => 'http://' . $validated['subdomain'] . '.ekklesia.test',
        ]);
    }

    public function checkSubdomain(Request $request)
    {
        $subdomain = strtolower(
            preg_replace('/[^a-z0-9]/', '', $request->query('subdomain', ''))
        );

        if (!$subdomain) {
            return response()->json(['available' => false]);
        }

        $taken = \Stancl\Tenancy\Database\Models\Domain::where(
            'domain', 'like', $subdomain . '.%'
        )->exists();

        return response()->json(['available' => !$taken]);
    }
}