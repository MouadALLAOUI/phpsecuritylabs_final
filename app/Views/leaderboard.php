<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Leaderboard - MIL-OPS</title>
  <?php include_once ROOT . '/shared/military-ui/header.php'; ?>
  <style>
  .leaderboard-container {
    max-width: 800px;
    margin: 2rem auto;
  }

  .rank-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #1a3d2f;
    transition: all 0.3s ease;
  }

  .rank-item:hover {
    background: rgba(0, 255, 127, 0.05);
  }

  .rank-number {
    font-size: 2rem;
    font-weight: bold;
    color: #00ff7f;
    width: 60px;
    text-align: center;
  }

  .rank-icon {
    font-size: 1.5rem;
    margin-right: 1rem;
  }

  .rank-info {
    flex: 1;
  }

  .rank-codename {
    font-size: 1.2rem;
    color: #00ff7f;
    margin-bottom: 0.25rem;
  }

  .rank-username {
    color: #6b7280;
    font-size: 0.9rem;
  }

  .rank-score {
    font-size: 1.5rem;
    font-weight: bold;
    color: #fbbf24;
    padding: 0.5rem 1rem;
    background: rgba(251, 191, 36, 0.1);
    border-radius: 4px;
  }

  .trophy {
    font-size: 3rem;
    text-align: center;
    margin: 2rem 0;
  }
  </style>
</head>

<body class="mil-ops-bg">
  <?php include_once ROOT . '/shared/sidebar.php'; ?>

  <main class="mil-ops-main">
    <div class="mission-header">
      <h1><span class="icon">🏆</span> OPERATOR RANKINGS</h1>
      <p class="objective">Top performers in cybersecurity training operations</p>
    </div>

    <div class="leaderboard-container">
      <div class="trophy">🥇 🥈 🥉</div>

      <?php if (empty($leaderboard)): ?>
      <div class="intel-brief">
        <p>No operators have completed challenges yet.</p>
        <p>Be the first to climb the ranks!</p>
      </div>
      <?php else: ?>
      <?php foreach ($leaderboard as $index => $operator): ?>
      <div class="rank-item">
        <div class="rank-number"><?= $index + 1 ?></div>
        <div class="rank-icon">
          <?php if ($index === 0): ?>🥇
          <?php elseif ($index === 1): ?>🥈
          <?php elseif ($index === 2): ?>🥉
          <?php else: ?>🎖️<?php endif; ?>
        </div>
        <div class="rank-info">
          <div class="rank-codename"><?= htmlspecialchars($operator['codename'] ?? 'Unknown') ?></div>
          <div class="rank-username">@<?= htmlspecialchars($operator['username']) ?></div>
        </div>
        <div class="rank-score"><?= $operator['completed_count'] ?> COMPLETED</div>
      </div>
      <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div class="intel-brief">
      <h3>RANKING CRITERIA</h3>
      <ul>
        <li>Operators ranked by total number of completed challenges</li>
        <li>Tie-breaker: earliest registration date</li>
        <li>All lab types contribute equally to ranking</li>
        <li>Complete more challenges to climb the leaderboard</li>
      </ul>
    </div>
  </main>

  <?php include_once ROOT . '/shared/military-ui/footer.php'; ?>
</body>

</html>