<?php

/**
 * Simulated Admin Review Panel
 * Displays recent search queries and executes any JavaScript payloads stored in them.
 * This is where the XSS payload will trigger when the admin views the log.
 */
include_once ROOT . '/shared/military-ui/header.php';
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
    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6" style="background: rgba(255,0,0,0.1); border-color: #ff0040;">
      <div class="flex">
        <i class="fas fa-user-shield text-red-600 mr-3"></i>
        <div>
          <p class="font-semibold text-red-800" style="color: #ff4d4d;">Admin Review Panel (Simulated)</p>
          <p class="text-sm text-red-700" style="color: #ff9999;">Any JavaScript injected into search queries will execute when the admin views
            this page.</p>
        </div>
      </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6" style="background: #0a0f1a; border: 1px solid #1a2332;">
      <h2 class="text-lg font-medium text-gray-900 mb-4" style="color: #00ff41; font-family: monospace;">Recent Search Logs</h2>
      
      <?php 
      // Pagination configuration
      $itemsPerPage = 10;
      $totalItems = count($searches);
      $totalPages = max(1, ceil($totalItems / $itemsPerPage));
      $currentPage = isset($_GET['page_num']) && is_numeric($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
      $currentPage = max(1, min($currentPage, $totalPages));
      $offset = ($currentPage - 1) * $itemsPerPage;
      $paginatedSearches = array_slice($searches, $offset, $itemsPerPage);
      ?>
      
      <?php if (empty($searches)): ?>
        <div class="empty-state" style="text-align: center; padding: 40px; color: #6b7280;">
          <i class="fas fa-folder-open" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;"></i>
          <p class="text-gray-500 italic">No search logs available yet.</p>
          <p class="text-sm" style="margin-top: 10px;">Search queries will appear here when agents use the search function.</p>
        </div>
      <?php else: ?>
        <ul class="divide-y divide-gray-200" style="border-color: #1a2332;">
          <?php foreach ($paginatedSearches as $entry): ?>
            <li class="py-3" style="border-color: #1a2332;">
              <p class="text-sm text-gray-500 font-mono" style="color: #00ff41;"><?= htmlspecialchars($entry) ?></p>
              <!-- ⚠️ VULNERABLE: the raw search term is echoed without escaping -->
              <div class="mt-1 text-sm text-gray-700" style="color: #c0c0c0;">
                <strong style="color: #ff9500;">Preview:</strong>
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
        
        <!-- Pagination Controls -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination-controls" style="margin-top: 20px; display: flex; justify-content: center; gap: 5px; flex-wrap: wrap;">
          <?php if ($currentPage > 1): ?>
            <a href="?page=xss_admin_panel&page_num=<?= $currentPage - 1 ?>" class="mil-btn mil-btn-secondary" style="padding: 5px 10px; font-size: 12px;">
              <i class="fas fa-chevron-left"></i> Prev
            </a>
          <?php endif; ?>
          
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=xss_admin_panel&page_num=<?= $i ?>" 
               class="mil-btn <?= $i === $currentPage ? 'mil-btn-primary' : 'mil-btn-secondary' ?>" 
               style="padding: 5px 10px; font-size: 12px; min-width: 30px;">
              <?= $i ?>
            </a>
          <?php endfor; ?>
          
          <?php if ($currentPage < $totalPages): ?>
            <a href="?page=xss_admin_panel&page_num=<?= $currentPage + 1 ?>" class="mil-btn mil-btn-secondary" style="padding: 5px 10px; font-size: 12px;">
              Next <i class="fas fa-chevron-right"></i>
            </a>
          <?php endif; ?>
        </div>
        <p class="text-xs text-gray-500" style="text-align: center; margin-top: 10px;">
          Showing <?= $offset + 1 ?>-<?= min($offset + $itemsPerPage, $totalItems) ?> of <?= $totalItems ?> entries
        </p>
        <?php endif; ?>
      <?php endif; ?>
    </div>

    <div class="mt-6 text-xs text-gray-400" style="color: #6b7280;">
      <p>To complete the challenge: Inject a payload into the agent search that steals the admin's cookie when this page
        is loaded.</p>
    </div>
  </div>
</div>
<?php include_once ROOT . '/shared/military-ui/footer.php'; ?>