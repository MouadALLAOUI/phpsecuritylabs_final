<?php

/**
 * MIL-OPS CONTROL SYSTEM - Military Theme Header
 * Redesigned into modern Cyber Range Console
 */

// Generate dynamic agent codename (only if not already set in this session)
if (!isset($_SESSION['agent_codename'])) {
  $codenames = ['GHOST', 'VIPER', 'PHANTOM', 'ECHO', 'RAVEN', 'SPECTRE', 'TITAN', 'FALCON'];
  $_SESSION['agent_codename'] = $codenames[array_rand($codenames)];
}
$agentCodename = $_SESSION['agent_codename'] ?? 'OPERATOR';

// Clear agent codename on logout detection
if (isset($_GET['logout']) && isset($_SESSION['agent_codename'])) {
  unset($_SESSION['agent_codename']);
  $agentCodename = 'OPERATOR';
}

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
    'title' => 'AGENTS SQLi',
    'icon' => 'user-secret',
    'badge' => 'SQLi'
  ],
  'cases' => [
    'page' => 'xss',
    'lvl' => 1,
    'title' => 'REFLECTED XSS',
    'icon' => 'folder-open',
    'badge' => 'XSS L1'
  ],
  'communications' => [
    'page' => 'xss',
    'lvl' => 3,
    'title' => 'DOM XSS',
    'icon' => 'envelope',
    'badge' => 'DOM L3'
  ],
  'reports' => [
    'page' => 'xss',
    'lvl' => 2,
    'title' => 'STORED XSS',
    'icon' => 'file-alt',
    'badge' => 'XSS L2'
  ],
  'secrets' => [
    'page' => 'sqli',
    'lvl' => 2,
    'title' => 'SQLi UNION',
    'icon' => 'key',
    'badge' => 'SQLi L2'
  ],
  'uploads' => [
    'page' => 'file_upload',
    'lvl' => 1,
    'title' => 'UPLOADS',
    'icon' => 'upload',
    'badge' => 'File Upload'
  ],
  'csrf-ops' => [
    'page' => 'csrf',
    'lvl' => 1,
    'title' => 'CSRF OPS',
    'icon' => 'exchange-alt',
    'badge' => 'CSRF'
  ],
  'xxe-intel' => [
    'page' => 'xxe',
    'lvl' => 1,
    'title' => 'XXE INTEL',
    'icon' => 'file-code',
    'badge' => 'XXE'
  ],
  'audit' => [
    'page' => 'xss_admin_reports',
    'lvl' => null,
    'title' => 'AUDIT LOGS',
    'icon' => 'history',
    'badge' => 'Admin'
  ]
];

// Helper to check if a menu item is active
if (!function_exists('isMenuItemActive')) {
  function isMenuItemActive($item)
  {
    $currentPage = $_GET['page'] ?? 'home';
    $currentLvl = $_GET['lvl'] ?? null;

    if ($item['page'] === 'xss_admin_reports') {
      return $currentPage === 'xss_admin_reports';
    }
    return ($currentPage === $item['page'] && ($item['lvl'] === null || $currentLvl == $item['lvl']));
  }
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cyber Range Console | Classified Training Area</title>
  <link rel="icon" type="image/svg+xml" href="<?= $baseUrl ?>/public/favicon.svg">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="<?= $baseUrl ?>/shared/military-ui/mil-ops.css">
  
  <style>
    /* Support theme-specific classes in challenge views */
    body.theme-light {
      --bg-main: #f8fafc;
      --bg-surface: #ffffff;
      --bg-card: #f1f5f9;
      --border-color: #cbd5e1;
      --text-primary: #0f172a;
      --text-secondary: #334155;
      --text-dim: #64748b;
    }
    body.theme-dark {
      --bg-main: #030712;
      --bg-surface: #0f172a;
      --bg-card: #1f2937;
      --border-color: #374151;
      --text-primary: #f3f4f6;
      --text-secondary: #d1d5db;
      --text-dim: #9ca3af;
    }
  </style>
</head>

<body class="mil-body theme-military">

  <!-- TOP HEADER CONSOLE -->
  <header class="mil-top-bar">
    <div class="top-bar-left">
      <div class="bg-blue-600/15 p-1.5 rounded border border-blue-500/20 flex items-center justify-center">
        <i class="fas fa-crosshairs text-blue-500"></i>
      </div>
      <span class="system-title">Cyber Range Console</span>
      <span class="connection-status secure"><i class="fas fa-circle text-[8px]"></i> SECURE LINK ACTIVE</span>
    </div>
    
    <div class="top-bar-right">
      <!-- Language Selector -->
      <select id="languageSelector" onchange="changeLanguage(this.value)" 
        class="bg-slate-900 text-slate-300 border border-slate-700 rounded px-2 py-1 text-xs focus:outline-none focus:border-blue-500 cursor-pointer">
        <option value="en" <?= (($_SESSION['lang'] ?? $_COOKIE['lang'] ?? 'en') === 'en') ? 'selected' : '' ?>>EN</option>
        <option value="fr" <?= (($_SESSION['lang'] ?? $_COOKIE['lang'] ?? 'en') === 'fr') ? 'selected' : '' ?>>FR</option>
      </select>
      
      <!-- Theme Toggle -->
      <button onclick="toggleTheme()" aria-label="Cycle theme"
        class="bg-slate-900 text-slate-300 border border-slate-700 rounded p-1 hover:bg-slate-800 focus:outline-none flex items-center justify-center">
        <i class="fas fa-adjust text-xs px-1"></i>
      </button>
      
      <div class="agent-info hidden sm:flex">
        <span class="agent-label">AGENT:</span>
        <span class="agent-codename"><?= htmlspecialchars($agentCodename) ?></span>
      </div>
      
      <div class="clearance-info hidden sm:flex">
        <span class="clearance-label">CLEARANCE:</span>
        <span class="clearance-level"><?= htmlspecialchars($currentClearance) ?></span>
      </div>
      
      <div class="system-time" id="systemTime">--:--:--</div>
    </div>
  </header>

  <!-- SIDEBAR SELECTOR -->
  <aside class="mil-sidebar">
    <div class="sidebar-header">
      <i class="fas fa-terminal"></i>
      <span>Mission Selector</span>
    </div>
    
    <nav class="nav-menu">
      <ul class="nav-list">
        <?php foreach ($sidebarMenu as $item): ?>
        <?php
          // Build challenge navigation URL
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
            <span><?= $item['title'] ?></span>
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
      <p class="classification-marking">CLASSIFIED OPERATIONS AREA</p>
    </div>
  </aside>

  <!-- Content starts here -->
  <main class="mil-main-content">