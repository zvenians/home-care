<?php
return [
    // Demo access must never be active on a publicly deployed production instance.
    'demo_mode' => (bool) env('HOMECARE_DEMO_MODE', false),
    'free_radius_km' => 5,
    'additional_km_fee' => 4500,
    'night_surcharge_pct' => 20,
    'weekend_surcharge_pct' => 10,
    'urgent_fee' => 75000,
    'platform_fee' => 5000,
    'addons' => ['care_kit' => ['name' => 'Care kit sekali pakai', 'price' => 40000], 'report' => ['name' => 'Laporan perawatan digital', 'price' => 15000], 'companion' => ['name' => 'Pendamping keluarga', 'price' => 90000]],
];
