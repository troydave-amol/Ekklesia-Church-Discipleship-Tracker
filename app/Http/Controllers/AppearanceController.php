<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class AppearanceController extends Controller
{
    /**
     * Show the appearance customization form.
     */
    public function edit(): View
    {
        $church = auth()->user()->church;

        return view('settings.appearance', compact('church'));
    }

    /**
     * Save appearance settings (color + logo).
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'primary_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'logo'          => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'remove_logo'   => ['nullable', 'boolean'],
        ]);

        $church = $request->user()->church;

        // ── Color ──────────────────────────────────────────────────
        $church->primary_color = $request->input('primary_color') ?: null;

        // ── Logo ───────────────────────────────────────────────────
        if ($request->boolean('remove_logo') && $church->logo_path) {
            Storage::disk('public')->delete($church->logo_path);
            $church->logo_path = null;
        }

        if ($request->hasFile('logo')) {
            // Remove old logo first
            if ($church->logo_path) {
                Storage::disk('public')->delete($church->logo_path);
            }

            $path = $request->file('logo')->store('church-logos', 'public');
            $church->logo_path = $path;
        }

        $church->save();

        return back()->with('success', 'Appearance settings updated successfully.');
    }
}
