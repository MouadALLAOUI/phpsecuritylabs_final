<?php
/**
 * JWT Lab - Level 1 View Template
 */
?>
<div class="max-w-4xl mx-auto space-y-6">
  <!-- Page Header -->
  <div>
    <h1 class="text-2xl font-bold text-slate-100 uppercase tracking-wider flex items-center gap-2">
      <i class="fas fa-key text-blue-500"></i>
      JWT Lab &mdash; Level 1
    </h1>
    <p class="text-xs text-slate-400 mt-1">JSON Web Token Algorithm Confusion</p>
  </div>

  <!-- Mission Card -->
  <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-lg relative overflow-hidden">
    <div class="absolute top-0 left-0 right-0 h-1 bg-amber-500"></div>
    <div class="flex items-start gap-4">
      <div class="bg-amber-500/10 p-3 rounded-lg border border-amber-500/20 text-amber-500">
        <i class="fas fa-bullseye text-xl animate-pulse"></i>
      </div>
      <div>
        <h3 class="text-sm font-bold text-slate-200 uppercase tracking-wide">Mission Objective</h3>
        <p class="text-xs text-slate-400 leading-relaxed mt-2">
          You have discovered a JWT-based authentication system with a potential algorithm confusion vulnerability.
          Your goal is to exploit this to bypass signature verification and submit a forged token with <code>"role": "admin"</code>.
        </p>
      </div>
    </div>
  </div>

  <!-- Error Notice -->
  <?php if (!empty($this->error)): ?>
  <div class="bg-red-950/30 border border-red-500/30 rounded-lg p-4 flex items-start space-x-3 text-red-200" role="alert">
    <i class="fas fa-exclamation-circle text-red-400 mt-0.5"></i>
    <div class="text-xs font-semibold">
      <?= e($this->error) ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- Interactive Form -->
  <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-lg">
    <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-4 flex items-center gap-1.5">
      <i class="fas fa-user-shield text-blue-500"></i> Submit Forged JWT Token
    </h3>
    
    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-2" for="token">Operational JWT Token</label>
        <textarea name="token" id="token" rows="4" required
          placeholder="header.payload.signature"
          class="appearance-none block w-full px-3.5 py-3 border border-slate-700 bg-slate-950 text-slate-200 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-xs font-mono transition"><?= htmlspecialchars($this->token) ?></textarea>
        <small class="block text-[10px] text-slate-500 mt-1 font-mono">Format: header.payload.signature</small>
      </div>

      <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent text-xs font-bold uppercase tracking-wider rounded-lg text-white bg-blue-600 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150">
        <i class="fas fa-key mr-2"></i> Verify Token Signature
      </button>
    </form>

    <!-- Response Container -->
    <?php if ($this->result !== null): ?>
    <div class="mt-6 border-t border-slate-800 pt-6">
      <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-3">Verification stream:</h4>
      <pre class="bg-slate-950 border border-slate-850 p-4 rounded-lg overflow-auto text-xs font-mono text-slate-300 max-h-[300px] leading-relaxed"><?= htmlspecialchars($this->result) ?></pre>
    </div>
    <?php endif; ?>
  </div>

  <!-- Success Banner -->
  <?php if ($this->solved): ?>
  <div class="bg-slate-900 border border-teal-500 rounded-xl p-6 shadow-lg relative overflow-hidden">
    <div class="absolute top-0 left-0 right-0 h-1 bg-teal-500"></div>
    <div class="flex items-start gap-4">
      <div class="bg-teal-500/10 p-3 rounded-lg border border-teal-500/20 text-teal-400">
        <i class="fas fa-check-circle text-xl"></i>
      </div>
      <div>
        <h3 class="text-sm font-bold text-teal-400 uppercase tracking-wide">Simulation Completed</h3>
        <p class="text-xs text-slate-400 mt-2">
          Admin privileges established. You successfully bypassed signature verification using a "none" algorithm!
        </p>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Hint briefing -->
  <div class="text-xs text-slate-500 pt-4 border-t border-slate-850">
    <details class="group cursor-pointer">
      <summary class="font-semibold text-slate-400 hover:text-slate-300 list-none flex items-center gap-1.5 transition">
        <i class="fas fa-info-circle text-blue-500"></i> Intelligence Briefing (Hint)
      </summary>
      <p class="text-slate-500 pl-5 mt-2 leading-relaxed">
        Decode the default token using online tools (like jwt.io), modify the header algorithm from <code>"alg": "HS256"</code> to <code>"alg": "none"</code>, change the payload role from <code>"role": "user"</code> to <code>"role": "admin"</code>, base64 encode them back without trailing signature dots (keep the final dot: <code>header.payload.</code>), and submit the result.
      </p>
    </details>
  </div>
</div>
