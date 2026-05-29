<?php

/**
 * Landing Page View – Home / Dashboard
 * Loaded by the Router when ?page=home
 */

include_once ROOT . '/shared/header.php';
include_once ROOT . '/shared/sidebar.php';

// Get user progress if logged in
$totalCompleted = 0;
$labProgress = [];
if (isset($_SESSION['user_id'])) {
    $auth = new \App\Core\Auth();
    $userId = $_SESSION['user_id'];
    $completed = $auth->getCompletedChallenges($userId);
    $totalCompleted = count($completed);
    
    // Group by lab
    foreach ($completed as $c) {
        $lab = $c['lab_name'];
        if (!isset($labProgress[$lab])) {
            $labProgress[$lab] = 0;
        }
        $labProgress[$lab]++;
    }
}
?>

<div class="lg:ml-64 p-6 min-h-[85vh] theme-transition">
  <div class="max-w-5xl mx-auto space-y-8">
    
    <!-- Welcome Header Section -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-lg flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
      <div>
        <div class="flex items-center space-x-3">
          <span class="bg-blue-600/10 p-2 rounded-lg border border-blue-500/20 text-blue-500"><i class="fas fa-shield-halved text-lg"></i></span>
          <h1 class="text-xl font-bold text-slate-100 uppercase tracking-wide">Defense Intelligence Dashboard</h1>
        </div>
        <p class="mt-2 text-xs text-slate-400">
          Welcome back, Operator <span class="font-bold text-blue-400"><?= htmlspecialchars($_SESSION['username'] ?? 'Agent') ?></span>.
        </p>
      </div>
      <div class="flex items-center space-x-2 bg-slate-950 px-3.5 py-1.5 rounded-lg border border-slate-850">
        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
        <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest font-mono">Status: Secure Sandbox</span>
      </div>
    </div>

    <!-- System Status Panel -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- System operational -->
      <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 shadow-md flex items-center gap-4">
        <div class="h-12 w-12 rounded-lg bg-teal-500/10 border border-teal-500/20 flex items-center justify-center text-teal-400">
          <i class="fas fa-server text-xl"></i>
        </div>
        <div>
          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Platform State</p>
          <p class="text-sm font-semibold text-slate-200 mt-0.5">All Systems Active</p>
        </div>
      </div>

      <!-- Active labs count -->
      <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 shadow-md flex items-center gap-4">
        <div class="h-12 w-12 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
          <i class="fas fa-flask text-xl"></i>
        </div>
        <div>
          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Active Modules</p>
          <p class="text-sm font-semibold text-slate-200 mt-0.5">5 Core Training Labs</p>
        </div>
      </div>

      <!-- Challenges Completed -->
      <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 shadow-md flex items-center gap-4">
        <div class="h-12 w-12 rounded-lg bg-yellow-500/10 border border-yellow-500/20 flex items-center justify-center text-yellow-400">
          <i class="fas fa-trophy text-xl"></i>
        </div>
        <div>
          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Operator Achievements</p>
          <p class="text-sm font-semibold text-slate-200 mt-0.5"><?= $totalCompleted ?> Lab Tasks Completed</p>
        </div>
      </div>
    </div>

    <!-- Interactive Progress Section -->
    <?php if ($totalCompleted > 0): ?>
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-md">
      <h2 class="text-sm font-bold text-slate-100 uppercase tracking-wider mb-5 flex items-center gap-2">
        <i class="fas fa-chart-line text-blue-500"></i> Operational Performance Tracker
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <?php foreach ($labProgress as $lab => $count): ?>
        <?php
          $total_for_lab = ($lab === 'xss') ? 3 : (($lab === 'sqli' || $lab === 'file_upload' || $lab === 'csrf' || $lab === 'xxe') ? 2 : 1);
          $percentage = min(100, round(($count / $total_for_lab) * 100));
        ?>
        <div class="bg-slate-950/40 border border-slate-850 p-4 rounded-lg">
          <div class="flex justify-between items-center mb-2">
            <span class="text-xs font-semibold text-slate-200 uppercase tracking-wide"><?= htmlspecialchars(str_replace('_', ' ', $lab)) ?></span>
            <span class="text-xs font-mono font-bold text-blue-400"><?= $count ?> / <?= $total_for_lab ?> (<?= $percentage ?>%)</span>
          </div>
          <div class="w-full bg-slate-800 rounded-full h-2">
            <div class="bg-blue-500 h-2 rounded-full transition-all duration-500" style="width: <?= $percentage ?>%"></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Command Directives Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      
      <!-- Quick Tactical Actions -->
      <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-md">
        <h2 class="text-sm font-bold text-slate-100 uppercase tracking-wider mb-4 flex items-center gap-2">
          <i class="fas fa-bolt text-blue-500"></i> Dispatch Center
        </h2>
        <div class="space-y-3">
          <a href="?page=labs" class="flex items-center justify-between p-3.5 bg-slate-950/60 border border-slate-800 hover:border-slate-700 rounded-lg group transition">
            <div class="flex items-center">
              <i class="fas fa-flask text-teal-400 w-5 mr-3 flex items-center text-sm"></i>
              <span class="text-xs font-semibold text-slate-300">Catalog of Security Modules</span>
            </div>
            <i class="fas fa-chevron-right text-[10px] text-slate-500 group-hover:text-slate-300 group-hover:translate-x-0.5 transition"></i>
          </a>
          <a href="?page=xss&lvl=1" class="flex items-center justify-between p-3.5 bg-slate-950/60 border border-slate-800 hover:border-slate-700 rounded-lg group transition">
            <div class="flex items-center">
              <i class="fas fa-code text-amber-400 w-5 mr-3 flex items-center text-sm"></i>
              <span class="text-xs font-semibold text-slate-300">Cross-Site Scripting (XSS L1)</span>
            </div>
            <i class="fas fa-chevron-right text-[10px] text-slate-500 group-hover:text-slate-300 group-hover:translate-x-0.5 transition"></i>
          </a>
          <a href="?page=sqli&lvl=1" class="flex items-center justify-between p-3.5 bg-slate-950/60 border border-slate-800 hover:border-slate-700 rounded-lg group transition">
            <div class="flex items-center">
              <i class="fas fa-database text-red-500 w-5 mr-3 flex items-center text-sm"></i>
              <span class="text-xs font-semibold text-slate-300">SQL Injection Lab (L1)</span>
            </div>
            <i class="fas fa-chevron-right text-[10px] text-slate-500 group-hover:text-slate-300 group-hover:translate-x-0.5 transition"></i>
          </a>
        </div>
      </div>

      <!-- Active Directives Overview -->
      <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-md">
        <h2 class="text-sm font-bold text-slate-100 uppercase tracking-wider mb-4 flex items-center gap-2">
          <i class="fas fa-bars-progress text-blue-500"></i> Operation Directives
        </h2>
        <div class="space-y-3">
          <div class="flex items-center justify-between p-3.5 bg-slate-950/40 border border-slate-850 rounded-lg">
            <div class="flex items-center">
              <i class="fas fa-code text-amber-500 w-5 mr-3 text-sm flex items-center"></i>
              <span class="text-xs font-semibold text-slate-300">XSS Attack Modules</span>
            </div>
            <span class="text-[10px] font-bold px-2 py-0.5 bg-slate-800 text-slate-300 rounded border border-slate-700 uppercase tracking-wide">3 Stages</span>
          </div>
          <div class="flex items-center justify-between p-3.5 bg-slate-950/40 border border-slate-850 rounded-lg">
            <div class="flex items-center">
              <i class="fas fa-database text-red-500 w-5 mr-3 text-sm flex items-center"></i>
              <span class="text-xs font-semibold text-slate-300">SQL Database Injections</span>
            </div>
            <span class="text-[10px] font-bold px-2 py-0.5 bg-slate-800 text-slate-300 rounded border border-slate-700 uppercase tracking-wide">2 Stages</span>
          </div>
          <div class="flex items-center justify-between p-3.5 bg-slate-950/40 border border-slate-850 rounded-lg">
            <div class="flex items-center">
              <i class="fas fa-upload text-emerald-500 w-5 mr-3 text-sm flex items-center"></i>
              <span class="text-xs font-semibold text-slate-300">File Infiltration Uploads</span>
            </div>
            <span class="text-[10px] font-bold px-2 py-0.5 bg-slate-800 text-slate-300 rounded border border-slate-700 uppercase tracking-wide">2 Stages</span>
          </div>
        </div>
      </div>

    </div>

  </div>
</div>

<?php
include_once ROOT . '/shared/footer.php';
?>
