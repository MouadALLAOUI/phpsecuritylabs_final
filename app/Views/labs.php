<?php
include_once ROOT . '/shared/header.php';
include_once ROOT . '/shared/sidebar.php';
?>
<div class="lg:ml-64 p-6">
  <div class="max-w-4xl mx-auto">
    <?php if (isset($_SESSION['reset_message'])): ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
      <?= htmlspecialchars($_SESSION['reset_message']) ?>
    </div>
    <?php unset($_SESSION['reset_message']); ?>
    <?php endif; ?>
    <h1 class="text-2xl font-bold text-gray-900 mb-6">
      <i class="fas fa-flask text-indigo-600 mr-2"></i>Available Labs
    </h1>

    <!-- XSS Lab -->
    <div class="bg-white rounded-lg shadow p-6 mb-6 border-l-4 border-yellow-400">
      <div class="flex justify-between items-start">
        <div>
          <h2 class="text-xl font-semibold text-gray-900">Cross‑Site Scripting (XSS)</h2>
          <p class="text-gray-600 mt-1">Learn reflected, stored, and DOM‑based XSS attacks in a realistic intelligence
            messaging system.</p>
          <div class="mt-2 flex flex-wrap gap-2">
            <span class="px-2 py-1 bg-gray-100 text-xs rounded">Level 1: Reflected</span>
            <span class="px-2 py-1 bg-gray-100 text-xs rounded">Level 2: Stored</span>
            <span class="px-2 py-1 bg-gray-100 text-xs rounded">Level 3: DOM</span>
          </div>
        </div>
        <a href="?page=xss&lvl=1"
          class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition text-sm">Start Lab</a>
        <?php
        $xssCompleted = $completedMap['xss'] ?? [];
        $xssProgress = count($xssCompleted);
        if ($xssProgress > 0):
        ?>
        <form method="POST" action="?page=labs&reset=xss"
          onsubmit="return confirm('Reset all progress for XSS lab? This cannot be undone.');">
          <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition text-sm">
            <i class="fas fa-undo-alt mr-1"></i> Reset
          </button>
        </form>
        <?php endif; ?>
      </div>
      <!-- show progress for XSS -->
      <?php
      $xssCompleted = $completedMap['xss'] ?? [];
      $xssLevels = ['lvl1', 'lvl2']; // define all levels for progress calculation
      $xssProgress = 0;
      foreach ($xssLevels as $lvl) {
        if (isset($xssCompleted[$lvl])) $xssProgress++;
      }
      ?>
      <div class="mt-3 text-sm text-gray-500">
        Progress: <?= $xssProgress ?>/<?= count($xssLevels) ?> completed
        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
          <div class="bg-indigo-600 h-1.5 rounded-full" style="width: <?= ($xssProgress / count($xssLevels)) * 100 ?>%">
          </div>
        </div>
      </div>
    </div>

    <!-- SQL Injection Lab -->
    <div class="bg-white rounded-lg shadow p-6 mb-6 border-l-4 border-red-400">
      <div class="flex justify-between items-start">
        <div>
          <h2 class="text-xl font-semibold text-gray-900">SQL Injection</h2>
          <p class="text-gray-600 mt-1">Exploit vulnerable database queries to extract secrets from the military
            database.</p>
          <div class="mt-2 flex flex-wrap gap-2">
            <span class="px-2 py-1 bg-gray-100 text-xs rounded">Coming Soon</span>
          </div>
        </div>
        <button disabled class="bg-gray-300 text-gray-500 px-4 py-2 rounded cursor-not-allowed">Locked</button>
      </div>
    </div>

    <!-- File Upload Lab -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-400">
      <div class="flex justify-between items-start">
        <div>
          <h2 class="text-xl font-semibold text-gray-900">File Upload</h2>
          <p class="text-gray-600 mt-1">Bypass validation to upload malicious files and gain remote execution.</p>
          <div class="mt-2 flex flex-wrap gap-2">
            <span class="px-2 py-1 bg-gray-100 text-xs rounded">Coming Soon</span>
          </div>
        </div>
        <button disabled class="bg-gray-300 text-gray-500 px-4 py-2 rounded cursor-not-allowed">Locked</button>
      </div>
    </div>
  </div>
</div>
<?php include_once ROOT . '/shared/footer.php'; ?>