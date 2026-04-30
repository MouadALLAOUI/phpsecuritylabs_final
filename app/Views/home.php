<?php

/**
 * Landing Page View – Home / Dashboard
 * Loaded by the Router when ?page=home
 */

include_once ROOT . '/shared/header.php';
include_once ROOT . '/shared/sidebar.php';

// Get user progress if logged in
$totalCompleted = 0;
$labProgress = [];
if (isset($_SESSION['user_id'])) {
    $auth = new \App\Core\Auth();
    $userId = $_SESSION['user_id'];
    $completed = $auth->getCompletedChallenges($userId);
    $totalCompleted = count($completed);
    
    // Group by lab
    foreach ($completed as $c) {
        $lab = $c['lab_name'];
        if (!isset($labProgress[$lab])) {
            $labProgress[$lab] = 0;
        }
        $labProgress[$lab]++;
    }
}
?>

<div class="lg:ml-64 p-6">
  <div class="max-w-5xl mx-auto">
    <!-- Welcome Section -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">
        <i class="fas fa-shield-halved text-indigo-600 mr-2"></i>Defense Intelligence Dashboard
      </h1>
      <p class="mt-2 text-sm text-gray-600">
        Welcome back, <span class="font-medium text-gray-800"><?= htmlspecialchars($_SESSION['username'] ?? 'Agent') ?></span>. 
        Your security clearance is <span class="font-mono bg-green-100 text-green-800 px-2 py-0.5 rounded">ACTIVE</span>.
      </p>
    </div>

    <!-- System Status Panel -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <!-- Card 1 -->
      <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-400">
        <div class="flex items-center">
          <i class="fas fa-server text-green-500 text-2xl mr-3"></i>
          <div>
            <p class="text-sm text-gray-500">System Status</p>
            <p class="text-lg font-semibold text-gray-900">All Systems Operational</p>
          </div>
        </div>
      </div>

      <!-- Card 2 -->
      <div class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-400">
        <div class="flex items-center">
          <i class="fas fa-flask text-blue-500 text-2xl mr-3"></i>
          <div>
            <p class="text-sm text-gray-500">Active Labs</p>
            <p class="text-lg font-semibold text-gray-900">3 Modules Available</p>
          </div>
        </div>
      </div>

      <!-- Card 3 -->
      <div class="bg-white rounded-lg shadow p-5 border-l-4 border-purple-400">
        <div class="flex items-center">
          <i class="fas fa-trophy text-purple-500 text-2xl mr-3"></i>
          <div>
            <p class="text-sm text-gray-500">Challenges Completed</p>
            <p class="text-lg font-semibold text-gray-900"><?= $totalCompleted ?></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Your Progress Section -->
    <?php if ($totalCompleted > 0): ?>
    <div class="bg-white rounded-lg shadow p-6 mb-8">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">
        <i class="fas fa-chart-bar text-indigo-500 mr-2"></i>Your Progress
      </h2>
      <div class="space-y-4">
        <?php foreach ($labProgress as $lab => $count): ?>
        <div>
          <div class="flex justify-between items-center mb-1">
            <span class="text-sm font-medium text-gray-700 capitalize"><?= htmlspecialchars(str_replace('_', ' ', $lab)) ?></span>
            <span class="text-xs text-gray-500"><?= $count ?> challenge(s)</span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-indigo-600 h-2 rounded-full" style="width: <?= min(100, ($count / 3) * 100) ?>%"></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Quick Access & Labs Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Quick Access -->
      <div>
        <div class="bg-white rounded-lg shadow p-5">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-bolt text-indigo-500 mr-2"></i>Quick Access
          </h2>
          <div class="space-y-3">
            <a href="?page=labs" class="flex items-center px-3 py-2 bg-gray-50 rounded-md hover:bg-gray-100 transition">
              <i class="fas fa-flask text-indigo-500 w-5 mr-2"></i>
              <span class="text-sm font-medium text-gray-700">All Training Labs</span>
            </a>
            <a href="?page=xss&lvl=1" class="flex items-center px-3 py-2 bg-gray-50 rounded-md hover:bg-gray-100 transition">
              <i class="fas fa-code text-yellow-500 w-5 mr-2"></i>
              <span class="text-sm font-medium text-gray-700">XSS Lab (Start Level 1)</span>
            </a>
            <a href="?page=sqli&lvl=1" class="flex items-center px-3 py-2 bg-gray-50 rounded-md hover:bg-gray-100 transition">
              <i class="fas fa-database text-red-500 w-5 mr-2"></i>
              <span class="text-sm font-medium text-gray-700">SQL Injection Lab</span>
            </a>
            <a href="?page=file_upload&lvl=1" class="flex items-center px-3 py-2 bg-gray-50 rounded-md hover:bg-gray-100 transition">
              <i class="fas fa-upload text-green-500 w-5 mr-2"></i>
              <span class="text-sm font-medium text-gray-700">File Upload Lab</span>
            </a>
          </div>
        </div>
      </div>

      <!-- Lab Status Overview -->
      <div>
        <div class="bg-white rounded-lg shadow p-5">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-list-check text-gray-500 mr-2"></i>Lab Status
          </h2>
          <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
              <div class="flex items-center">
                <i class="fas fa-code text-yellow-500 w-5 mr-2"></i>
                <span class="text-sm font-medium text-gray-700">XSS Lab</span>
              </div>
              <span class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded">3 Levels</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
              <div class="flex items-center">
                <i class="fas fa-database text-red-500 w-5 mr-2"></i>
                <span class="text-sm font-medium text-gray-700">SQL Injection</span>
              </div>
              <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded">1 Level</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
              <div class="flex items-center">
                <i class="fas fa-upload text-green-500 w-5 mr-2"></i>
                <span class="text-sm font-medium text-gray-700">File Upload</span>
              </div>
              <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded">1 Level</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
include_once ROOT . '/shared/footer.php';
?>
