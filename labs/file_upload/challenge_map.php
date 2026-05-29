<?php

/**
 * File: /labs/file_upload/challenge_map.php
 * Maps level identifiers to challenge classes for File Upload lab
 */

$challengeMap = [
    'lvl1' => \Labs\FileUpload\Challenges\Level1ExtensionBypass::class,
    '1'    => \Labs\FileUpload\Challenges\Level1ExtensionBypass::class,
    'lvl2' => \Labs\FileUpload\Challenges\Level2MimeBypass::class,
    '2'    => \Labs\FileUpload\Challenges\Level2MimeBypass::class,
];

// Validate that all mapped classes exist before use
foreach ($challengeMap as $level => $className) {
    if (!class_exists($className)) {
        error_log("FileUpload Lab: Challenge class not found for {$level}: {$className}");
        // Fallback: remove invalid entry from map
        unset($challengeMap[$level]);
    }
}

return $challengeMap;