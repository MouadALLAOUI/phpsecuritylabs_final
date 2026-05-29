<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Leaderboard - Cyber Range Console</title>
  <?php include_once ROOT . '/shared/military-ui/header.php'; ?>
  <style>
    /* Scope styling to prevent any layout overlap on standard pages */
    .leaderboard-box {
      max-width: 800px;
      margin: 0 auto;
    }

    .ladder-row {
      display: flex;
      align-items: center;
      padding: 16px 20px;
      background-color: var(--bg-surface);
      border: 1px solid var(--border-color);
      border-radius: 8px;
      margin-bottom: 12px;
      transition: all 0.2s ease;
    }

    .ladder-row:hover {
      border-color: var(--border-highlight);
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .rank-num {
      font-family: var(--font-mono);
      font-size: 20px;
      font-weight: 700;
      width: 50px;
      color: var(--text-dim);
    }

    /* Elegant Metallic Ranks */
    .ladder-row:nth-child(1) .rank-num { color: #fbbf24; } /* Gold */
    .ladder-row:nth-child(2) .rank-num { color: #cbd5e1; } /* Silver */
    .ladder-row:nth-child(3) .rank-num { color: #b45309; } /* Bronze */

    .rank-icon-wrapper {
      font-size: 20px;
      margin-right: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 32px;
    }

    .player-details {
      flex: 1;
    }

    .player-codename {
      font-size: 14px;
      font-weight: 700;
      color: var(--text-primary);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .player-username {
      font-family: var(--font-mono);
      font-size: 11px;
      color: var(--text-dim);
      margin-top: 2px;
    }

    .player-score {
      font-family: var(--font-mono);
      font-size: 12px;
      font-weight: 700;
      color: #38bdf8;
      background-color: rgba(56, 189, 248, 0.1);
      border: 1px solid rgba(56, 189, 248, 0.2);
      padding: 6px 14px;
      border-radius: 6px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
  </style>
</head>

<body class="mil-body">
  <?php include_once ROOT . '/shared/sidebar.php'; ?>

  <!-- HEADER -->
  <div class="mission-header">
    <div class="mission-title">
      <i class="fas fa-trophy text-yellow-500"></i>
      <span>OPERATOR LEADERBOARD</span>
    </div>
    <div class="mission-grid">
      <div class="mission-stat">
        <div class="stat-label">Objective</div>
        <div class="stat-value">Breach modules to claim active rank status</div>
      </div>
      <div class="mission-stat">
        <div class="stat-label">CLEARANCE REQUIRED</div>
        <div class="stat-value">STANDARD OPERATOR</div>
      </div>
    </div>
  </div>

  <!-- LEADERBOARD LADDER -->
  <div class="leaderboard-box">
    <div class="flex justify-center items-center gap-4 text-3xl py-4 mb-4">
      <i class="fas fa-medal text-yellow-500"></i>
      <i class="fas fa-medal text-slate-300"></i>
      <i class="fas fa-medal text-amber-700"></i>
    </div>

    <?php if (empty($leaderboard)): ?>
    <div class="terminal-panel">
      <div class="terminal-body text-center py-8">
        <i class="fas fa-users-slash text-slate-500 text-3xl mb-3"></i>
        <p class="text-slate-400 font-semibold uppercase tracking-wider text-xs">No Operator Achievements Logged</p>
        <p class="text-slate-600 text-xs mt-1">Be the first to claim a rank spot!</p>
      </div>
    </div>
    <?php else: ?>
    <div class="space-y-1">
      <?php foreach ($leaderboard as $index => $operator): ?>
      <div class="ladder-row">
        <div class="rank-num">#<?= $index + 1 ?></div>
        <div class="rank-icon-wrapper">
          <?php if ($index === 0): ?><i class="fas fa-medal text-yellow-500"></i>
          <?php elseif ($index === 1): ?><i class="fas fa-medal text-slate-300"></i>
          <?php elseif ($index === 2): ?><i class="fas fa-medal text-amber-700"></i>
          <?php else: ?><i class="fas fa-award text-slate-600 text-sm"></i><?php endif; ?>
        </div>
        <div class="player-details">
          <div class="player-codename"><?= htmlspecialchars($operator['codename'] ?? 'Unknown') ?></div>
          <div class="player-username">@<?= htmlspecialchars($operator['username']) ?></div>
        </div>
        <div class="player-score"><?= $operator['completed_count'] ?> Completed</div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- RULES PANEL -->
    <div class="terminal-panel mt-6">
      <div class="terminal-header">
        <i class="fas fa-circle-info"></i>
        <span>Evaluation Criteria</span>
      </div>
      <div class="terminal-body">
        <ul class="space-y-2.5 text-xs text-slate-400 font-mono">
          <li class="flex items-start gap-2.5"><span class="text-blue-500">&bull;</span> Rank evaluation is driven by total successfully completed sandbox tasks.</li>
          <li class="flex items-start gap-2.5"><span class="text-blue-500">&bull;</span> Collision resolve factor: priority sorted by registration chronologies.</li>
          <li class="flex items-start gap-2.5"><span class="text-blue-500">&bull;</span> Standard and classified modules yield equal rank points.</li>
        </ul>
      </div>
    </div>
  </div>

  <?php include_once ROOT . '/shared/military-ui/footer.php'; ?>
</body>

</html>