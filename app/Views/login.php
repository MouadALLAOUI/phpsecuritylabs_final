<?php include_once ROOT . '/shared/header.php'; ?>

<div class="min-h-[80vh] flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
  <div class="max-w-md w-full space-y-8 bg-slate-900 border border-slate-800 p-8 rounded-xl shadow-2xl transition duration-200">
    
    <!-- Branding Header -->
    <div class="text-center">
      <div class="mx-auto h-12 w-12 bg-blue-600/10 border border-blue-500/20 rounded-xl flex items-center justify-center">
        <i class="fas fa-shield-alt text-blue-500 text-2xl"></i>
      </div>
      <h2 class="mt-4 text-2xl font-bold text-slate-100 uppercase tracking-wider">Cyber Range Login</h2>
      <p class="mt-2 text-xs text-slate-400">Sign in to access your cybersecurity training labs</p>
    </div>

    <!-- Error notice -->
    <?php if (isset($_SESSION['login_error'])): ?>
      <div class="bg-red-950/30 border border-red-500/30 rounded-lg p-4 mb-6 flex items-start space-x-3 text-red-200" role="alert">
        <i class="fas fa-exclamation-circle text-red-400 mt-0.5"></i>
        <div class="text-xs font-semibold">
          <?= e($_SESSION['login_error']) ?>
        </div>
      </div>
      <?php unset($_SESSION['login_error']); ?>
    <?php endif; ?>

    <!-- Form -->
    <form class="mt-8 space-y-6" method="POST" action="?page=login&action=do">
      <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::getCsrfToken() ?>">
      
      <div class="space-y-4 rounded-md">
        <!-- Username input -->
        <div>
          <label class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-2" for="username">Operator Username</label>
          <input type="text" name="username" id="username" required
            maxlength="50" autocomplete="username"
            placeholder="Enter username"
            class="appearance-none relative block w-full px-3.5 py-3 border border-slate-700 bg-slate-950 text-slate-200 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm transition">
        </div>
        
        <!-- Password input -->
        <div>
          <label class="block text-slate-400 text-xs font-bold uppercase tracking-wider mb-2" for="password">Operational Access Code</label>
          <input type="password" name="password" id="password" required
            maxlength="100" autocomplete="current-password"
            placeholder="Enter password"
            class="appearance-none relative block w-full px-3.5 py-3 border border-slate-700 bg-slate-950 text-slate-200 placeholder-slate-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm transition">
        </div>
      </div>

      <!-- Submit button -->
      <div>
        <button type="submit" 
          class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-xs font-bold uppercase tracking-wider rounded-lg text-white bg-blue-600 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150">
          <span class="absolute left-0 inset-y-0 flex items-center pl-3">
            <i class="fas fa-lock text-blue-400 group-hover:text-blue-300 transition"></i>
          </span>
          Establish Secure Link
        </button>
      </div>
    </form>

    <!-- Demo details box -->
    <div class="mt-6 border-t border-slate-800/80 pt-6">
      <div class="bg-slate-950/60 border border-slate-800 rounded-lg p-4 text-center">
        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-2"><i class="fas fa-info-circle text-blue-500 mr-1.5"></i> Sandbox Operator Credentials</span>
        <div class="flex justify-center items-center space-x-2 text-xs font-mono text-slate-300">
          <div>
            <span class="text-slate-500">Student:</span> <span class="bg-slate-900 px-1.5 py-0.5 rounded text-blue-400">ghost / password</span>
          </div>
          <span class="text-slate-700">|</span>
          <div>
            <span class="text-slate-500">Admin:</span> <span class="bg-slate-900 px-1.5 py-0.5 rounded text-red-400">admin_root / password</span>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<?php include_once ROOT . '/shared/footer.php'; ?>