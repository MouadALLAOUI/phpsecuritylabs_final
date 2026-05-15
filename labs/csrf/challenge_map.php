<?php

/**
 * File: /labs/csrf/challenge_map.php
 * Maps level identifiers to challenge classes for CSRF lab
 */

$challengeMap = [
  'lvl1' => \Labs\CSRF\Challenges\Level1CSRF::class
];

// Validate that all mapped classes exist before use
foreach ($challengeMap as $level => $className) {
    if (!class_exists($className)) {
        error_log("CSRF Lab: Challenge class not found for {$level}: {$className}");
        // Fallback: remove invalid entry from map
        unset($challengeMap[$level]);
    }
}

return $challengeMap;
