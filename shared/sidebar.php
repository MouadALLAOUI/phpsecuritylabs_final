<?php
/**
 * Sidebar navigation – auto-generated from LabCatalog.
 * Replaces the previous static HTML list of lab links.
 */

use App\Core\LabCatalog;

$currentPage = $_GET['page'] ?? 'home';
$currentLvl  = $_GET['lvl']  ?? null;

$catalog = LabCatalog::all();
$colors  = LabCatalog::$colorMap;
?>
<aside class="hidden lg:block lg:fixed lg:inset-y-0 lg:left-0 lg:w-64 lg:z-40 bg-slate-900 border-r border-slate-800 shadow-xl theme-transition">
  <div class="flex flex-col h-full">

    <!-- Sidebar Header Brand -->
    <div class="flex items-center h-16 px-5 border-b border-slate-800">
      <i class="fas fa-crosshairs text-blue-500 text-lg mr-3 animate-pulse"></i>
      <span class="text-sm font-bold text-slate-100 tracking-wider uppercase">Mission Control</span>
    </div>

    <!-- Navigation List -->
    <nav class="flex-1 overflow-y-auto mt-4 px-3 space-y-1.5 scrollbar-thin">

      <!-- Core navigation -->
      <div class="space-y-1">
        <a href="?page=home"
          class="flex items-center px-4 py-2.5 rounded-lg text-xs font-semibold tracking-wide transition group <?= $currentPage === 'home' ? 'bg-blue-600/15 text-blue-400 border-l-2 border-blue-500' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' ?>">
          <i class="fas fa-tachometer-alt w-5 h-5 mr-3 flex items-center justify-center text-sm <?= $currentPage === 'home' ? 'text-blue-400' : 'text-slate-400 group-hover:text-blue-400' ?>"></i>
          Dashboard
        </a>

        <a href="?page=labs"
          class="flex items-center px-4 py-2.5 rounded-lg text-xs font-semibold tracking-wide transition group <?= $currentPage === 'labs' ? 'bg-blue-600/15 text-blue-400 border-l-2 border-blue-500' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' ?>">
          <i class="fas fa-flask w-5 h-5 mr-3 flex items-center justify-center text-sm text-slate-400 group-hover:text-blue-400"></i>
          All Training Labs
        </a>

        <a href="?page=leaderboard"
          class="flex items-center px-4 py-2.5 rounded-lg text-xs font-semibold tracking-wide transition group <?= $currentPage === 'leaderboard' ? 'bg-blue-600/15 text-blue-400 border-l-2 border-blue-500' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' ?>">
          <i class="fas fa-trophy w-5 h-5 mr-3 flex items-center justify-center text-sm text-yellow-500"></i>
          Leaderboard
        </a>
      </div>

      <!-- Dynamically-generated lab links -->
      <div class="pt-5 pb-2">
        <p class="px-4 text-[10px] font-bold uppercase tracking-widest text-slate-500">Active Directives</p>
      </div>

      <div class="space-y-1">
        <?php foreach ($catalog as $slug => $lab):
          $c       = $colors[$lab['color']] ?? $colors['blue'];
          $isActive = $currentPage === $slug;
        ?>
        <a href="?page=<?= e($slug) ?>&lvl=1"
          class="flex items-center px-4 py-2.5 rounded-lg text-xs font-semibold tracking-wide transition group <?= $isActive ? 'bg-blue-600/15 text-blue-400 border-l-2 border-blue-500' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' ?>">
          <i class="fas <?= e($lab['icon']) ?> w-5 h-5 mr-3 flex items-center justify-center text-sm <?= $isActive ? 'text-blue-400' : $c['text'] . ' opacity-80 group-hover:opacity-100' ?>"></i>
          <?= e($lab['short']) ?> Lab
          <?php if ($lab['total'] > 1): ?>
          <span class="ml-auto text-[9px] font-mono text-slate-500"><?= $lab['total'] ?>L</span>
          <?php endif; ?>
        </a>
        <?php endforeach; ?>
      </div>

      <!-- Admin panel (admins only) -->
      <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
      <div class="pt-6 pb-2">
        <p class="px-4 text-[10px] font-bold uppercase tracking-widest text-red-500">Command Control</p>
      </div>
      <div class="space-y-1">
        <a href="?page=admin"
          class="flex items-center px-4 py-2.5 rounded-lg text-xs font-semibold tracking-wide transition group <?= $currentPage === 'admin' ? 'bg-red-950/20 text-red-400 border-l-2 border-red-600' : 'text-slate-400 hover:bg-red-950/10 hover:text-red-300' ?> border border-red-900/30">
          <i class="fas fa-user-shield w-5 h-5 mr-3 flex items-center justify-center text-sm text-red-400"></i>
          Admin Panel
        </a>
      </div>
      <?php endif; ?>

    </nav>

    <!-- Sidebar Footer -->
    <div class="px-5 py-4 border-t border-slate-800 bg-slate-950/40">
      <p class="text-[10px] text-slate-500 uppercase tracking-widest">&copy; <?= date('Y') ?> Cyber Range Console</p>
    </div>

  </div>
</aside>