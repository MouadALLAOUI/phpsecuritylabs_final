<?php

/**
 * MIL-OPS CONTROL SYSTEM - Military Theme Header
 * With dynamic sidebar configuration
 */

// Generate dynamic agent codename
if (!isset($_SESSION['agent_codename'])) {
  $codenames = ['GHOST', 'VIPER', 'PHANTOM', 'ECHO', 'RAVEN', 'SPECTRE', 'TITAN', 'FALCON'];
  $_SESSION['agent_codename'] = $codenames[array_rand($codenames)];
}
$agentCodename = $_SESSION['agent_codename'] ?? 'OPERATOR';

$currentClearance = $_SESSION['clearance_level'] ?? 'LEVEL 2';

// Detect base URL for assets
$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($baseUrl === '/') $baseUrl = '';

// ============================================
// SIDEBAR CONFIGURATION - EASY TO EXTEND
// ============================================
$sidebarMenu = [
  'dashboard' => [
    'page' => 'home',
    'lvl' => null,
    'title' => 'DASHBOARD',
    'icon' => 'tachometer-alt',
    'badge' => null
  ],
  'agents' => [
    'page' => 'sqli',
    'lvl' => 1,
    'title' => 'AGENTS',
    'icon' => 'user-secret',
    'badge' => 'SQLi'
  ],
  'cases' => [
    'page' => 'xss',
    'lvl' => 1,
    'title' => 'CASES',
    'icon' => 'folder-open',
    'badge' => 'XSS'
  ],
  'communications' => [
    'page' => 'xss',
    'lvl' => 3,
    'title' => 'COMMUNICATIONS',
    'icon' => 'envelope',
    'badge' => 'DOM XSS'
  ],
  'reports' => [
    'page' => 'xss',
    'lvl' => 2,
    'title' => 'REPORTS',
    'icon' => 'file-alt',
    'badge' => 'Stored XSS'
  ],
  'secrets' => [
    'page' => 'sqli',
    'lvl' => 2,  // future UNION level
    'title' => 'SECRETS',
    'icon' => 'key',
    'badge' => 'SQLi Union'
  ],
  'audit' => [
    'page' => 'xss_admin_reports',
    'lvl' => null,
    'title' => 'AUDIT LOGS',
    'icon' => 'history',
    'badge' => 'Admin Panel'
  ]
];

// Helper to check if a menu item is active
function isMenuItemActive($item)
{
  $currentPage = $_GET['page'] ?? 'home';
  $currentLvl = $_GET['lvl'] ?? null;

  if ($item['page'] === 'xss_admin_reports') {
    return $currentPage === 'xss_admin_reports';
  }
  return ($currentPage === $item['page'] && ($item['lvl'] === null || $currentLvl == $item['lvl']));
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-mil-dark">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MIL-OPS CONTROL SYSTEM | Classified Terminal</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>/shared/military-ui/mil-ops.css">
  <style>
  body.mil-body {
    background: #0b0f14;
    color: #e0e0e0;
    margin: 0;
    font-family: 'Courier New', monospace;
  }

  .mil-top-bar {
    background: #111820;
    border-bottom: 2px solid #00ff41;
  }

  .mil-sidebar {
    background: #070a0d;
  }

  .mil-main-content {
    margin-left: 260px;
    padding: 20px;
  }
  </style>
</head>

<body class="mil-body">
  <div class="crt-overlay"></div>

  <header class="mil-top-bar">
    <div class="top-bar-left">
      <i class="fas fa-shield-haltered mil-icon"></i>
      <span class="system-title">MIL-OPS CONTROL SYSTEM</span>
      <span class="connection-status secure"><i class="fas fa-lock"></i> SECURE LINK ACTIVE</span>
    </div>
    <div class="top-bar-right">
      <div class="agent-info"><span class="agent-label">AGENT:</span><span
          class="agent-codename"><?= htmlspecialchars($agentCodename) ?></span></div>
      <div class="clearance-info"><span class="clearance-label">CLEARANCE:</span><span
          class="clearance-level"><?= htmlspecialchars($currentClearance) ?></span></div>
      <div class="system-time" id="systemTime">--:--:--</div>
    </div>
  </header>

  <aside class="mil-sidebar">
    <div class="sidebar-header"><i class="fas fa-crosshairs"></i><span>MISSION SELECTOR</span></div>
    <nav class="nav-menu">
      <ul class="nav-list">
        <?php foreach ($sidebarMenu as $item): ?>
        <?php
          // Build URL
          if ($item['page'] === 'xss_admin_reports') {
            $url = $baseUrl . '/?page=xss_admin_reports';
          } else {
            $url = $baseUrl . '/?page=' . $item['page'];
            if ($item['lvl'] !== null) {
              $url .= '&lvl=' . $item['lvl'];
            }
          }
          $isActive = isMenuItemActive($item);
          ?>
        <li class="nav-item <?= $isActive ? 'active' : '' ?>">
          <a href="<?= $url ?>">
            <i class="fas fa-<?= $item['icon'] ?>"></i>
            <?= $item['title'] ?>
            <?php if ($item['badge']): ?>
            <span class="badge"><?= $item['badge'] ?></span>
            <?php endif; ?>
          </a>
        </li>
        <?php endforeach; ?>
      </ul>
    </nav>
    <div class="sidebar-footer">
      <div class="system-log-mini" id="systemLogMini"></div>
      <p class="classification-marking">UNAUTHORIZED ACCESS PROHIBITED</p>
    </div>
  </aside>

  <main class="mil-main-content">