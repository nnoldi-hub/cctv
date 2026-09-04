<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pachete CCTV
    |--------------------------------------------------------------------------
    |
    | Pachete standard afisate pe site-ul public. Preturile sunt orientative
    | ("de la") - oferta finala se stabileste dupa vizita tehnica.
    |
    */

    'tiers' => [
        [
            'key' => 'entry',
            'name' => 'Entry',
            'price_from' => 1490,
            'cameras' => 4,
            'resolution' => '2MP Full HD',
            'storage_days' => 15,
            'features' => [
                '4 camere 2MP (interior/exterior)',
                'NVR 4 canale cu HDD 1TB',
                'Vizualizare live pe telefon',
                'Instalare si configurare incluse',
                'Garantie 12 luni',
            ],
        ],
        [
            'key' => 'medium',
            'name' => 'Medium',
            'price_from' => 2890,
            'cameras' => 8,
            'resolution' => '4MP',
            'storage_days' => 30,
            'features' => [
                '8 camere 4MP (interior/exterior)',
                'NVR 8 canale cu HDD 2TB',
                'Detectie miscare inteligenta',
                'Vizualizare live + notificari push',
                'Instalare si configurare incluse',
                'Garantie 24 luni',
            ],
            'highlight' => true,
        ],
        [
            'key' => 'premium',
            'name' => 'Premium',
            'price_from' => 5490,
            'cameras' => 16,
            'resolution' => '4K',
            'storage_days' => 60,
            'features' => [
                '16 camere 4K (interior/exterior)',
                'NVR 16 canale cu HDD 4TB',
                'Recunoastere faciala si numere inmatriculare',
                'Acces remote securizat pe termen lung',
                'Mentenanta preventiva inclusa 1 an',
                'Garantie 36 luni',
            ],
        ],
    ],

];
