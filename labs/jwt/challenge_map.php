<?php

/**
 * JWT Lab Challenge Map
 */

return [
    'lvl1' => [
        'name' => 'Basic JWT Attack',
        'class' => '\\Labs\\JWT\\Challenges\\Level1Basic',
        'description' => 'Exploit weak JWT implementation to gain unauthorized access'
    ],
    'lvl2' => [
        'name' => 'Advanced JWT Attack',
        'class' => '\\Labs\\JWT\\Challenges\\Level2Advanced',
        'description' => 'Bypass JWT signature verification and algorithm confusion'
    ]
];
