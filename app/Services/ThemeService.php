<?php

namespace App\Services;

use App\Models\Church;

class ThemeService
{
    /**
     * Generate a complete color palette from a primary hex color.
     * Returns shades from 50 (lightest) to 950 (darkest).
     *
     * @param string $primaryHex Hex color (e.g., "#D4A853")
     * @return array Associative array with shade keys (50, 100, ..., 950)
     */
    public function generatePalette(string $primaryHex): array
    {
        // Convert hex to RGB
        $rgb = $this->hexToRgb($primaryHex);
        
        // Convert RGB to HSL for easier manipulation
        $hsl = $this->rgbToHsl($rgb['r'], $rgb['g'], $rgb['b']);
        
        $palette = [];
        
        // Generate shades
        $shades = [
            50 => ['l' => 0.95, 's' => 0.9],   // Very light
            100 => ['l' => 0.90, 's' => 0.95],
            200 => ['l' => 0.80, 's' => 1.0],
            300 => ['l' => 0.70, 's' => 1.0],
            400 => ['l' => 0.60, 's' => 1.0],
            500 => ['l' => $hsl['l'], 's' => $hsl['s']], // Original color
            600 => ['l' => 0.45, 's' => 1.0],
            700 => ['l' => 0.35, 's' => 1.0],
            800 => ['l' => 0.25, 's' => 0.95],
            900 => ['l' => 0.15, 's' => 0.9],
            950 => ['l' => 0.10, 's' => 0.85],  // Very dark
        ];
        
        foreach ($shades as $shade => $adjustments) {
            $newHsl = [
                'h' => $hsl['h'],
                's' => $hsl['s'] * $adjustments['s'],
                'l' => $adjustments['l'],
            ];
            
            $rgb = $this->hslToRgb($newHsl['h'], $newHsl['s'], $newHsl['l']);
            $palette[$shade] = $this->rgbToHex($rgb['r'], $rgb['g'], $rgb['b']);
        }
        
        return $palette;
    }

    /**
     * Calculate contrast ratio between two colors (WCAG formula).
     *
     * @param string $color1 Hex color
     * @param string $color2 Hex color
     * @return float Contrast ratio (1-21)
     */
    public function getContrastRatio(string $color1, string $color2): float
    {
        $l1 = $this->getRelativeLuminance($color1);
        $l2 = $this->getRelativeLuminance($color2);
        
        $lighter = max($l1, $l2);
        $darker = min($l1, $l2);
        
        return ($lighter + 0.05) / ($darker + 0.05);
    }

    /**
     * Get recommended text color (light or dark) for a background color.
     *
     * @param string $bgColor Hex background color
     * @return string '#FFFFFF' or '#0D1117'
     */
    public function getTextColor(string $bgColor): string
    {
        $contrastWithWhite = $this->getContrastRatio($bgColor, '#FFFFFF');
        $contrastWithDark = $this->getContrastRatio($bgColor, '#0D1117');
        
        return $contrastWithWhite > $contrastWithDark ? '#FFFFFF' : '#0D1117';
    }

    /**
     * Generate CSS custom properties for a church's theme.
     *
     * @param Church|null $church
     * @return string CSS custom properties
     */
    public function generateCSSVariables(?Church $church): string
    {
        if (!$church || !$church->primary_color) {
            return $this->getDefaultCSSVariables();
        }

        $primaryColor = $church->primary_color;
        $palette = $this->generatePalette($primaryColor);
        $textColor = $this->getTextColor($primaryColor);

        $css = "/* Tenant Theme Variables */\n";
        $css .= "--tenant-primary: {$primaryColor};\n";
        $css .= "--tenant-text-on-primary: {$textColor};\n";
        
        // Add all palette shades
        foreach ($palette as $shade => $color) {
            $css .= "--tenant-primary-{$shade}: {$color};\n";
        }
        
        // Semantic color mappings
        $css .= "\n/* Semantic Mappings */\n";
        $css .= "--btn-primary-bg: var(--tenant-primary);\n";
        $css .= "--btn-primary-text: var(--tenant-text-on-primary);\n";
        $css .= "--btn-primary-hover: var(--tenant-primary-600);\n";
        $css .= "--link-color: var(--tenant-primary);\n";
        $css .= "--link-hover: var(--tenant-primary-600);\n";
        $css .= "--focus-ring: var(--tenant-primary-200);\n";
        $css .= "--progress-bar: var(--tenant-primary);\n";
        $css .= "--badge-accent-bg: var(--tenant-primary-100);\n";
        $css .= "--badge-accent-text: var(--tenant-primary-700);\n";
        $css .= "--badge-accent-border: var(--tenant-primary-300);\n";
        $css .= "--tab-active: var(--tenant-primary);\n";
        $css .= "--toggle-active: var(--tenant-primary);\n";
        $css .= "--nav-active-bg: var(--tenant-primary-100);\n";
        $css .= "--nav-active-text: var(--tenant-primary);\n";
        $css .= "--nav-active-border: var(--tenant-primary);\n";
        
        // Secondary and accent colors if set
        if ($church->secondary_color) {
            $css .= "--tenant-secondary: {$church->secondary_color};\n";
        }
        
        if ($church->accent_color) {
            $css .= "--tenant-accent: {$church->accent_color};\n";
        }

        return $css;
    }

