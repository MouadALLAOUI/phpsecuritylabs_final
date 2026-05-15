<?php
include_once ROOT . '/shared/header.php';
include_once ROOT . '/shared/sidebar.php';

// Get progress data from Auth (now returns array with count and last_solved)
$progressData = $completed; // Already passed from controller as ['xss' => ['count' => 2, 'last_solved' => '...'], ...]
?>
<div class="lg:ml-64 p-6">
  <div class="max-w-4xl mx-auto">
    <!-- Loading Spinner (shown initially, hidden when content loads) -->
    <div id="loadingSpinner" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-8 text-center">
        <i class="fas fa-circle-notch fa-spin text-4xl text-indigo-600 mb-4"></i>
        <p class="text-gray-700 font-medium">Loading labs...</p>
      </div>
    </div>
    
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
    <div class="bg-white rounded-lg shadow p-6 mb-6 border-l-4 border-yellow-400 lab-content">
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
          <div class="mt-2 flex gap-2">
            <a href="?page=patch_xss" class="text-xs text-indigo-600 hover:text-indigo-800 underline">📄 View Patch
              Report</a>
          </div>
        </div>
        <a href="?page=xss&lvl=1"
          class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition text-sm">Start Lab</a>
      </div>
      <?php
      $xssProgress = $progressData['xss']['count'] ?? 0;
      $xssLastSolved = $progressData['xss']['last_solved'] ?? null;
      $xssTotal = 3;
      ?>
      <div class="mt-3 flex justify-between items-center">
        <div class="text-sm text-gray-500">
          Progress: <?= $xssProgress ?>/<?= $xssTotal ?> completed
          <div class="w-48 bg-gray-200 rounded-full h-1.5 mt-1 inline-block ml-2">
            <div class="bg-indigo-600 h-1.5 rounded-full progress-bar" style="width: <?= ($xssProgress / $xssTotal) * 100 ?>%"></div>
          </div>
          <?php if ($xssLastSolved): ?>
          <div class="mt-1 text-xs text-gray-400">Last solved: <?= date('Y-m-d H:i', strtotime($xssLastSolved)) ?></div>
          <?php endif; ?>
        </div>
        <?php if ($xssProgress > 0): ?>
        <form method="POST" action="?page=labs&reset=xss" class="inline"
          onsubmit="return confirm('Reset all progress for XSS lab? This cannot be undone.');">
          <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32)) ?>">
          <button type="submit" class="bg-red-600 text-white px-3 py-1.5 rounded hover:bg-red-700 transition text-xs">
            <i class="fas fa-undo-alt mr-1"></i> Reset
          </button>
        </form>
        <?php endif; ?>
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
            <span class="px-2 py-1 bg-gray-100 text-xs rounded">Level 1: Auth Bypass</span>
            <span class="px-2 py-1 bg-gray-100 text-xs rounded">Level 2: UNION Extraction</span>
          </div>
          <div class="mt-2 flex gap-2">
            <a href="?page=patch_sqli" class="text-xs text-indigo-600 hover:text-indigo-800 underline">📄 View Patch
              Report</a>
          </div>
        </div>
        <a href="?page=sqli&lvl=1"
          class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition text-sm">Start Lab</a>
      </div>
      <?php
      $sqliProgress = $progressData['sqli']['count'] ?? 0;
      $sqliLastSolved = $progressData['sqli']['last_solved'] ?? null;
      $sqliTotal = 2;
      ?>
      <div class="mt-3 flex justify-between items-center">
        <div class="text-sm text-gray-500">
          Progress: <?= $sqliProgress ?>/<?= $sqliTotal ?> completed
          <div class="w-48 bg-gray-200 rounded-full h-1.5 mt-1 inline-block ml-2">
            <div class="bg-indigo-600 h-1.5 rounded-full" style="width: <?= ($sqliProgress / $sqliTotal) * 100 ?>%">
            </div>
          </div>
          <?php if ($sqliLastSolved): ?>
          <div class="mt-1 text-xs text-gray-400">Last solved: <?= date('Y-m-d H:i', strtotime($sqliLastSolved)) ?>
          </div>
          <?php endif; ?>
        </div>
        <?php if ($sqliProgress > 0): ?>
        <form method="POST" action="?page=labs&reset=sqli" class="inline"
          onsubmit="return confirm('Reset all progress for SQL Injection lab? This cannot be undone.');">
          <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32)) ?>">
          <button type="submit" class="bg-red-600 text-white px-3 py-1.5 rounded hover:bg-red-700 transition text-xs">
            <i class="fas fa-undo-alt mr-1"></i> Reset
          </button>
        </form>
        <?php endif; ?>
      </div>
    </div>

    <!-- File Upload Lab -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-400">
      <div class="flex justify-between items-start">
        <div>
          <h2 class="text-xl font-semibold text-gray-900">File Upload</h2>
          <p class="text-gray-600 mt-1">Bypass validation to upload malicious files and gain remote execution.</p>
          <div class="mt-2 flex flex-wrap gap-2">
            <span class="px-2 py-1 bg-gray-100 text-xs rounded">Level 1: Extension Bypass</span>
            <span class="px-2 py-1 bg-gray-100 text-xs rounded">Level 2: MIME Spoofing</span>
          </div>
          <div class="mt-2 flex gap-2">
            <a href="?page=patch_fileupload" class="text-xs text-indigo-600 hover:text-indigo-800 underline">📄 View
              Patch Report</a>
          </div>
        </div>
        <a href="?page=file_upload&lvl=1"
          class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition text-sm">Start Lab</a>
      </div>
      <?php
      $uploadProgress = $progressData['file_upload']['count'] ?? 0;
      $uploadLastSolved = $progressData['file_upload']['last_solved'] ?? null;
      $uploadTotal = 2;
      ?>
      <div class="mt-3 flex justify-between items-center">
        <div class="text-sm text-gray-500">
          Progress: <?= $uploadProgress ?>/<?= $uploadTotal ?> completed
          <div class="w-48 bg-gray-200 rounded-full h-1.5 mt-1 inline-block ml-2">
            <div class="bg-indigo-600 h-1.5 rounded-full" style="width: <?= ($uploadProgress / $uploadTotal) * 100 ?>%">
            </div>
            <?php if ($uploadLastSolved): ?>
            <div class="mt-1 text-xs text-gray-400">Last solved: <?= date('Y-m-d H:i', strtotime($uploadLastSolved)) ?>
            </div>
            <?php endif; ?>
          </div>
        </div>
        <?php if ($uploadProgress > 0): ?>
        <form method="POST" action="?page=labs&reset=file_upload" class="inline"
          onsubmit="return confirm('Reset all progress for File Upload lab? This cannot be undone.');">
          <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32)) ?>">
          <button type="submit" class="bg-red-600 text-white px-3 py-1.5 rounded hover:bg-red-700 transition text-xs">
            <i class="fas fa-undo-alt mr-1"></i> Reset
          </button>
        </form>
        <?php endif; ?>
      </div>
    </div>

    <!-- CSRF Lab -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-400">
      <div class="flex justify-between items-start">
        <div>
          <h2 class="text-xl font-semibold text-gray-900">Cross-Site Request Forgery (CSRF)</h2>
          <p class="text-gray-600 mt-1">Exploit missing CSRF tokens to perform unauthorized fund transfers.</p>
          <div class="mt-2 flex flex-wrap gap-2">
            <span class="px-2 py-1 bg-gray-100 text-xs rounded">Level 1: Basic CSRF</span>
            <span class="px-2 py-1 bg-gray-100 text-xs rounded">Level 2: Token Prediction</span>
          </div>
        </div>
        <a href="?page=csrf&lvl=1"
          class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition text-sm">Start Lab</a>
      </div>
      <?php
      $csrfProgress = $progressData['csrf']['count'] ?? 0;
      $csrfLastSolved = $progressData['csrf']['last_solved'] ?? null;
      $csrfTotal = 2;
      ?>
      <div class="mt-3 flex justify-between items-center">
        <div class="text-sm text-gray-500">
          Progress: <?= $csrfProgress ?>/<?= $csrfTotal ?> completed
          <div class="w-48 bg-gray-200 rounded-full h-1.5 mt-1 inline-block ml-2">
            <div class="bg-indigo-600 h-1.5 rounded-full" style="width: <?= ($csrfProgress / $csrfTotal) * 100 ?>%"></div>
          </div>
          <?php if ($csrfLastSolved): ?>
          <div class="mt-1 text-xs text-gray-400">Last solved: <?= date('Y-m-d H:i', strtotime($csrfLastSolved)) ?></div>
          <?php endif; ?>
        </div>
        <?php if ($csrfProgress > 0): ?>
        <form method="POST" action="?page=labs&reset=csrf" class="inline"
          onsubmit="return confirm('Reset all progress for CSRF lab? This cannot be undone.');">
          <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32)) ?>">
          <button type="submit" class="bg-red-600 text-white px-3 py-1.5 rounded hover:bg-red-700 transition text-xs">
            <i class="fas fa-undo-alt mr-1"></i> Reset
          </button>
        </form>
        <?php endif; ?>
      </div>
    </div>

    <!-- XXE Lab -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-400">
      <div class="flex justify-between items-start">
        <div>
          <h2 class="text-xl font-semibold text-gray-900">XML External Entity (XXE)</h2>
          <p class="text-gray-600 mt-1">Exploit XML parsers to read sensitive files and exfiltrate data.</p>
          <div class="mt-2 flex flex-wrap gap-2">
            <span class="px-2 py-1 bg-gray-100 text-xs rounded">Level 1: Basic XXE</span>
            <span class="px-2 py-1 bg-gray-100 text-xs rounded">Level 2: Blind XXE (OOB)</span>
          </div>
        </div>
        <a href="?page=xxe&lvl=1"
          class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition text-sm">Start Lab</a>
      </div>
      <?php
      $xxeProgress = $progressData['xxe']['count'] ?? 0;
      $xxeLastSolved = $progressData['xxe']['last_solved'] ?? null;
      $xxeTotal = 2;
      ?>
      <div class="mt-3 flex justify-between items-center">
        <div class="text-sm text-gray-500">
          Progress: <?= $xxeProgress ?>/<?= $xxeTotal ?> completed
          <div class="w-48 bg-gray-200 rounded-full h-1.5 mt-1 inline-block ml-2">
            <div class="bg-indigo-600 h-1.5 rounded-full" style="width: <?= ($xxeProgress / $xxeTotal) * 100 ?>%"></div>
          </div>
          <?php if ($xxeLastSolved): ?>
          <div class="mt-1 text-xs text-gray-400">Last solved: <?= date('Y-m-d H:i', strtotime($xxeLastSolved)) ?></div>
          <?php endif; ?>
        </div>
        <?php if ($xxeProgress > 0): ?>
        <form method="POST" action="?page=labs&reset=xxe" class="inline"
          onsubmit="return confirm('Reset all progress for XXE lab? This cannot be undone.');">
          <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32)) ?>">
          <button type="submit" class="bg-red-600 text-white px-3 py-1.5 rounded hover:bg-red-700 transition text-xs">
            <i class="fas fa-undo-alt mr-1"></i> Reset
          </button>
        </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
// Hide loading spinner when page is fully loaded
window.addEventListener('load', function() {
  const spinner = document.getElementById('loadingSpinner');
  if (spinner) {
    spinner.style.opacity = '0';
    spinner.style.transition = 'opacity 0.3s ease-out';
    setTimeout(() => {
      spinner.style.display = 'none';
    }, 300);
  }
  
  // Animate progress bars
  const progressBars = document.querySelectorAll('.progress-bar');
  progressBars.forEach(bar => {
    const width = bar.style.width;
    bar.style.width = '0%';
    bar.style.transition = 'width 0.8s ease-out';
    setTimeout(() => {
      bar.style.width = width;
    }, 100);
  });
});
</script>

<?php include_once ROOT . '/shared/footer.php'; ?>