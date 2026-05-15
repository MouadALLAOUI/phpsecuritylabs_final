<?php

namespace App\Core;

use PDO;

class Auth
{
  private Database $db;
  private ?array $user = null;

  public function __construct()
  {
    $this->db = Database::getInstance('app');
  }

  public function login(string $username, string $password): bool
  {
    $sql = "SELECT id, username, email, password, role, is_admin FROM users WHERE username = :username";
    $stmt = $this->db->query($sql, ['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
      return false;
    }

    // Seeded passwords are MD5 (for training only – do not use in production)
    if (md5($password) === $user['password']) {
      // Regenerate session ID to prevent fixation
      session_regenerate_id(true);
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['username'] = $user['username'];
      $_SESSION['role'] = $user['role'];
      $_SESSION['is_admin'] = (bool)$user['is_admin'];
      $this->user = $user;
      return true;
    }

    return false;
  }

  public function logout(): void
  {
    Session::destroy();
    $this->user = null;
  }

  public function isAuthenticated(): bool
  {
    return isset($_SESSION['user_id']);
  }

  public function getUser(): ?array
  {
    if ($this->user !== null) {
      return $this->user;
    }

    if (!$this->isAuthenticated()) {
      return null;
    }

    $userId = $_SESSION['user_id'];
    $sql = "SELECT id, username, email, role, is_admin, created_at FROM users WHERE id = :id";
    $stmt = $this->db->query($sql, ['id' => $userId]);
    $this->user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $this->user;
  }

  public function getUserId(): ?int
  {
    return $_SESSION['user_id'] ?? null;
  }

  public function isAdmin(): bool
  {
    return $this->isAuthenticated() && ($_SESSION['is_admin'] ?? false);
  }

  /**
   * Get completed challenges for the current user with aggregated stats
   * @return array ['xss' => ['count' => 2, 'last_solved' => '2025-04-30'], ...]
   */
  public function getCompletedChallenges(): array
  {
    if (!$this->isAuthenticated()) return [];

    $userId = $_SESSION['user_id'];

    $sql = "SELECT lab_name, COUNT(*) as count, MAX(completed_at) as last_solved
            FROM lab_progress
            WHERE user_id = :user_id AND completed = 1
            GROUP BY lab_name";
    $stmt = $this->db->query($sql, ['user_id' => $userId]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $progress = [];
    foreach ($results as $row) {
      $progress[$row['lab_name']] = [
        'count' => (int)$row['count'],
        'last_solved' => $row['last_solved']
      ];
    }

    return $progress;
  }

  /**
   * Get detailed list of completed challenges for profile page
   * @return array [['lab_name' => 'xss', 'challenge' => 'lvl1', 'completed_at' => '...'], ...]
   */
  public function getCompletedChallengesDetailed(): array
  {
    if (!$this->isAuthenticated()) return [];

    $userId = $_SESSION['user_id'];

    $sql = "SELECT lab_name, challenge, completed_at
            FROM lab_progress
            WHERE user_id = :user_id AND completed = 1
            ORDER BY completed_at DESC";
    $stmt = $this->db->query($sql, ['user_id' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Get leaderboard data
   * @param int $limit
   * @return array
   */
  public static function getLeaderboard(int $limit = 10): array
  {
    $db = Database::getInstance('app');
    $pdo = $db->getConnection();

    $sql = "SELECT u.id, u.username, u.role, COUNT(lp.id) as completed_count
            FROM users u
            LEFT JOIN lab_progress lp ON u.id = lp.user_id AND lp.completed = 1
            GROUP BY u.id
            ORDER BY completed_count DESC, u.created_at ASC
            LIMIT :limit";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Get all users with their progress (Admin only)
   */
  public static function getAllUsersProgress(): array
  {
    $db = Database::getInstance('app');
    $pdo = $db->getConnection();

    $sql = "SELECT u.id, u.username, u.role, u.created_at,
                   SUM(CASE WHEN lp.lab_name = 'xss' AND lp.completed = 1 THEN 1 ELSE 0 END) as xss_count,
                   SUM(CASE WHEN lp.lab_name = 'sqli' AND lp.completed = 1 THEN 1 ELSE 0 END) as sqli_count,
                   SUM(CASE WHEN lp.lab_name = 'file_upload' AND lp.completed = 1 THEN 1 ELSE 0 END) as upload_count,
                   COUNT(CASE WHEN lp.completed = 1 THEN 1 END) as total_count
            FROM users u
            LEFT JOIN lab_progress lp ON u.id = lp.user_id
            GROUP BY u.id
            ORDER BY total_count DESC";

    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Reset a specific lab for a specific user (Admin only)
   */
  public static function resetUserLab(int $userId, string $labName): bool
  {
    if (!(new self())->isAdmin()) return false;

    $db = Database::getInstance('app');
    $pdo = $db->getConnection();

    $sql = "DELETE FROM lab_progress WHERE user_id = :user_id AND lab_name = :lab_name";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
      'user_id' => $userId,
      'lab_name' => $labName
    ]);
  }

  /**
   * Mark a challenge as completed (called from BaseChallenge)
   */
  public function completeChallenge(int $userId, string $lab, string $challenge): void
  {
    $sql = "INSERT INTO lab_progress (user_id, lab_name, challenge, completed, completed_at)
                VALUES (:user_id, :lab, :challenge, 1, NOW())
                ON DUPLICATE KEY UPDATE completed = 1, completed_at = NOW()";
    $this->db->query($sql, [
      'user_id' => $userId,
      'lab' => $lab,
      'challenge' => $challenge
    ]);
  }
}