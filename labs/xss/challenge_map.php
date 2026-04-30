<?php

/**
 * File: /labs/xss/challenge_map.php
 */

return [
  // 'lvl0' => 'Labs\\XSS\\Challenges\\TestChallenge',
  'lvl1' => \Labs\XSS\Challenges\Level1ReflectedMilitary::class,
  'lvl2' => \Labs\XSS\Challenges\Level2Stored::class,
  'lvl3' => \Labs\XSS\Challenges\Level3Dom::class,
];