    /**
     * Validate color accessibility against WCAG standards.
     *
     * @param string $primaryColor Hex color
     * @param string $level 'AA' or 'AAA'
     * @return array ['passes' => bool, 'ratio' => float, 'issues' => array]
     */
    public function validateAccessibility(string $primaryColor, string $level = 'AA'): array
    {
        $darkBg = '#0D1117';
        $lightBg = '#FFFFFF';
        
        $contrastDark = $this->getContrastRatio($primaryColor, $darkBg);
        $contrastLight = $this->getContrastRatio($primaryColor, $lightBg);
        
        $minRatio = $level === 'AAA' ? 7.0 : 4.5;
        
        $issues = [];
        
        if ($contrastDark < $minRatio) {
            $issues[] = "Insufficient contrast against dark backgrounds (ratio: " . number_format($contrastDark, 2) . ":1, needs {$minRatio}:1)";
        }
        
        if ($contrastLight < $minRatio) {
            $issues[] = "Insufficient contrast against light backgrounds (ratio: " . number_format($contrastLight, 2) . ":1, needs {$minRatio}:1)";
        }
        
        return [
            'passes' => empty($issues),
            'level' => $level,
            'contrast_dark' => $contrastDark,
            'contrast_light' => $contrastLight,
            'issues' => $issues,
        ];
    }

    /**
     * Get default CSS variables (fallback).
     */
    private function getDefaultCSSVariables(): string
    {
        return "--tenant-primary: #D4A853;\n--tenant-text-on-primary: #0D1117;";
    }

    /**
     * Convert hex color to RGB.
     */
    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        
        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * Convert RGB to hex.
     */
    private function rgbToHex(int $r, int $g, int $b): string
    {
        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }

    /**
     * Convert RGB to HSL.
     */
    private function rgbToHsl(int $r, int $g, int $b): array
    {
        $r /= 255;
        $g /= 255;
        $b /= 255;
        
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;
        
        if ($max === $min) {
            $h = $s = 0;
        } else {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);
            
            switch ($max) {
                case $r:
                    $h = (($g - $b) / $d + ($g < $b ? 6 : 0)) / 6;
                    break;
                case $g:
                    $h = (($b - $r) / $d + 2) / 6;
                    break;
                case $b:
                    $h = (($r - $g) / $d + 4) / 6;
                    break;
            }
        }
        
        return ['h' => $h, 's' => $s, 'l' => $l];
    }

    /**
     * Convert HSL to RGB.
     */
    private function hslToRgb(float $h, float $s, float $l): array
    {
        if ($s == 0) {
            $r = $g = $b = $l;
        } else {
            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;
            
            $r = $this->hueToRgb($p, $q, $h + 1/3);
            $g = $this->hueToRgb($p, $q, $h);
            $b = $this->hueToRgb($p, $q, $h - 1/3);
        }
        
        return [
            'r' => round($r * 255),
            'g' => round($g * 255),
            'b' => round($b * 255),
        ];
    }

    /**
     * Helper for HSL to RGB conversion.
     */
    private function hueToRgb(float $p, float $q, float $t): float
    {
        if ($t < 0) $t += 1;
        if ($t > 1) $t -= 1;
        if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
        if ($t < 1/2) return $q;
        if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;
        return $p;
    }

    /**
     * Calculate relative luminance for contrast ratio.
     */
    private function getRelativeLuminance(string $hex): float
    {
        $rgb = $this->hexToRgb($hex);
        
        $r = $rgb['r'] / 255;
        $g = $rgb['g'] / 255;
        $b = $rgb['b'] / 255;
        
        $r = $r <= 0.03928 ? $r / 12.92 : pow(($r + 0.055) / 1.055, 2.4);
        $g = $g <= 0.03928 ? $g / 12.92 : pow(($g + 0.055) / 1.055, 2.4);
        $b = $b <= 0.03928 ? $b / 12.92 : pow(($b + 0.055) / 1.055, 2.4);
        
        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }
}
