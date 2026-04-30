<?php

namespace Labs\CSRF;

return [
  'lvl1' => [
    'class' => \Labs\CSRF\Challenges\Level1CSRF::class,
    'title' => 'Funds Transfer CSRF',
    'description' => 'Exploit a cross-site request forgery vulnerability in a funds transfer system.',
    'difficulty' => 'Easy',
    'points' => 100
  ]
];
