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
    ]
];
