<?php
include_once ROOT . '/shared/header.php';
include_once ROOT . '/shared/sidebar.php';
?>
<div class="lg:ml-64 p-6 min-h-[85vh] theme-transition">
  <div class="max-w-4xl mx-auto space-y-6">
    
    <!-- User Info/Clearance Card -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-lg flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left gap-5">
      <div class="h-16 w-16 bg-blue-600/10 border border-blue-500/20 rounded-2xl flex items-center justify-center text-blue-500 text-3xl">
        <i class="fas fa-user-shield"></i>
      </div>
      <div class="space-y-2 flex-1">
        <h1 class="text-xl font-bold text-slate-100 uppercase tracking-wide"><?= htmlspecialchars($user['username']) ?></h1>
        <p class="text-xs text-slate-400 font-mono"><?= htmlspecialchars($user['email']) ?></p>
        <div class="flex flex-wrap justify-center sm:justify-start gap-2 pt-1">
          <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-950 border border-slate-800 text-slate-300">
            Clearance: <?= ucfirst(htmlspecialchars($user['role'])) ?>
          </span>
          <?php if ($user['is_admin']): ?>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-red-950/20 border border-red-500/20 text-red-400">
              Commander Clearance
            </span>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Achievements / Completed Challenges -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-md">
      <h2 class="text-sm font-bold text-slate-100 uppercase tracking-wider mb-5 flex items-center gap-2">
        <i class="fas fa-trophy text-yellow-500 text-sm"></i> Earned Directives Completed
      </h2>
      <?php if (empty($completed)): ?>
        <p class="text-xs text-slate-400 italic bg-slate-950/40 p-4 border border-slate-850 rounded-lg text-center">
          <i class="fas fa-users-slash text-xl text-slate-600 block mb-2"></i> No sandbox accomplishments recorded in session. Start a training lab to earn badges.
        </p>
      <?php else: ?>
        <div class="grid gap-3">
          <?php foreach ($completed as $challenge): ?>
            <div class="border-l-4 border-teal-500 bg-slate-950/40 p-4 rounded-r-lg border border-slate-850 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
              <div>
                <p class="text-xs font-bold text-slate-200 uppercase tracking-wider">
                  <?= strtoupper(htmlspecialchars($challenge['lab_name'])) ?> &mdash; <?= htmlspecialchars($challenge['challenge']) ?>
                </p>
                <p class="text-[10px] text-slate-500 font-mono mt-1">Audit log completion ID: #<?= bin2hex(random_bytes(4)) ?></p>
              </div>
              <span class="text-[10px] font-bold font-mono text-teal-400 bg-teal-950/25 px-2 py-0.5 border border-teal-900/30 rounded uppercase tracking-wider">
                Completed: <?= date('Y-m-d H:i', strtotime($challenge['completed_at'])) ?>
              </span>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Settings Placeholder panel -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-md flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h2 class="text-sm font-bold text-slate-100 uppercase tracking-wider flex items-center gap-2">
          <i class="fas fa-gear text-slate-400"></i> Account Directives
        </h2>
        <p class="text-xs text-slate-400 mt-1.5">Manage operator session controls and personalizations.</p>
      </div>
      <div class="flex items-center gap-3">
        <a href="?page=logout" class="bg-red-600/10 border border-red-500/20 hover:bg-red-600 hover:text-white text-red-400 font-bold px-4 py-2 rounded-lg text-xs uppercase tracking-wider transition">
          Sign Out Link
        </a>
      </div>
    </div>

  </div>
</div>
<?php include_once ROOT . '/shared/footer.php'; ?>