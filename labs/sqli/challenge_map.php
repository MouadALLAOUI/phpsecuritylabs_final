<?php

/**
 * File: /labs/sqli/challenge_map.php
 * Maps level identifiers to challenge classes for SQL Injection lab
 */

return [
  'lvl1' => \Labs\SQLi\Challenges\Level1AuthBypass::class,
  'lvl2' => \Labs\SQLi\Challenges\Level2UnionExtraction::class,
];
