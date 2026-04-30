<?php include_once ROOT . '/shared/header.php'; ?>
<div class="min-h-screen flex items-center justify-center bg-gray-100">
  <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
    <div class="text-center mb-6">
      <i class="fas fa-shield-haltered text-indigo-600 text-4xl"></i>
      <h2 class="text-2xl font-bold mt-2">PHP Security Labs</h2>
      <p class="text-gray-600">Sign in to access training</p>
    </div>

    <?php if (isset($_SESSION['login_error'])): ?>
      <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
        <?= htmlspecialchars($_SESSION['login_error']) ?>
      </div>
      <?php unset($_SESSION['login_error']); ?>
    <?php endif; ?>

    <form method="POST" action="?page=login&action=do">
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32)) ?>">
      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="username">Username</label>
        <input type="text" name="username" id="username" required
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
      </div>
      <div class="mb-6">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
        <input type="password" name="password" id="password" required
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
      </div>
      <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition">
        Sign In
      </button>
    </form>

    <div class="mt-4 text-center text-sm text-gray-500">
      <p>Test credentials: <span class="font-mono">ghost / password</span> or <span class="font-mono">admin_root /
          password</span></p>
    </div>
  </div>
</div>
<?php include_once ROOT . '/shared/footer.php'; ?>