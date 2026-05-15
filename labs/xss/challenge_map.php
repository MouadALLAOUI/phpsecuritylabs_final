<?php

/**
 * File: /labs/xss/challenge_map.php
 * Maps challenge levels to their corresponding PHP classes
 */

$challengeMap = [
  // 'lvl0' => 'Labs\\XSS\\Challenges\\TestChallenge',
  'lvl1' => \Labs\XSS\Challenges\Level1ReflectedMilitary::class,
  'lvl2' => \Labs\XSS\Challenges\Level2Stored::class,
  'lvl3' => \Labs\XSS\Challenges\Level3Dom::class,
];

// Validate that all mapped classes exist before use
foreach ($challengeMap as $level => $className) {
    if (!class_exists($className)) {
        error_log("XSS Lab: Challenge class not found for {$level}: {$className}");
        // Fallback: remove invalid entry from map
        unset($challengeMap[$level]);
    }
}

return $challengeMap;