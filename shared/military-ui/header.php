<?php
/**
 * MIL-OPS CONTROL SYSTEM - Military Theme Header
 * Replaces standard header for lab interfaces
 * Each lab becomes a classified mission terminal
 */

// Generate dynamic agent codename
if (!isset($_SESSION['agent_codename'])) {
    $codenames = ['GHOST', 'VIPER', 'PHANTOM', 'ECHO', 'RAVEN', 'SPECTRE', 'TITAN', 'FALCON'];
    $_SESSION['agent_codename'] = $codenames[array_rand($codenames)];
}
$agentCodename = $_SESSION['agent_codename'] ?? 'OPERATOR';

// Clearance levels
$clearanceLevels = ['LEVEL 1', 'LEVEL 2', 'LEVEL 3', 'LEVEL 4', 'TOP SECRET'];
$currentClearance = $_SESSION['clearance_level'] ?? 'LEVEL 2';
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-mil-dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MIL-OPS CONTROL SYSTEM | Classified Terminal</title>
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  
  <!-- Military UI Styles -->
  <link rel="stylesheet" href="/shared/military-ui/mil-ops.css">
</head>

<body class="mil-body">
  <!-- CRT Scanline Overlay -->
  <div class="crt-overlay"></div>
  
  <!-- TOP BAR - MIL-OPS CONTROL SYSTEM -->
  <header class="mil-top-bar">
    <div class="top-bar-left">
      <i class="fas fa-shield-haltered mil-icon"></i>
      <span class="system-title">MIL-OPS CONTROL SYSTEM</span>
      <span class="connection-status secure">
        <i class="fas fa-lock"></i> SECURE LINK ACTIVE
      </span>
    </div>
    
    <div class="top-bar-right">
      <div class="agent-info">
        <span class="agent-label">AGENT:</span>
        <span class="agent-codename"><?= htmlspecialchars($agentCodename) ?></span>
      </div>
      <div class="clearance-info">
        <span class="clearance-label">CLEARANCE:</span>
        <span class="clearance-level"><?= htmlspecialchars($currentClearance) ?></span>
      </div>
      <div class="system-time" id="systemTime">--:--:--</div>
    </div>
  </header>
  
  <!-- SIDEBAR - MISSION SELECTOR -->
  <aside class="mil-sidebar">
    <div class="sidebar-header">
      <i class="fas fa-crosshairs"></i>
      <span>MISSION SELECTOR</span>
    </div>
    
    <nav class="mission-nav">
      <a href="?page=home" class="mission-link">
        <i class="fas fa-chevron-right"></i>
        <span>COMMAND CENTER</span>
      </a>
      
      <div class="nav-section-title">ACTIVE OPERATIONS</div>
      
      <a href="?page=xss" class="mission-link active">
        <i class="fas fa-code"></i>
        <span>SIGNAL INTERCEPT (XSS)</span>
        <span class="mission-status status-active"></span>
      </a>
      
      <a href="?page=sqli" class="mission-link">
        <i class="fas fa-database"></i>
        <span>DATABASE BREACH (SQLi)</span>
        <span class="mission-status status-active"></span>
      </a>
      
      <a href="?page=file_upload" class="mission-link">
        <i class="fas fa-upload"></i>
        <span>INTEL UPLOAD (File)</span>
        <span class="mission-status status-active"></span>
      </a>
      
      <div class="nav-section-title">CLASSIFIED OPERATIONS</div>
      
      <a href="#" class="mission-link locked">
        <i class="fas fa-lock"></i>
        <span>CSRF ATTACK</span>
        <span class="mission-status status-locked"></span>
      </a>
      
      <a href="#" class="mission-link locked">
        <i class="fas fa-lock"></i>
        <span>SESSION HIJACK</span>
        <span class="mission-status status-locked"></span>
      </a>
      
      <a href="#" class="mission-link locked">
        <i class="fas fa-lock"></i>
        <span>XXE INJECTION</span>
        <span class="mission-status status-locked"></span>
      </a>
    </nav>
    
    <div class="sidebar-footer">
      <div class="system-log-mini" id="systemLogMini">
        <div class="log-entry"><span class="log-time">--:--:--</span> SYSTEM INITIALIZED</div>
      </div>
      <p class="classification-marking">UNAUTHORIZED ACCESS PROHIBITED</p>
    </div>
  </aside>
  
  <!-- MAIN CONTENT AREA -->
  <main class="mil-main-content">
