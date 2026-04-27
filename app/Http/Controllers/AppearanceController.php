<?php

namespace App\Http\Controllers;

use App\Services\ThemeService;
use App\Services\LogoService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AppearanceController extends Controller
{
    public function __construct(
        private ThemeService $themeService,
        private LogoService $logoService
    ) {}

    /**
     * Show the appearance customization form.
     */
    public function edit(): View
    {
        $church = auth()->user()->church;
        
        // Get accessibility validation for current color
        $accessibilityCheck = null;
        if ($church->primary_color) {
            $accessibilityCheck = $this->themeService->validateAccessibility($church->primary_color);
        }

        return view('settings.appearance', compact('church', 'accessibilityCheck'));
    }

    /**
     * Save appearance settings (color + logo).
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'primary_color'   => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'accent_color'    => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'logo'            => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'logo_dark'       => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'remove_logo'     => ['nullable', 'boolean'],
            'remove_logo_dark' => ['nullable', 'boolean'],
        ]);

        $church = $request->user()->church;

        // ── Colors ─────────────────────────────────────────────────
        $church->primary_color = $request->input('primary_color') ?: null;
        $church->secondary_color = $request->input('secondary_color') ?: null;
        $church->accent_color = $request->input('accent_color') ?: null;

        // Generate and store theme config
        if ($church->primary_color) {
            $palette = $this->themeService->generatePalette($church->primary_color);
            $church->theme_config = [
                'palette' => $palette,
                'generated_at' => now()->toIso8601String(),
            ];
        }

        // ── Logo (Light) ───────────────────────────────────────────
        if ($request->boolean('remove_logo') && $church->logo_path) {
            $this->logoService->deleteLogo($church->logo_path);
            $church->logo_path = null;
        }

        if ($request->hasFile('logo')) {
            // Validate logo
            $validation = $this->logoService->validateLogo($request->file('logo'));
            if (!$validation['valid']) {
                return back()->withErrors(['logo' => $validation['errors']]);
            }

            // Remove old logo first
            if ($church->logo_path) {
                $this->logoService->deleteLogo($church->logo_path);
            }

            // Process and store new logo
            $path = $this->logoService->processLogo($request->file('logo'), $church, 'logo');
            $church->logo_path = $path;
        }

        // ── Logo (Dark) ────────────────────────────────────────────
        if ($request->boolean('remove_logo_dark') && $church->logo_dark_path) {
            $this->logoService->deleteLogo($church->logo_dark_path);
            $church->logo_dark_path = null;
        }

        if ($request->hasFile('logo_dark')) {
            // Validate logo
            $validation = $this->logoService->validateLogo($request->file('logo_dark'));
            if (!$validation['valid']) {
                return back()->withErrors(['logo_dark' => $validation['errors']]);
            }

            // Remove old logo first
            if ($church->logo_dark_path) {
                $this->logoService->deleteLogo($church->logo_dark_path);
            }

            // Process and store new logo
            $path = $this->logoService->processLogo($request->file('logo_dark'), $church, 'logo_dark');
            $church->logo_dark_path = $path;
        }

        $church->save();

        return back()->with('success', 'Appearance settings updated successfully.');
    }
}
