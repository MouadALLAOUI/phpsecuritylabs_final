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
  $logEntry = date('[Y-m-d H:i:s]') . " [LEVEL $level] Stolen cookie: " . $stolenCookie . "\n";
  file_put_contents(__DIR__ . '/../storage/logs/xss_hits.log', $logEntry, FILE_APPEND);

  // Mark the appropriate level as solved based on level parameter
  if ($level === '3') {
    $_SESSION['xss_lvl3_solved'] = true;
  } elseif ($level === '2') {
    $_SESSION['xss_lvl2_solved'] = true;
  } else {
    $_SESSION['xss_lvl1_solved'] = true;
  }

  // For level 3, redirect back to challenge with success status
  if ($level === '3') {
    header('Location: /challenge.php?page=lvl3&status=complete');
    exit;
  }

  // Return a 1x1 transparent GIF to avoid breaking the injected page
  header('Content-Type: image/gif');
  echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
  exit;
}

// If accessed directly without cookie parameter - show collected data
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>ATTACKER SERVER - Cookie Collection Point</title>
  <style>
    body {
      background: #0a0f14;
      color: #00ff41;
      font-family: 'Courier New', monospace;
      padding: 40px;
    }
    h1 {
      border-bottom: 2px solid #00ff41;
      padding-bottom: 15px;
      text-shadow: 0 0 10px rgba(0, 255, 65, 0.5);
    }
    .log-entry {
      background: rgba(0, 255, 65, 0.05);
      border: 1px solid #333;
      padding: 15px;
      margin: 10px 0;
      word-break: break-all;
    }
    .timestamp {
      color: #666;
    }
    .cookie-data {
      color: #ff9500;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <h1>◈ COOKIE COLLECTION POINT</h1>
  <p>Waiting for incoming data streams...</p>
  
  <?php
  $logFile = __DIR__ . '/../storage/logs/xss_hits.log';
  if (file_exists($logFile)) {
    $logs = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!empty($logs)) {
      echo '<h2>INTERCEPTED SESSIONS:</h2>';
      foreach (array_reverse($logs) as $log) {
        echo '<div class="log-entry">';
        if (preg_match('/\[(.*?)\] \[LEVEL (\d+)\] Stolen cookie: (.+)/', $log, $matches)) {
          echo '<span class="timestamp">' . htmlspecialchars($matches[1]) . '</span>';
          echo '<span style="color: #ff9500; margin-left: 15px;">[LEVEL ' . htmlspecialchars($matches[2]) . ']</span>';
          echo '<div class="cookie-data">' . htmlspecialchars($matches[3]) . '</div>';
        } else {
          echo htmlspecialchars($log);
        }
        echo '</div>';
      }
    }
  }
  ?>
</body>
</html>