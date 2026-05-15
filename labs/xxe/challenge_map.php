<?php

/**
 * File: /labs/xxe/challenge_map.php
 * Maps level identifiers to challenge classes for XXE lab
 */

$challengeMap = [
  'lvl1' => \Labs\XXE\Challenges\Level1XXE::class
];

// Validate that all mapped classes exist before use
foreach ($challengeMap as $level => $className) {
    if (!class_exists($className)) {
        error_log("XXE Lab: Challenge class not found for {$level}: {$className}");
        // Fallback: remove invalid entry from map
        unset($challengeMap[$level]);
    }
}

return $challengeMap;
