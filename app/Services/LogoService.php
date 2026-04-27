<?php

namespace App\Services;

use App\Models\Church;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LogoService
{
    /**
     * Process and store uploaded logo.
     *
     * @param UploadedFile $file
     * @param Church $church
     * @param string $type 'logo' or 'logo_dark'
     * @return string Stored file path
     */
    public function processLogo(UploadedFile $file, Church $church, string $type = 'logo'): string
    {
        // Generate unique filename
        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . $extension;
        
        // Determine storage path based on tenant
        $tenantId = tenancy()->tenant?->getTenantKey() ?? 'default';
        $directory = "tenant-{$tenantId}/logos";
        
        // Store the file
        $path = $file->storeAs($directory, $filename, 'public');
        
        // TODO: Add image optimization here if Intervention Image is installed
        // - Resize to max 512x512
        // - Convert to WebP
        // - Optimize quality
        
        return $path;
    }

    /**
     * Generate favicon from logo.
     * 
     * @param string $logoPath
     * @return string|null Favicon path
     */
    public function generateFavicon(string $logoPath): ?string
    {
        // TODO: Implement favicon generation
        // For now, return null (will use default favicon)
        // Future: Use Intervention Image to create 16x16, 32x32, 180x180 variants
        
        return null;
    }

    /**
     * Get logo URL with fallback.
     *
     * @param Church $church
     * @param string $variant 'default', 'dark', or 'favicon'
     * @return string|null
     */
    public function getLogoUrl(Church $church, string $variant = 'default'): ?string
    {
        $path = match($variant) {
            'dark' => $church->logo_dark_path ?? $church->logo_path,
            'favicon' => $church->favicon_path,
            default => $church->logo_path,
        };
        
        if ($path && Storage::disk('public')->exists($path)) {
            return asset('storage/' . $path);
        }
        
        return null;
    }

    /**
     * Delete logo file from storage.
     *
     * @param string $path
     * @return bool
     */
    public function deleteLogo(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        
        return false;
    }

    /**
     * Delete all logo variants for a church.
     *
     * @param Church $church
     * @return void
     */
    public function deleteAllLogos(Church $church): void
    {
        if ($church->logo_path) {
            $this->deleteLogo($church->logo_path);
        }
        
        if ($church->logo_dark_path) {
            $this->deleteLogo($church->logo_dark_path);
        }
        
        if ($church->favicon_path) {
            $this->deleteLogo($church->favicon_path);
        }
    }

    /**
     * Validate uploaded logo file.
     *
     * @param UploadedFile $file
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validateLogo(UploadedFile $file): array
    {
        $errors = [];
        
        // Check file size (max 2MB)
        if ($file->getSize() > 2048 * 1024) {
            $errors[] = 'Logo file size must not exceed 2 MB.';
        }
        
        // Check file type
        $allowedMimes = ['image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            $errors[] = 'Logo must be a PNG, JPG, SVG, or WebP image.';
        }
        
        // Check if file is actually an image
        if (!$file->isValid()) {
            $errors[] = 'Invalid file upload.';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Get storage path for tenant logos.
     *
     * @return string
     */
    public function getTenantLogoDirectory(): string
    {
        $tenantId = tenancy()->tenant?->getTenantKey() ?? 'default';
        return "tenant-{$tenantId}/logos";
    }
}
