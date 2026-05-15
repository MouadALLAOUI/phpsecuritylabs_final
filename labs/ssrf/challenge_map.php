<?php

/**
 * SSRF Lab Challenge Map
 * Maps challenge levels to their corresponding classes
 */

return [
    'lvl1' => [
        'name' => 'Basic SSRF',
        'class' => '\\Labs\\SSRF\\Challenges\\Level1BasicSSRF',
        'description' => 'Exploit server-side request forgery to access internal services'
    ],
    'lvl2' => [
        'name' => 'Advanced SSRF',
        'class' => '\\Labs\\SSRF\\Challenges\\Level2AdvancedSSRF',
        'description' => 'Bypass filters and exploit SSRF to read internal metadata'
    ]
];
