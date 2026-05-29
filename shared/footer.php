<?php

/**
 * Global footer for the secure core application.
 * Included at the end of every page.
 */
?>
<?php
// Consistent Level Navigation Footer for Standard UI
if (isset($_GET['page'], $_GET['lvl'])) {
    $currentLab = $_GET['page'];
    $currentLvl = (int)$_GET['lvl'];
    $mapFile = ROOT . "/labs/{$currentLab}/challenge_map.php";
    if (file_exists($mapFile)) {
        $map = require $mapFile;
        if (is_array($map)) {
            $levels = [];
            foreach (array_keys($map) as $key) {
                if (preg_match('/^lvl(\d+)$/', $key, $matches)) {
                    $levels[] = (int)$matches[1];
                }
            }
            sort($levels);
            if (in_array($currentLvl, $levels)) {
                $hasPrev = in_array($currentLvl - 1, $levels);
                $hasNext = in_array($currentLvl + 1, $levels);
                $prevUrl = $hasPrev ? "?page={$currentLab}&lvl=" . ($currentLvl - 1) : "#";
                $nextUrl = $hasNext ? "?page={$currentLab}&lvl=" . ($currentLvl + 1) : "#";
                ?>
                <div class="lg:ml-64 px-6 pb-6 theme-transition">
                  <div class="max-w-4xl mx-auto border-t border-slate-800 pt-6">
                    <div class="flex items-center justify-between gap-4">
                      <!-- Back to Catalog -->
                      <a href="?page=labs" class="flex items-center gap-2 bg-slate-900 border border-slate-800 hover:bg-slate-800 text-slate-300 font-semibold py-2.5 px-5 rounded-lg text-xs uppercase tracking-wider transition">
                        <i class="fas fa-th-large"></i> Back to Labs
                      </a>

                      <div class="flex items-center gap-3">
                        <!-- Previous Level -->
                        <?php if ($hasPrev): ?>
                        <a href="<?= $prevUrl ?>" class="flex items-center gap-2 bg-slate-900 border border-slate-800 hover:bg-slate-800 text-slate-300 font-semibold py-2.5 px-5 rounded-lg text-xs uppercase tracking-wider transition">
                          <i class="fas fa-chevron-left"></i> Previous
                        </a>
                        <?php else: ?>
                        <span class="flex items-center gap-2 bg-slate-900/50 border border-slate-850 text-slate-600 font-semibold py-2.5 px-5 rounded-lg text-xs uppercase tracking-wider cursor-not-allowed">
                          <i class="fas fa-chevron-left"></i> Previous
                        </span>
                        <?php endif; ?>

                        <!-- Next Level -->
                        <?php if ($hasNext): ?>
                        <a href="<?= $nextUrl ?>" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2.5 px-5 rounded-lg text-xs uppercase tracking-wider transition">
                          Next <i class="fas fa-chevron-right"></i>
                        </a>
                        <?php else: ?>
                        <span class="flex items-center gap-2 bg-slate-900/50 border border-slate-850 text-slate-600 font-semibold py-2.5 px-5 rounded-lg text-xs uppercase tracking-wider cursor-not-allowed">
                          Next <i class="fas fa-chevron-right"></i>
                        </span>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
                <?php
            }
        }
    }
}
?>
</main>

<!-- Footer -->
<footer class="bg-slate-900 border-t border-slate-800 py-6 mt-12 theme-transition">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row justify-between items-center text-xs text-slate-500 gap-4">
      <p class="text-center sm:text-left">
        &copy; <?php echo date('Y'); ?> PHP Security Labs – Professional Cybersecurity Training Operations.
      </p>
      <div class="flex space-x-4">
        <span class="flex items-center gap-1.5"><i class="fas fa-lock text-emerald-500 text-[10px]"></i> SECURE SYSTEM</span>
        <span class="text-slate-600">|</span>
        <span>INTERNAL TRAINING ONLY</span>
      </div>
    </div>
  </div>
</footer>

</body>

</html>