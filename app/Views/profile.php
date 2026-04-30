<?php
include_once ROOT . '/shared/header.php';
include_once ROOT . '/shared/sidebar.php';
?>
<div class="lg:ml-64 p-6">
  <div class="max-w-4xl mx-auto">
    <!-- User info card -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
      <div class="flex items-center">
        <i class="fas fa-user-circle text-indigo-600 text-5xl mr-4"></i>
        <div>
          <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($user['username']) ?></h1>
          <p class="text-gray-600"><?= htmlspecialchars($user['email']) ?></p>
          <p class="text-sm mt-1">
            <span
              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
              <?= ucfirst(htmlspecialchars($user['role'])) ?>
            </span>
            <?php if ($user['is_admin']): ?>
              <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 ml-2">
                Admin
              </span>
            <?php endif; ?>
          </p>
        </div>
      </div>
    </div>

    <!-- Achievements / Completed Challenges -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
      <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
        <i class="fas fa-trophy text-yellow-500 mr-2"></i> Achievements
      </h2>
      <?php if (empty($completed)): ?>
        <p class="text-gray-500 italic">No challenges completed yet. Start a lab to earn achievements.</p>
      <?php else: ?>
        <div class="grid gap-3">
          <?php foreach ($completed as $challenge): ?>
            <div class="border-l-4 border-green-400 bg-green-50 p-3 rounded">
              <p class="font-medium text-gray-800">
                <?= strtoupper(htmlspecialchars($challenge['lab_name'])) ?> –
                <?= htmlspecialchars($challenge['challenge']) ?>
              </p>
              <p class="text-xs text-gray-500">Completed: <?= htmlspecialchars($challenge['completed_at']) ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Settings placeholder -->
    <div class="bg-white rounded-lg shadow p-6">
      <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
        <i class="fas fa-cog text-gray-600 mr-2"></i> Settings
      </h2>
      <p class="text-gray-500">Additional settings (theme, notifications, etc.) will be available in future updates.</p>
      <div class="mt-4">
        <a href="?page=logout" class="text-red-600 hover:text-red-800 text-sm">Sign Out</a>
      </div>
    </div>
  </div>
</div>
<?php include_once ROOT . '/shared/footer.php'; ?>