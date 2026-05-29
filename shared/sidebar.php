<?php

/**
 * Sidebar navigation for the main application layout.
 * Use alongside header and footer.
 */

// Helper to determine if a menu item is active
$currentPage = $_GET['page'] ?? 'home';
$currentLvl = $_GET['lvl'] ?? null;
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
      
      <div class="space-y-1">
        <!-- Dashboard Link -->
        <a href="?page=home"
          class="flex items-center px-4 py-2.5 rounded-lg text-xs font-semibold tracking-wide transition group <?= $currentPage === 'home' ? 'bg-blue-600/15 text-blue-400 border-l-2 border-blue-500' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' ?>">
          <i class="fas fa-tachometer-alt w-5 h-5 mr-3 flex items-center justify-center text-sm <?= $currentPage === 'home' ? 'text-blue-400' : 'text-slate-400 group-hover:text-blue-400' ?>"></i>
          Dashboard
        </a>

        <!-- All Labs Catalog -->
        <a href="?page=labs"
          class="flex items-center px-4 py-2.5 rounded-lg text-xs font-semibold tracking-wide transition group <?= $currentPage === 'labs' ? 'bg-blue-600/15 text-blue-400 border-l-2 border-blue-500' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' ?>">
          <i class="fas fa-flask w-5 h-5 mr-3 flex items-center justify-center text-sm <?= $currentPage === 'labs' ? 'text-slate-400' : 'text-slate-400 group-hover:text-blue-400' ?>"></i>
          All Training Labs
        </a>

        <!-- Leaderboard -->
        <a href="?page=leaderboard"
          class="flex items-center px-4 py-2.5 rounded-lg text-xs font-semibold tracking-wide transition group <?= $currentPage === 'leaderboard' ? 'bg-blue-600/15 text-blue-400 border-l-2 border-blue-500' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' ?>">
          <i class="fas fa-trophy w-5 h-5 mr-3 flex items-center justify-center text-sm text-yellow-500"></i>
          Leaderboard
        </a>
      </div>

      <!-- Categories Divider -->
      <div class="pt-5 pb-2">
        <p class="px-4 text-[10px] font-bold uppercase tracking-widest text-slate-500">Active Directives</p>
      </div>

      <div class="space-y-1">
        <!-- XSS Lab -->
        <a href="?page=xss&lvl=1"
          class="flex items-center px-4 py-2.5 rounded-lg text-xs font-semibold tracking-wide transition group <?= ($currentPage === 'xss' && $currentLvl == 1) ? 'bg-blue-600/15 text-blue-400 border-l-2 border-blue-500' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' ?>">
          <i class="fas fa-code w-5 h-5 mr-3 flex items-center justify-center text-sm text-amber-500"></i>
          XSS Lab (Level 1)
        </a>

        <!-- SQLi Lab -->
        <a href="?page=sqli&lvl=1"
          class="flex items-center px-4 py-2.5 rounded-lg text-xs font-semibold tracking-wide transition group <?= ($currentPage === 'sqli' && $currentLvl == 1) ? 'bg-blue-600/15 text-blue-400 border-l-2 border-blue-500' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' ?>">
          <i class="fas fa-database w-5 h-5 mr-3 flex items-center justify-center text-sm text-red-500"></i>
          SQL Injection Lab
        </a>

        <!-- File Upload Lab -->
        <a href="?page=file_upload&lvl=1"
          class="flex items-center px-4 py-2.5 rounded-lg text-xs font-semibold tracking-wide transition group <?= ($currentPage === 'file_upload' && $currentLvl == 1) ? 'bg-blue-600/15 text-blue-400 border-l-2 border-blue-500' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' ?>">
          <i class="fas fa-upload w-5 h-5 mr-3 flex items-center justify-center text-sm text-emerald-500"></i>
          File Upload Lab
        </a>

        <!-- CSRF Lab -->
        <a href="?page=csrf&lvl=1"
          class="flex items-center px-4 py-2.5 rounded-lg text-xs font-semibold tracking-wide transition group <?= ($currentPage === 'csrf' && $currentLvl == 1) ? 'bg-blue-600/15 text-blue-400 border-l-2 border-blue-500' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' ?>">
          <i class="fas fa-exchange-alt w-5 h-5 mr-3 flex items-center justify-center text-sm text-purple-500"></i>
          CSRF Lab
        </a>

        <!-- XXE Lab -->
        <a href="?page=xxe&lvl=1"
          class="flex items-center px-4 py-2.5 rounded-lg text-xs font-semibold tracking-wide transition group <?= ($currentPage === 'xxe' && $currentLvl == 1) ? 'bg-blue-600/15 text-blue-400 border-l-2 border-blue-500' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-100' ?>">
          <i class="fas fa-file-code w-5 h-5 mr-3 flex items-center justify-center text-sm text-orange-500"></i>
          XXE Lab
        </a>
      </div>

      <!-- Instructor Commands (visible only to admins) -->
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
      <p class="text-[10px] text-slate-500 uppercase tracking-widest">&copy; <?php echo date('Y'); ?> Cyber Range Console</p>
    </div>
  </div>
</aside>