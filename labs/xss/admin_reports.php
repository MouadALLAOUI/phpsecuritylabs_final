<?php
// Simulated admin review panel for stored XSS
// Loads all reports and displays them without sanitization

include_once ROOT . '/shared/header.php';
include_once ROOT . '/shared/sidebar.php';

use App\Core\Database;

$db = Database::getInstance('labs'); // connects to challenges DB
$sql = "SELECT id, agent_id, report, reviewed_by_admin, created_at FROM reports ORDER BY created_at DESC";
$stmt = $db->query($sql);
$reports = $stmt->fetchAll();

// Mark a report as reviewed if requested (optional)
if (isset($_GET['mark_reviewed']) && is_numeric($_GET['mark_reviewed'])) {
  $id = $_GET['mark_reviewed'];
  $update = "UPDATE reports SET reviewed_by_admin = 1 WHERE id = :id";
  $db->query($update, ['id' => $id]);
  // header('Location: ?page=xss_admin_reports');
  exit;
}

// Pagination configuration
$itemsPerPage = 15;
$totalItems = count($reports);
$totalPages = max(1, ceil($totalItems / $itemsPerPage));
$currentPage = isset($_GET['page_num']) && is_numeric($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
$currentPage = max(1, min($currentPage, $totalPages));
$offset = ($currentPage - 1) * $itemsPerPage;
$paginatedReports = array_slice($reports, $offset, $itemsPerPage);
?>
<div class="lg:ml-64 p-6">
  <div class="max-w-6xl mx-auto">
    <div class="bg-red-950/50 border-l-4 border-red-500 p-4 mb-6">
      <div class="flex items-center gap-3">
        <i class="fas fa-user-shield text-red-400"></i>
        <div>
          <p class="font-semibold text-red-300">ADMIN REVIEW TERMINAL</p>
          <p class="text-sm text-red-200/80">Any stored JavaScript in reports will execute here</p>
        </div>
      </div>
    </div>

    <?php if (empty($reports)): ?>
    <div class="bg-slate-800/30 border border-slate-700 rounded-sm overflow-hidden">
      <div class="empty-state" style="text-align: center; padding: 60px 20px;">
        <i class="fas fa-file-alt" style="font-size: 4rem; color: #475569; margin-bottom: 20px;"></i>
        <h3 class="text-lg font-medium text-slate-300" style="margin-bottom: 10px;">No Reports Available</h3>
        <p class="text-slate-400 text-sm">Agent field reports will appear here once submitted.</p>
        <p class="text-slate-500 text-xs" style="margin-top: 15px;">Submit a report through the Cases terminal to begin.</p>
      </div>
    </div>
    <?php else: ?>
    <div class="bg-slate-800/30 border border-slate-700 rounded-sm overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-slate-800 text-slate-300">
          <tr>
            <th class="px-4 py-2 text-left">ID</th>
            <th class="px-4 py-2 text-left">Agent</th>
            <th class="px-4 py-2 text-left">Report</th>
            <th class="px-4 py-2 text-left">Status</th>
            <th class="px-4 py-2 text-left">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($paginatedReports as $r): ?>
          <tr class="border-t border-slate-700">
            <td class="px-4 py-2 font-mono text-xs"><?= $r['id'] ?></td>
            <td class="px-4 py-2 font-mono text-xs"><?= $r['agent_id'] ?></td>
            <td class="px-4 py-2">
              <!-- ⚠️ VULNERABLE: directly outputs stored report content -->
              <?= $r['report'] ?>
            </td>
            <td class="px-4 py-2">
              <?= $r['reviewed_by_admin'] ? 'Reviewed' : '<span class="text-amber-400">Pending</span>' ?>
            </td>
            <td class="px-4 py-2">
              <?php if (!$r['reviewed_by_admin']): ?>
              <a href="?page=xss_admin_reports&mark_reviewed=<?= $r['id'] ?>" class="text-blue-400 text-xs">Mark
                reviewed</a>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      
      <!-- Pagination Controls -->
      <?php if ($totalPages > 1): ?>
      <div class="pagination-controls" style="padding: 15px; display: flex; justify-content: center; gap: 5px; flex-wrap: wrap; border-top: 1px solid #334155;">
        <?php if ($currentPage > 1): ?>
          <a href="?page=xss_admin_reports&page_num=<?= $currentPage - 1 ?>" class="text-blue-400 text-xs hover:text-blue-300">
            <i class="fas fa-chevron-left"></i> Prev
          </a>
        <?php endif; ?>
        
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <a href="?page=xss_admin_reports&page_num=<?= $i ?>" 
             class="text-xs px-3 py-1 rounded <?= $i === $currentPage ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-700' ?>">
            <?= $i ?>
          </a>
        <?php endfor; ?>
        
        <?php if ($currentPage < $totalPages): ?>
          <a href="?page=xss_admin_reports&page_num=<?= $currentPage + 1 ?>" class="text-blue-400 text-xs hover:text-blue-300">
            Next <i class="fas fa-chevron-right"></i>
          </a>
        <?php endif; ?>
      </div>
      <p class="text-xs text-slate-500" style="text-align: center; padding: 10px;">
        Showing <?= $offset + 1 ?>-<?= min($offset + $itemsPerPage, $totalItems) ?> of <?= $totalItems ?> entries
      </p>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
</div>
<?php include_once ROOT . '/shared/footer.php'; ?>