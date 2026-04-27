<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Logo Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for church logo uploads and processing.
    |
    */
    'logo' => [
        'max_size' => 2048, // KB
        'max_dimensions' => [512, 512], // [width, height]
        'allowed_types' => ['png', 'jpg', 'jpeg', 'svg', 'webp'],
        'storage_disk' => 'public',
        'storage_path' => 'church-logos',
    ],

    /*
    |--------------------------------------------------------------------------
    | Theme Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for tenant theme customization.
    |
    */
    'theme' => [
        'default_primary' => '#D4A853', // Ekklesia Gold
        'generate_shades' => true,
        'accessibility_level' => 'AA', // 'AA' or 'AAA'
        'cache_duration' => 3600, // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific branding features.
    |
    */
    'features' => [
        'custom_fonts' => false, // Future feature
        'dark_mode_logo' => true,
        'favicon_generation' => false, // Future feature
        'custom_css' => false, // Future feature
    ],
];
