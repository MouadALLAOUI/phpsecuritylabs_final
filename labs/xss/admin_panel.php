<?php

/**
 * Simulated Admin Review Panel
 * Displays recent search queries and executes any JavaScript payloads stored in them.
 * This is where the XSS payload will trigger when the admin views the log.
 */
include_once ROOT . '/shared/header.php';
include_once ROOT . '/shared/sidebar.php';


$logDir = ROOT . '/storage/logs';
if (!is_dir($logDir)) {
  mkdir($logDir, 0755, true);
}
$logFile = $logDir . '/search_history.log';

$searches = [];
if (file_exists($logFile)) {
  $lines = file($logFile, FILE_IGNORE_NEW_LINES);
  $searches = array_reverse($lines); // latest first
}

// If a search was just performed, log it
if (isset($_POST['search'])) {
  $query = $_POST['search'];
  $entry = date('[Y-m-d H:i:s]') . ' ' . $query;
  file_put_contents($logFile, $entry . PHP_EOL, FILE_APPEND);
  $searches = array_reverse(file($logFile, FILE_IGNORE_NEW_LINES));
}
?>
<div class="lg:ml-64 p-6">
  <div class="max-w-4xl mx-auto">
    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
      <div class="flex">
        <i class="fas fa-user-shield text-red-600 mr-3"></i>
        <div>
          <p class="font-semibold text-red-800">Admin Review Panel (Simulated)</p>
          <p class="text-sm text-red-700">Any JavaScript injected into search queries will execute when the admin views
            this page.</p>
        </div>
      </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
      <h2 class="text-lg font-medium text-gray-900 mb-4">Recent Search Logs</h2>
      <?php if (empty($searches)): ?>
        <p class="text-gray-500 italic">No searches yet.</p>
      <?php else: ?>
        <ul class="divide-y divide-gray-200">
          <?php foreach ($searches as $entry): ?>
            <li class="py-3">
              <p class="text-sm text-gray-500 font-mono"><?= htmlspecialchars($entry) ?></p>
              <!-- ⚠️ VULNERABLE: the raw search term is echoed without escaping -->
              <div class="mt-1 text-sm text-gray-700">
                <strong>Preview:</strong>
                <?php
                // Extract the search term from the log entry (after timestamp)
                $parts = explode(' ', $entry, 2);
                $searchTerm = $parts[1] ?? '';
                echo $searchTerm; // UNSAFE – XSS!
                ?>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>

    <div class="mt-6 text-xs text-gray-400">
      <p>To complete the challenge: Inject a payload into the agent search that steals the admin's cookie when this page
        is loaded.</p>
    </div>
  </div>
</div>
<?php include_once ROOT . '/shared/footer.php'; ?>