<?php

/**
 * Attacker exfiltration endpoint
 * Simulates a remote server collecting stolen cookies.
 * Sets a session flag to mark challenge as solved.
 */
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (isset($_GET['cookie'])) {
  $stolenCookie = $_GET['cookie'];
  $level = $_GET['level'] ?? '1'; // default to level 1
  // Log the stolen cookie for demo (optional)
  $logEntry = date('[Y-m-d H:i:s]') . " Stolen cookie: " . $stolenCookie . "\n";
  file_put_contents(__DIR__ . '/../storage/logs/xss_hits.log', $logEntry, FILE_APPEND);

  // Mark the appropriate level as solved
  if ($level === '2') {
    $_SESSION['xss_lvl2_solved'] = true;
  } else {
    $_SESSION['xss_lvl1_solved'] = true;
  }
  // Mark this lab as solved
  $_SESSION['xss_lvl1_solved'] = true;

  // Return a 1x1 transparent GIF to avoid breaking the injected page
  header('Content-Type: image/gif');
  echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
  exit;
}

// If accessed directly without cookie parameter
http_response_code(404);
echo 'Not found';