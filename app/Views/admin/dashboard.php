<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - MIL-OPS</title>
  <?php include_once ROOT . '/shared/military-ui/header.php'; ?>
</head>

<body class="mil-ops-bg">
  <?php include_once ROOT . '/shared/sidebar.php'; ?>

  <main class="mil-ops-main">
    <div class="mission-header">
      <h1><span class="icon">⚔️</span> COMMAND OVERVIEW</h1>
      <p class="objective">Monitor all operator progress and manage training assignments</p>
      <div class="clearance-badge">CLEARANCE: ADMIN</div>
    </div>

    <?php if (isset($_SESSION['admin_message'])): ?>
    <div class="system-alert success"><?= htmlspecialchars($_SESSION['admin_message']) ?></div>
    <?php unset($_SESSION['admin_message']); ?>
    <?php endif; ?>

    <div class="terminal-panel">
      <div class="terminal-header">
        <span class="terminal-title">OPERATOR ROSTER</span>
        <span class="blink">● LIVE FEED</span>
      </div>

      <table class="data-table">
        <thead>
          <tr>
            <th>CODENAME</th>
            <th>USERNAME</th>
            <th>XSS</th>
            <th>SQLi</th>
            <th>UPLOAD</th>
            <th>TOTAL</th>
            <th>ACTIONS</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $user): ?>
          <tr>
            <td class="codename"><?= htmlspecialchars($user['codename'] ?? 'N/A') ?></td>
            <td><?= htmlspecialchars($user['username']) ?></td>
            <td><span class="badge <?= $user['xss_count'] > 0 ? 'success' : '' ?>"><?= $user['xss_count'] ?>/3</span>
            </td>
            <td><span class="badge <?= $user['sqli_count'] > 0 ? 'success' : '' ?>"><?= $user['sqli_count'] ?>/2</span>
            </td>
            <td><span
                class="badge <?= $user['upload_count'] > 0 ? 'success' : '' ?>"><?= $user['upload_count'] ?>/2</span>
            </td>
            <td><strong><?= $user['total_count'] ?></strong></td>
            <td class="actions">
              <form method="POST" action="?page=admin&action=reset" style="display:inline;"
                onsubmit="return confirm('Reset all progress for this operator?');">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32)) ?>">
                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                <button type="submit" name="lab_name" value="xss" class="btn-small btn-warning">XSS</button>
                <button type="submit" name="lab_name" value="sqli" class="btn-small btn-warning">SQLi</button>
                <button type="submit" name="lab_name" value="file_upload" class="btn-small btn-warning">UPLOAD</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="intel-brief">
      <h3>ADMINISTRATIVE CONTROLS</h3>
      <ul>
        <li>Use reset buttons to clear specific lab progress for any operator</li>
        <li>All actions are logged in the system audit trail</li>
        <li>Contact system administrator for account management</li>
      </ul>
    </div>
  </main>

  <?php include_once ROOT . '/shared/military-ui/footer.php'; ?>
</body>

</html>