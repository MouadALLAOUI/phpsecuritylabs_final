<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Session;

// use App\Core\Session;

class AuthController
{
  private Auth $auth;
  private Database $db;

  public function __construct()
  {
    $this->auth = new Auth();
    $this->db = Database::getInstance();
  }

  public function showLogin(): void
  {
    if ($this->auth->isAuthenticated()) {
      header('Location: ?page=profile');
      exit;
    }
    include_once ROOT . '/app/Views/login.php';
  }

  public function handleLogin(): void
  {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($this->auth->login($username, $password)) {
      header('Location: ?page=profile');
    } else {
      $_SESSION['login_error'] = 'Invalid credentials';
      header('Location: ?page=login');
    }
    exit;
  }

  public function handleLogout(): void
  {
    $this->auth->logout();
    header('Location: ?page=home');
    exit;
  }

  public function showProfile(): void
  {
    if (!$this->auth->isAuthenticated()) {
      header('Location: ?page=login');
      exit;
    }
    $user = $this->auth->getUser();
    $completed = $this->auth->getCompletedChallenges($user['id']);
    include_once ROOT . '/app/Views/profile.php';
  }

  public function showLabs(): void
  {
    if (!$this->auth->isAuthenticated()) {
      header('Location: ?page=login');
      exit;
    }
    $user = $this->auth->getUser();
    $completed = $this->auth->getCompletedChallenges($user['id']);
    // Group completed challenges by lab for quick stats
    $completedMap = [];
    foreach ($completed as $c) {
      $completedMap[$c['lab_name']][$c['challenge']] = true;
    }
    include_once ROOT . '/app/Views/labs.php';
  }

  /**
   * Reset all progress for a given lab for the current user
   */
  public function resetLab(string $labName): void
  {
    if (!$this->auth->isAuthenticated()) {
      header('Location: ?page=login');
      exit;
    }

    $userId = $this->auth->getUserId();
    $sql = "DELETE FROM lab_progress WHERE user_id = :user_id AND lab_name = :lab_name";
    $this->db->query($sql, ['user_id' => $userId, 'lab_name' => $labName]);

    // Also clear any session flags used for challenges
    Session::delete('xss_lvl1_solved');
    Session::delete('xss_lvl2_solved');
    Session::delete('xss_lvl3_solved');
    Session::delete('sqli_solved');
    Session::delete('file_upload_solved');
    Session::delete('uploaded_files');

    $_SESSION['reset_message'] = "Progress for $labName has been reset.";
    header('Location: ?page=labs');
    exit;
  }
}