<?php
/**
 * Landing Page View – Home / Dashboard
 * Progress totals and per-lab bars are driven by LabCatalog.
 */

use App\Core\LabCatalog;

include_once ROOT . '/shared/header.php';
include_once ROOT . '/shared/sidebar.php';

// User progress
$totalCompleted = 0;
$labProgress    = [];
if (isset($_SESSION['user_id'])) {
    $auth        = new \App\Core\Auth();
    $labProgress = $auth->getCompletedChallenges(); // ['xss' => ['count' => 2, ...], ...]
    foreach ($labProgress as $data) {
        $totalCompleted += (int)$data['count'];
    }
}

// Catalog
$catalog    = LabCatalog::all();
$colors     = LabCatalog::$colorMap;
$labCount   = count($catalog);
$grandTotal = LabCatalog::grandTotal();
?>

<div class="lg:ml-64 p-6 min-h-[85vh] theme-transition">
  <div class="max-w-5xl mx-auto space-y-8">

    <!-- Welcome Header -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-lg flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
      <div>
        <div class="flex items-center space-x-3">
          <span class="bg-blue-600/10 p-2 rounded-lg border border-blue-500/20 text-blue-500"><i class="fas fa-shield-alt text-lg"></i></span>
          <h1 class="text-xl font-bold text-slate-100 uppercase tracking-wide">Defense Intelligence Dashboard</h1>
        </div>
        <p class="mt-2 text-xs text-slate-400">
          Welcome back, Operator <span class="font-bold text-blue-400"><?= e($_SESSION['username'] ?? 'Agent') ?></span>.
        </p>
      </div>
      <div class="flex items-center space-x-2 bg-slate-950 px-3.5 py-1.5 rounded-lg border border-slate-850">
        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
        <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest font-mono">Status: Secure Sandbox</span>
      </div>
    </div>

    <!-- KPI Strip -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 shadow-md flex items-center gap-4">
        <div class="h-12 w-12 rounded-lg bg-teal-500/10 border border-teal-500/20 flex items-center justify-center text-teal-400">
          <i class="fas fa-server text-xl"></i>
        </div>
        <div>
          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Platform State</p>
          <p class="text-sm font-semibold text-slate-200 mt-0.5">All Systems Active</p>
        </div>
      </div>

      <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 shadow-md flex items-center gap-4">
        <div class="h-12 w-12 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
          <i class="fas fa-flask text-xl"></i>
        </div>
        <div>
          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Active Modules</p>
          <p class="text-sm font-semibold text-slate-200 mt-0.5"><?= $labCount ?> Training Labs &mdash; <?= $grandTotal ?> Challenges</p>
        </div>
      </div>

      <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 shadow-md flex items-center gap-4">
        <div class="h-12 w-12 rounded-lg bg-yellow-500/10 border border-yellow-500/20 flex items-center justify-center text-yellow-400">
          <i class="fas fa-trophy text-xl"></i>
        </div>
        <div>
          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Operator Achievements</p>
          <p class="text-sm font-semibold text-slate-200 mt-0.5">
            <?= $totalCompleted ?> / <?= $grandTotal ?> Completed
            <?php if ($grandTotal > 0): ?>
            <span class="text-[10px] text-blue-400 ml-1">(<?= round(($totalCompleted / $grandTotal) * 100) ?>%)</span>
            <?php endif; ?>
          </p>
        </div>
      </div>
    </div>

    <!-- Per-lab Progress Tracker -->
    <?php if (!empty($catalog)): ?>
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-md">
      <h2 class="text-sm font-bold text-slate-100 uppercase tracking-wider mb-5 flex items-center gap-2">
        <i class="fas fa-chart-line text-blue-500"></i> Operational Performance Tracker
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php foreach ($catalog as $slug => $lab):
          $c        = $colors[$lab['color']] ?? $colors['blue'];
          $done     = (int)($labProgress[$slug]['count'] ?? 0);
          $total    = $lab['total'];
          $pct      = $total > 0 ? min(100, round(($done / $total) * 100)) : 0;
          $finished = $done >= $total && $total > 0;
        ?>
        <div class="bg-slate-950/40 border border-slate-850 p-4 rounded-lg">
          <div class="flex justify-between items-center mb-2">
            <div class="flex items-center gap-2">
              <i class="fas <?= e($lab['icon']) ?> text-xs <?= $c['text'] ?>"></i>
              <span class="text-xs font-semibold text-slate-200 uppercase tracking-wide"><?= e($lab['short']) ?></span>
            </div>
            <span class="text-xs font-mono font-bold <?= $finished ? 'text-teal-400' : 'text-blue-400' ?>">
              <?= $done ?>/<?= $total ?> (<?= $pct ?>%)<?= $finished ? ' ✓' : '' ?>
            </span>
          </div>
          <div class="w-full bg-slate-800 rounded-full h-2">
            <div class="<?= $finished ? 'bg-teal-500' : 'bg-blue-500' ?> h-2 rounded-full transition-all duration-500" style="width: <?= $pct ?>%"></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Quick Links + Directives Overview -->
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
          <?php
          // Show the first 2 labs as quick links
          $quick = array_slice($catalog, 0, 2, true);
          foreach ($quick as $slug => $lab):
            $c = $colors[$lab['color']] ?? $colors['blue'];
          ?>
          <a href="?page=<?= e($slug) ?>&lvl=1" class="flex items-center justify-between p-3.5 bg-slate-950/60 border border-slate-800 hover:border-slate-700 rounded-lg group transition">
            <div class="flex items-center">
              <i class="fas <?= e($lab['icon']) ?> <?= $c['text'] ?> w-5 mr-3 flex items-center text-sm"></i>
              <span class="text-xs font-semibold text-slate-300"><?= e($lab['title']) ?> (L1)</span>
            </div>
            <i class="fas fa-chevron-right text-[10px] text-slate-500 group-hover:text-slate-300 group-hover:translate-x-0.5 transition"></i>
          </a>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Operation Directives summary -->
      <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-md">
        <h2 class="text-sm font-bold text-slate-100 uppercase tracking-wider mb-4 flex items-center gap-2">
          <i class="fas fa-bars-progress text-blue-500"></i> Operation Directives
        </h2>
        <div class="space-y-3">
          <?php foreach (array_slice($catalog, 0, 4, true) as $slug => $lab):
            $c = $colors[$lab['color']] ?? $colors['blue'];
          ?>
          <div class="flex items-center justify-between p-3.5 bg-slate-950/40 border border-slate-850 rounded-lg">
            <div class="flex items-center">
              <i class="fas <?= e($lab['icon']) ?> <?= $c['text'] ?> w-5 mr-3 text-sm flex items-center"></i>
              <span class="text-xs font-semibold text-slate-300"><?= e($lab['title']) ?></span>
            </div>
            <span class="text-[10px] font-bold px-2 py-0.5 bg-slate-800 text-slate-300 rounded border border-slate-700 uppercase tracking-wide"><?= $lab['total'] ?> Stage<?= $lab['total'] !== 1 ? 's' : '' ?></span>
          </div>
          <?php endforeach; ?>
          <?php if (count($catalog) > 4): ?>
          <a href="?page=labs" class="block text-center text-xs text-blue-400 hover:text-blue-300 font-semibold pt-1 transition">
            + <?= count($catalog) - 4 ?> more modules &rarr;
          </a>
          <?php endif; ?>
        </div>
      </div>

    </div>

  </div>
</div>

<?php include_once ROOT . '/shared/footer.php'; ?>
