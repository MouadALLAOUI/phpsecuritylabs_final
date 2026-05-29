<?php
include_once ROOT . '/shared/header.php';
include_once ROOT . '/shared/sidebar.php';

// Get progress data from Auth
$progressData = $completed; 
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
      <p class="text-xs font-semibold"><?= htmlspecialchars($_SESSION['reset_message']) ?></p>
    </div>
    <?php unset($_SESSION['reset_message']); ?>
    <?php endif; ?>

    <!-- Title Header -->
    <div class="border-b border-slate-800 pb-4 mb-6">
      <h1 class="text-xl font-bold text-slate-100 uppercase tracking-wide flex items-center gap-2.5">
        <i class="fas fa-flask text-blue-500"></i> Operation Directives
      </h1>
      <p class="text-slate-400 text-xs mt-1">Select an active training sandbox to perform offensive security directives.</p>
    </div>

    <!-- Redesigned Catalog Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      
      <!-- XSS Lab -->
      <div class="bg-slate-900 border border-slate-800 hover:border-slate-700/80 rounded-xl p-6 shadow-lg flex flex-col justify-between transition duration-200">
        <div>
          <div class="flex justify-between items-start mb-4">
            <div class="flex items-center gap-3">
              <span class="bg-amber-500/10 p-2 rounded-lg border border-amber-500/20 text-amber-500"><i class="fas fa-code text-sm"></i></span>
              <h2 class="text-sm font-bold text-slate-100 uppercase tracking-wider">Cross-Site Scripting (XSS)</h2>
            </div>
            <span class="text-[10px] font-bold px-2 py-0.5 bg-amber-500/10 text-amber-400 rounded border border-amber-500/25 tracking-wide uppercase">Vulnerable messaging</span>
          </div>
          <p class="text-xs text-slate-400 leading-relaxed mb-4">Learn reflected, stored, and DOM‑based XSS attacks in a simulated messaging console.</p>
          
          <div class="flex flex-wrap gap-1.5 mb-4">
            <span class="px-2 py-0.5 bg-slate-950 text-[10px] text-slate-400 rounded font-mono uppercase">Reflected</span>
            <span class="px-2 py-0.5 bg-slate-950 text-[10px] text-slate-400 rounded font-mono uppercase">Stored</span>
            <span class="px-2 py-0.5 bg-slate-950 text-[10px] text-slate-400 rounded font-mono uppercase">DOM</span>
          </div>

          <div class="mb-5 flex gap-2">
            <a href="?page=patch_xss" class="text-xs text-blue-400 hover:text-blue-300 font-semibold flex items-center gap-1 transition">
              <i class="fas fa-file-shield text-[10px]"></i> View Analysis & Fixes
            </a>
          </div>
        </div>

        <?php
        $xssProgress = $progressData['xss']['count'] ?? 0;
        $xssLastSolved = $progressData['xss']['last_solved'] ?? null;
        $xssTotal = 3;
        $xssPercent = round(($xssProgress / $xssTotal) * 100);
        ?>
        <div class="pt-5 border-t border-slate-800 flex flex-col gap-4">
          <div class="flex justify-between items-center text-xs text-slate-400">
            <span class="font-mono">Progress: <?= $xssProgress ?>/<?= $xssTotal ?> Tasks</span>
            <span class="font-mono font-bold text-blue-400"><?= $xssPercent ?>%</span>
          </div>
          <div class="w-full bg-slate-950 rounded-full h-2">
            <div class="bg-blue-500 h-2 rounded-full progress-bar transition-all duration-700" style="width: <?= $xssPercent ?>%"></div>
          </div>
          
          <div class="flex justify-between items-center mt-2 gap-4">
            <a href="?page=xss&lvl=1"
              class="flex-1 text-center bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 rounded-lg text-xs uppercase tracking-wider transition">
              Launch sandbox
            </a>
            <?php if ($xssProgress > 0): ?>
            <form method="POST" action="?page=labs&reset=xss" class="inline"
              onsubmit="return confirm('Reset progress for XSS? This action is irreversible.');">
              <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32)) ?>">
              <button type="submit" class="bg-slate-950 border border-slate-850 hover:bg-red-950/20 hover:text-red-400 hover:border-red-900/30 text-slate-500 p-2.5 rounded-lg transition" title="Reset directive tasks">
                <i class="fas fa-undo-alt text-xs"></i>
              </button>
            </form>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- SQL Injection Lab -->
      <div class="bg-slate-900 border border-slate-800 hover:border-slate-700/80 rounded-xl p-6 shadow-lg flex flex-col justify-between transition duration-200">
        <div>
          <div class="flex justify-between items-start mb-4">
            <div class="flex items-center gap-3">
              <span class="bg-red-500/10 p-2 rounded-lg border border-red-500/20 text-red-500"><i class="fas fa-database text-sm"></i></span>
              <h2 class="text-sm font-bold text-slate-100 uppercase tracking-wider">SQL Injection</h2>
            </div>
            <span class="text-[10px] font-bold px-2 py-0.5 bg-red-500/10 text-red-400 rounded border border-red-500/25 tracking-wide uppercase">Core breach</span>
          </div>
          <p class="text-xs text-slate-400 leading-relaxed mb-4">Exploit insecure backend queries to bypass credentials or dump table values.</p>
          
          <div class="flex flex-wrap gap-1.5 mb-4">
            <span class="px-2 py-0.5 bg-slate-950 text-[10px] text-slate-400 rounded font-mono uppercase">Auth Bypass</span>
            <span class="px-2 py-0.5 bg-slate-950 text-[10px] text-slate-400 rounded font-mono uppercase">Union Extract</span>
          </div>

          <div class="mb-5 flex gap-2">
            <a href="?page=patch_sqli" class="text-xs text-blue-400 hover:text-blue-300 font-semibold flex items-center gap-1 transition">
              <i class="fas fa-file-shield text-[10px]"></i> View Analysis & Fixes
            </a>
          </div>
        </div>

        <?php
        $sqliProgress = $progressData['sqli']['count'] ?? 0;
        $sqliLastSolved = $progressData['sqli']['last_solved'] ?? null;
        $sqliTotal = 2;
        $sqliPercent = round(($sqliProgress / $sqliTotal) * 100);
        ?>
        <div class="pt-5 border-t border-slate-800 flex flex-col gap-4">
          <div class="flex justify-between items-center text-xs text-slate-400">
            <span class="font-mono">Progress: <?= $sqliProgress ?>/<?= $sqliTotal ?> Tasks</span>
            <span class="font-mono font-bold text-blue-400"><?= $sqliPercent ?>%</span>
          </div>
          <div class="w-full bg-slate-950 rounded-full h-2">
            <div class="bg-blue-500 h-2 rounded-full progress-bar transition-all duration-700" style="width: <?= $sqliPercent ?>%"></div>
          </div>
          
          <div class="flex justify-between items-center mt-2 gap-4">
            <a href="?page=sqli&lvl=1"
              class="flex-1 text-center bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 rounded-lg text-xs uppercase tracking-wider transition">
              Launch sandbox
            </a>
            <?php if ($sqliProgress > 0): ?>
            <form method="POST" action="?page=labs&reset=sqli" class="inline"
              onsubmit="return confirm('Reset progress for SQL Injection? This action is irreversible.');">
              <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32)) ?>">
              <button type="submit" class="bg-slate-950 border border-slate-850 hover:bg-red-950/20 hover:text-red-400 hover:border-red-900/30 text-slate-500 p-2.5 rounded-lg transition" title="Reset directive tasks">
                <i class="fas fa-undo-alt text-xs"></i>
              </button>
            </form>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- File Upload Lab -->
      <div class="bg-slate-900 border border-slate-800 hover:border-slate-700/80 rounded-xl p-6 shadow-lg flex flex-col justify-between transition duration-200">
        <div>
          <div class="flex justify-between items-start mb-4">
            <div class="flex items-center gap-3">
              <span class="bg-emerald-500/10 p-2 rounded-lg border border-emerald-500/20 text-emerald-500"><i class="fas fa-upload text-sm"></i></span>
              <h2 class="text-sm font-bold text-slate-100 uppercase tracking-wider">File Infiltration</h2>
            </div>
            <span class="text-[10px] font-bold px-2 py-0.5 bg-emerald-500/10 text-emerald-400 rounded border border-emerald-500/25 tracking-wide uppercase">Command execute</span>
          </div>
          <p class="text-xs text-slate-400 leading-relaxed mb-4">Evade filter limits to load web shells and invoke server-side control.</p>
          
          <div class="flex flex-wrap gap-1.5 mb-4">
            <span class="px-2 py-0.5 bg-slate-950 text-[10px] text-slate-400 rounded font-mono uppercase">Extensions</span>
            <span class="px-2 py-0.5 bg-slate-950 text-[10px] text-slate-400 rounded font-mono uppercase">MIME Spoof</span>
          </div>

          <div class="mb-5 flex gap-2">
            <a href="?page=patch_fileupload" class="text-xs text-blue-400 hover:text-blue-300 font-semibold flex items-center gap-1 transition">
              <i class="fas fa-file-shield text-[10px]"></i> View Analysis & Fixes
            </a>
          </div>
        </div>

        <?php
        $uploadProgress = $progressData['file_upload']['count'] ?? 0;
        $uploadLastSolved = $progressData['file_upload']['last_solved'] ?? null;
        $uploadTotal = 2;
        $uploadPercent = round(($uploadProgress / $uploadTotal) * 100);
        ?>
        <div class="pt-5 border-t border-slate-800 flex flex-col gap-4">
          <div class="flex justify-between items-center text-xs text-slate-400">
            <span class="font-mono">Progress: <?= $uploadProgress ?>/<?= $uploadTotal ?> Tasks</span>
            <span class="font-mono font-bold text-blue-400"><?= $uploadPercent ?>%</span>
          </div>
          <div class="w-full bg-slate-950 rounded-full h-2">
            <div class="bg-blue-500 h-2 rounded-full progress-bar transition-all duration-700" style="width: <?= $uploadPercent ?>%"></div>
          </div>
          
          <div class="flex justify-between items-center mt-2 gap-4">
            <a href="?page=file_upload&lvl=1"
              class="flex-1 text-center bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 rounded-lg text-xs uppercase tracking-wider transition">
              Launch sandbox
            </a>
            <?php if ($uploadProgress > 0): ?>
            <form method="POST" action="?page=labs&reset=file_upload" class="inline"
              onsubmit="return confirm('Reset progress for File Upload? This action is irreversible.');">
              <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32)) ?>">
              <button type="submit" class="bg-slate-950 border border-slate-850 hover:bg-red-950/20 hover:text-red-400 hover:border-red-900/30 text-slate-500 p-2.5 rounded-lg transition" title="Reset directive tasks">
                <i class="fas fa-undo-alt text-xs"></i>
              </button>
            </form>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- CSRF Lab -->
      <div class="bg-slate-900 border border-slate-800 hover:border-slate-700/80 rounded-xl p-6 shadow-lg flex flex-col justify-between transition duration-200">
        <div>
          <div class="flex justify-between items-start mb-4">
            <div class="flex items-center gap-3">
              <span class="bg-purple-500/10 p-2 rounded-lg border border-purple-500/20 text-purple-500"><i class="fas fa-exchange-alt text-sm"></i></span>
              <h2 class="text-sm font-bold text-slate-100 uppercase tracking-wider">Request Forgery (CSRF)</h2>
            </div>
            <span class="text-[10px] font-bold px-2 py-0.5 bg-purple-500/10 text-purple-400 rounded border border-purple-500/25 tracking-wide uppercase">Auth forgery</span>
          </div>
          <p class="text-xs text-slate-400 leading-relaxed mb-4">Perform unauthorized fund transfers by exploiting missing user verification tokens.</p>
          
          <div class="flex flex-wrap gap-1.5 mb-4">
            <span class="px-2 py-0.5 bg-slate-950 text-[10px] text-slate-400 rounded font-mono uppercase">Basic CSRF</span>
            <span class="px-2 py-0.5 bg-slate-950 text-[10px] text-slate-400 rounded font-mono uppercase">Prediction</span>
          </div>

          <div class="mb-5 flex gap-2">
            <span class="text-slate-500 text-xs italic"><i class="fas fa-info-circle text-[10px]"></i> Standard Directive</span>
          </div>
        </div>

        <?php
        $csrfProgress = $progressData['csrf']['count'] ?? 0;
        $csrfLastSolved = $progressData['csrf']['last_solved'] ?? null;
        $csrfTotal = 2;
        $csrfPercent = round(($csrfProgress / $csrfTotal) * 100);
        ?>
        <div class="pt-5 border-t border-slate-800 flex flex-col gap-4">
          <div class="flex justify-between items-center text-xs text-slate-400">
            <span class="font-mono">Progress: <?= $csrfProgress ?>/<?= $csrfTotal ?> Tasks</span>
            <span class="font-mono font-bold text-blue-400"><?= $csrfPercent ?>%</span>
          </div>
          <div class="w-full bg-slate-950 rounded-full h-2">
            <div class="bg-blue-500 h-2 rounded-full progress-bar transition-all duration-700" style="width: <?= $csrfPercent ?>%"></div>
          </div>
          
          <div class="flex justify-between items-center mt-2 gap-4">
            <a href="?page=csrf&lvl=1"
              class="flex-1 text-center bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 rounded-lg text-xs uppercase tracking-wider transition">
              Launch sandbox
            </a>
            <?php if ($csrfProgress > 0): ?>
            <form method="POST" action="?page=labs&reset=csrf" class="inline"
              onsubmit="return confirm('Reset progress for CSRF? This action is irreversible.');">
              <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32)) ?>">
              <button type="submit" class="bg-slate-950 border border-slate-850 hover:bg-red-950/20 hover:text-red-400 hover:border-red-900/30 text-slate-500 p-2.5 rounded-lg transition" title="Reset directive tasks">
                <i class="fas fa-undo-alt text-xs"></i>
              </button>
            </form>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- XXE Lab -->
      <div class="bg-slate-900 border border-slate-800 hover:border-slate-700/80 rounded-xl p-6 shadow-lg flex flex-col justify-between transition duration-200">
        <div>
          <div class="flex justify-between items-start mb-4">
            <div class="flex items-center gap-3">
              <span class="bg-orange-500/10 p-2 rounded-lg border border-orange-500/20 text-orange-500"><i class="fas fa-file-code text-sm"></i></span>
              <h2 class="text-sm font-bold text-slate-100 uppercase tracking-wider">XML External Entity (XXE)</h2>
            </div>
            <span class="text-[10px] font-bold px-2 py-0.5 bg-orange-500/10 text-orange-400 rounded border border-orange-500/25 tracking-wide uppercase">Parser breach</span>
          </div>
          <p class="text-xs text-slate-400 leading-relaxed mb-4">Exploit XML parsers to extract sensitive local files and execute out-of-band data exfiltrations.</p>
          
          <div class="flex flex-wrap gap-1.5 mb-4">
            <span class="px-2 py-0.5 bg-slate-950 text-[10px] text-slate-400 rounded font-mono uppercase">Basic XXE</span>
            <span class="px-2 py-0.5 bg-slate-950 text-[10px] text-slate-400 rounded font-mono uppercase">Blind OOB</span>
          </div>

          <div class="mb-5 flex gap-2">
            <span class="text-slate-500 text-xs italic"><i class="fas fa-info-circle text-[10px]"></i> Standard Directive</span>
          </div>
        </div>

        <?php
        $xxeProgress = $progressData['xxe']['count'] ?? 0;
        $xxeLastSolved = $progressData['xxe']['last_solved'] ?? null;
        $xxeTotal = 2;
        $xxePercent = round(($xxeProgress / $xxeTotal) * 100);
        ?>
        <div class="pt-5 border-t border-slate-800 flex flex-col gap-4">
          <div class="flex justify-between items-center text-xs text-slate-400">
            <span class="font-mono">Progress: <?= $xxeProgress ?>/<?= $xxeTotal ?> Tasks</span>
            <span class="font-mono font-bold text-blue-400"><?= $xxePercent ?>%</span>
          </div>
          <div class="w-full bg-slate-950 rounded-full h-2">
            <div class="bg-blue-500 h-2 rounded-full progress-bar transition-all duration-700" style="width: <?= $xxePercent ?>%"></div>
          </div>
          
          <div class="flex justify-between items-center mt-2 gap-4">
            <a href="?page=xxe&lvl=1"
              class="flex-1 text-center bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 rounded-lg text-xs uppercase tracking-wider transition">
              Launch sandbox
            </a>
            <?php if ($xxeProgress > 0): ?>
            <form method="POST" action="?page=labs&reset=xxe" class="inline"
              onsubmit="return confirm('Reset progress for XXE? This action is irreversible.');">
              <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32)) ?>">
              <button type="submit" class="bg-slate-950 border border-slate-850 hover:bg-red-950/20 hover:text-red-400 hover:border-red-900/30 text-slate-500 p-2.5 rounded-lg transition" title="Reset directive tasks">
                <i class="fas fa-undo-alt text-xs"></i>
              </button>
            </form>
            <?php endif; ?>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
// Hide loading overlay when fully loaded
window.addEventListener('load', function() {
  const spinner = document.getElementById('loadingSpinner');
  if (spinner) {
    spinner.style.opacity = '0';
    spinner.style.transition = 'opacity 0.25s ease-out';
    setTimeout(() => {
      spinner.style.display = 'none';
    }, 250);
  }
  
  // Smoothly grow progress bars
  const progressBars = document.querySelectorAll('.progress-bar');
  progressBars.forEach(bar => {
    const targetWidth = bar.style.width;
    bar.style.width = '0%';
    setTimeout(() => {
      bar.style.width = targetWidth;
    }, 50);
  });
});
</script>

<?php include_once ROOT . '/shared/footer.php'; ?>