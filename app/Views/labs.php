<?php
/**
 * Labs Catalog – dynamically driven by LabCatalog
 */

use App\Core\LabCatalog;
use App\Core\Session;

include_once ROOT . '/shared/header.php';
include_once ROOT . '/shared/sidebar.php';

// $completed is passed in from AuthController::showLabs()  (keyed by lab slug)
$progressData = $completed ?? [];

// Build catalog once
$catalog = LabCatalog::all();
$colors  = LabCatalog::$colorMap;
?>
<div class="lg:ml-64 p-6 min-h-[85vh] theme-transition">
  <div class="max-w-5xl mx-auto space-y-6">

    <!-- Loading Overlay -->
    <div id="loadingSpinner" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md flex items-center justify-center z-50 transition-opacity duration-300">
      <div class="bg-slate-900 border border-slate-800 rounded-xl p-8 text-center max-w-sm w-full mx-4 shadow-2xl">
        <i class="fas fa-circle-notch fa-spin text-4xl text-blue-500 mb-4"></i>
        <p class="text-slate-100 font-bold uppercase tracking-wider text-xs">Establishing Console Link...</p>
        <p class="text-slate-500 text-xs mt-1">Retrieving tactical module progress</p>
      </div>
    </div>

    <!-- Reset notifications -->
    <?php if (isset($_SESSION['reset_message'])): ?>
    <div class="bg-blue-950/40 border border-blue-500/20 p-4 rounded-xl flex items-start space-x-3 text-blue-200">
      <i class="fas fa-info-circle text-blue-400 mt-0.5"></i>
      <p class="text-xs font-semibold"><?= e($_SESSION['reset_message']) ?></p>
    </div>
    <?php unset($_SESSION['reset_message']); endif; ?>

    <?php if (isset($_SESSION['reset_error'])): ?>
    <div class="bg-red-950/40 border border-red-500/20 p-4 rounded-xl flex items-start space-x-3 text-red-200">
      <i class="fas fa-exclamation-circle text-red-400 mt-0.5"></i>
      <p class="text-xs font-semibold"><?= e($_SESSION['reset_error']) ?></p>
    </div>
    <?php unset($_SESSION['reset_error']); endif; ?>

    <!-- Title Header -->
    <div class="border-b border-slate-800 pb-4 mb-6">
      <h1 class="text-xl font-bold text-slate-100 uppercase tracking-wide flex items-center gap-2.5">
        <i class="fas fa-flask text-blue-500"></i> Operation Directives
        <span class="ml-auto text-[10px] font-bold px-2 py-0.5 bg-slate-800 text-slate-400 rounded border border-slate-700 tracking-wide"><?= count($catalog) ?> MODULES</span>
      </h1>
      <p class="text-slate-400 text-xs mt-1">Select an active training sandbox to perform offensive security directives.</p>
    </div>

    <!-- Dynamic Lab Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

      <?php foreach ($catalog as $slug => $lab):
        $c         = $colors[$lab['color']] ?? $colors['blue'];
        $progress  = (int)($progressData[$slug]['count'] ?? 0);
        $total     = $lab['total'];
        $percent   = $total > 0 ? round(($progress / $total) * 100) : 0;
        $completed_lab = $progress >= $total && $total > 0;
      ?>
      <div class="bg-slate-900 border border-slate-800 hover:border-slate-700/80 rounded-xl p-6 shadow-lg flex flex-col justify-between transition duration-200 relative overflow-hidden">

        <?php if ($completed_lab): ?>
        <div class="absolute top-0 left-0 right-0 h-0.5 bg-teal-500"></div>
        <?php endif; ?>

        <div>
          <div class="flex justify-between items-start mb-4">
            <div class="flex items-center gap-3">
              <span class="<?= $c['bg'] ?> p-2 rounded-lg border <?= $c['border'] ?> <?= $c['text'] ?>">
                <i class="fas <?= e($lab['icon']) ?> text-sm"></i>
              </span>
              <h2 class="text-sm font-bold text-slate-100 uppercase tracking-wider"><?= e($lab['title']) ?></h2>
            </div>
            <span class="text-[10px] font-bold px-2 py-0.5 <?= $c['badge_bg'] ?> <?= $c['badge_text'] ?> rounded border <?= $c['badge_border'] ?> tracking-wide uppercase shrink-0">
              <?= e($lab['difficulty']) ?>
            </span>
          </div>

          <p class="text-xs text-slate-400 leading-relaxed mb-4"><?= e($lab['description']) ?></p>

          <!-- Tags -->
          <?php if (!empty($lab['tags'])): ?>
          <div class="flex flex-wrap gap-1.5 mb-4">
            <?php foreach ($lab['tags'] as $tag): ?>
            <span class="px-2 py-0.5 bg-slate-950 text-[10px] text-slate-400 rounded font-mono uppercase"><?= e($tag) ?></span>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>

          <!-- Patch link -->
          <div class="mb-5">
            <?php if ($lab['patch_page']): ?>
            <a href="?page=<?= e($lab['patch_page']) ?>" class="text-xs text-blue-400 hover:text-blue-300 font-semibold flex items-center gap-1 transition">
              <i class="fas fa-file-shield text-[10px]"></i> View Analysis &amp; Fixes
            </a>
            <?php else: ?>
            <span class="text-slate-500 text-xs italic"><i class="fas fa-info-circle text-[10px]"></i> Standard Directive</span>
            <?php endif; ?>
          </div>
        </div>

        <!-- Progress + Actions -->
        <div class="pt-5 border-t border-slate-800 flex flex-col gap-4">
          <div class="flex justify-between items-center text-xs text-slate-400">
            <span class="font-mono">Progress: <?= $progress ?>/<?= $total ?> Tasks</span>
            <span class="font-mono font-bold <?= $completed_lab ? 'text-teal-400' : 'text-blue-400' ?>"><?= $percent ?>%<?= $completed_lab ? ' ✓' : '' ?></span>
          </div>
          <div class="w-full bg-slate-950 rounded-full h-2">
            <div class="<?= $completed_lab ? 'bg-teal-500' : 'bg-blue-500' ?> h-2 rounded-full progress-bar transition-all duration-700" style="width: <?= $percent ?>%"></div>
          </div>

          <div class="flex justify-between items-center mt-2 gap-4">
            <a href="?page=<?= e($slug) ?>&lvl=1"
              class="flex-1 text-center <?= $completed_lab ? 'bg-teal-600 hover:bg-teal-500' : 'bg-blue-600 hover:bg-blue-500' ?> text-white font-bold py-2.5 rounded-lg text-xs uppercase tracking-wider transition">
              <?= $completed_lab ? 'Replay Lab' : 'Launch Sandbox' ?>
            </a>
            <?php if ($progress > 0): ?>
            <form method="POST" action="?page=labs" class="inline"
              onsubmit="return confirm('Reset progress for <?= e($lab['title']) ?>? This action is irreversible.');">
              <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCsrfToken() ?>">
              <input type="hidden" name="reset" value="<?= e($slug) ?>">
              <button type="submit" class="bg-slate-950 border border-slate-850 hover:bg-red-950/20 hover:text-red-400 hover:border-red-900/30 text-slate-500 p-2.5 rounded-lg transition" title="Reset directive tasks">
                <i class="fas fa-undo-alt text-xs"></i>
              </button>
            </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>

    </div><!-- /grid -->
  </div>
</div>

<script>
window.addEventListener('load', function() {
  const spinner = document.getElementById('loadingSpinner');
  if (spinner) {
    spinner.style.opacity = '0';
    spinner.style.transition = 'opacity 0.25s ease-out';
    setTimeout(() => { spinner.style.display = 'none'; }, 250);
  }
  document.querySelectorAll('.progress-bar').forEach(bar => {
    const w = bar.style.width;
    bar.style.width = '0%';
    setTimeout(() => { bar.style.width = w; }, 50);
  });
});
</script>

<?php include_once ROOT . '/shared/footer.php'; ?>