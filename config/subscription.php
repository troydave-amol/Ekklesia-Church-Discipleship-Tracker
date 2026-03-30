<?php

declare(strict_types=1);

/**
 * Monthly subscription tiers for the church discipleship tracker (central SaaS).
 * Amounts are in PHP (₱) per church per month unless noted.
 */
return [
    'currency' => 'PHP',
    'currency_symbol' => '₱',

    'plans' => [
        'free' => [
            'label' => 'Free',
            'price_monthly' => 0,
            'price_display' => '₱0',
            'programs_max' => 1,
            'programs_note' => 'Life Class only',
            'disciples_max' => 20,
            'leaders_max' => 5,
            'features' => [
                'Basic attendance & lesson tracking',
                'Manual progress tracking',
            ],
        ],
        'basic' => [
            'label' => 'Basic',
            'price_monthly' => 800,
            'price_display' => '₱800',
            'programs_max' => 4,
            'disciples_max' => 50,
            'leaders_max' => 5,
            'features' => [
                'All 4 programs',
                'Attendance & devotional tracking',
                'Basic hierarchical dashboard',
            ],
        ],
        'standard' => [
            'label' => 'Standard',
            'price_monthly' => 1500,
            'price_display' => '₱1,500',
            'programs_max' => 4,
            'disciples_max' => null,
            'disciples_note' => 'Unlimited disciples (small church)',
            'leaders_max' => 10,
            'features' => [
                'Dashboard with program completion summary',
                'Sheets export of attendance & devotionals',
            ],
        ],
        'premium' => [
            'label' => 'Premium',
            'price_monthly' => 2500,
            'price_display' => '₱2,500',
            'programs_max' => 4,
            'disciples_max' => null,
            'leaders_max' => null,
            'features' => [
                'Bulk import of disciples',
                'Historical reports (per program)',
                'Priority support',
            ],
        ],
    ],
];
