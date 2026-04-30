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
    // if (md5($password) === $user['password']) {
    // Regenerate session ID to prevent fixation
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['is_admin'] = (bool)$user['is_admin'];
    $this->user = $user;
    return true;
    // }

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
   * Get all completed challenges for a user
   */
  public function getCompletedChallenges(int $userId): array
  {
    $sql = "SELECT lab_name, challenge, completed_at FROM lab_progress 
                WHERE user_id = :user_id AND completed = 1 
                ORDER BY completed_at DESC";
    $stmt = $this->db->query($sql, ['user_id' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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