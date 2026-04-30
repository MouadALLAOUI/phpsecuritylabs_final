<?php

namespace Labs\XXE;

return [
  'lvl1' => [
    'class' => \Labs\XXE\Challenges\Level1XXE::class,
    'title' => 'XML Intelligence Parser',
    'description' => 'Exploit an XML External Entity vulnerability to extract sensitive server files.',
    'difficulty' => 'Medium',
    'points' => 150
  ]
];
