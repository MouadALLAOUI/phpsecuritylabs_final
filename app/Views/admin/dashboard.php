<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Instructor Panel - Cyber Range Console</title>
  <?php include_once ROOT . '/shared/military-ui/header.php'; ?>
  <style>
    /* Styling scopes for premium console data tables */
    .admin-table-wrapper {
      overflow-x: auto;
      border: 1px solid var(--border-color);
      border-radius: 8px;
      background-color: var(--bg-surface);
    }

    .admin-table {
      width: 100%;
      border-collapse: collapse;
      text-align: left;
      font-size: 13px;
    }

    .admin-table th {
      background-color: rgba(255, 255, 255, 0.02);
      border-bottom: 1px solid var(--border-color);
      padding: 14px 18px;
      font-family: var(--font-sans);
      font-weight: 700;
      color: var(--text-dim);
      text-transform: uppercase;
      font-size: 11px;
      letter-spacing: 0.5px;
    }

    .admin-table td {
      padding: 14px 18px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.02);
      color: var(--text-secondary);
      vertical-align: middle;
    }

    .admin-table tr:hover td {
      background-color: rgba(255, 255, 255, 0.01);
    }

    .instructor-badge {
      font-family: var(--font-mono);
      font-size: 11px;
      font-weight: 600;
      padding: 3px 8px;
      border-radius: 4px;
      text-transform: uppercase;
    }

    .instructor-badge.active {
      background-color: rgba(13, 148, 136, 0.1);
      border: 1px solid rgba(13, 148, 136, 0.2);
      color: #2dd4bf;
    }

    .instructor-badge.inactive {
      background-color: rgba(255, 255, 255, 0.05);
      border: 1px solid var(--border-color);
      color: var(--text-dim);
    }

    /* Small warning reset buttons */
    .reset-btn {
      font-family: var(--font-mono);
      font-size: 10px;
      font-weight: 700;
      padding: 4px 10px;
      border-radius: 4px;
      cursor: pointer;
      text-transform: uppercase;
      background-color: rgba(217, 119, 6, 0.1);
      border: 1px solid rgba(217, 119, 6, 0.3);
      color: var(--mil-amber);
      transition: all 0.2s ease;
    }

    .reset-btn:hover {
      background-color: var(--mil-amber);
      color: white;
      box-shadow: 0 2px 8px rgba(217, 119, 6, 0.3);
    }
  </style>
</head>

<body class="mil-body">
  <?php include_once ROOT . '/shared/sidebar.php'; ?>

  <!-- HEADER -->
  <div class="mission-header">
    <div class="mission-title">
      <i class="fas fa-user-shield text-red-500"></i>
      <span>COMMAND CENTER OVERVIEW</span>
    </div>
    <div class="mission-grid">
      <div class="mission-stat">
        <div class="stat-label">Objective</div>
        <div class="stat-value">Audit operator progression and command sandbox states</div>
      </div>
      <div class="mission-stat">
        <div class="stat-label">Clearance Level</div>
        <div class="stat-value danger">COMMAND SYSTEM ADMIN</div>
      </div>
    </div>
  </div>

  <!-- Dynamic system alert response -->
  <?php if (isset($_SESSION['admin_message'])): ?>
    <div class="mil-hint-box mb-6" style="border-left-color: var(--mil-green); background-color: rgba(13, 148, 136, 0.05);">
      <strong class="text-teal-400"><i class="fas fa-check-circle"></i> Directives Succeeded</strong>
      <p class="text-xs text-slate-400 mt-1"><?= htmlspecialchars($_SESSION['admin_message']) ?></p>
    </div>
    <?php unset($_SESSION['admin_message']); ?>
  <?php endif; ?>

  <!-- ROSTER TERMINAL CARD -->
  <div class="terminal-panel">
    <div class="terminal-header justify-between">
      <div class="flex items-center gap-2">
        <i class="fas fa-users text-blue-500"></i>
        <span>OPERATOR ROSTER</span>
      </div>
      <div class="flex items-center gap-2 bg-blue-600/10 px-2 py-0.5 border border-blue-500/25 rounded text-[10px] text-blue-400 font-bold font-mono">
        <span class="h-1.5 w-1.5 rounded-full bg-blue-500 animate-pulse"></span>
        LIVE FEED STATUS
      </div>
    </div>

    <div class="terminal-body p-0">
      <div class="admin-table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Codename</th>
              <th>Username</th>
              <th>XSS Lab</th>
              <th>SQLi Lab</th>
              <th>Upload Lab</th>
              <th>Accumulated</th>
              <th class="text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($users)): ?>
            <tr>
              <td colspan="7" class="text-center py-12 text-slate-500 italic font-mono">
                <i class="fas fa-users-slash text-2xl block mb-2"></i>
                No sandbox operators currently registered in session.
              </td>
            </tr>
            <?php else: ?>
            <?php foreach ($users as $user): ?>
            <tr>
              <td class="font-mono font-bold text-slate-200 uppercase tracking-wider"><?= htmlspecialchars($user['codename'] ?? 'N/A') ?></td>
              <td class="font-mono text-slate-400">@<?= htmlspecialchars($user['username']) ?></td>
              <td>
                <span class="instructor-badge <?= $user['xss_count'] > 0 ? 'active' : 'inactive' ?>">
                  <?= $user['xss_count'] ?>/3 Completed
                </span>
              </td>
              <td>
                <span class="instructor-badge <?= $user['sqli_count'] > 0 ? 'active' : 'inactive' ?>">
                  <?= $user['sqli_count'] ?>/2 Completed
                </span>
              </td>
              <td>
                <span class="instructor-badge <?= $user['upload_count'] > 0 ? 'active' : 'inactive' ?>">
                  <?= $user['upload_count'] ?>/2 Completed
                </span>
              </td>
              <td class="font-mono font-bold text-blue-400"><?= $user['total_count'] ?> Tasks</td>
              <td class="text-right">
                <form method="POST" action="?page=admin&action=reset" style="display:inline;"
                  onsubmit="return confirm('Reset all progress for this operator? This action cannot be undone.');">
                  <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32)) ?>">
                  <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                  <div class="flex justify-end gap-1.5">
                    <button type="submit" name="lab_name" value="xss" class="reset-btn" title="Reset XSS Progress">XSS</button>
                    <button type="submit" name="lab_name" value="sqli" class="reset-btn" title="Reset SQLi Progress">SQLi</button>
                    <button type="submit" name="lab_name" value="file_upload" class="reset-btn" title="Reset Upload Progress">Upload</button>
                  </div>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- REGULATION DOCUMENT -->
  <div class="terminal-panel">
    <div class="terminal-header">
      <i class="fas fa-circle-info text-blue-500"></i>
      <span>ADMINISTRATIVE REGULATIONS</span>
    </div>
    <div class="terminal-body font-mono text-xs text-slate-400">
      <ul class="space-y-2.5">
        <li class="flex items-start gap-2.5"><span class="text-blue-500">&bull;</span> Sandbox states may be reset to clear progress flags for testing.</li>
        <li class="flex items-start gap-2.5"><span class="text-blue-500">&bull;</span> Roster operations and audit triggers are logged persistently.</li>
      </ul>
    </div>
  </div>

  <?php include_once ROOT . '/shared/military-ui/footer.php'; ?>
</body>

</html>