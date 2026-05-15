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
    
    // Generate CSRF token for login form
    if (empty($_SESSION['csrf_token'])) {
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    include_once ROOT . '/app/Views/login.php';
  }

  public function handleLogin(): void
  {
    // CSRF protection for core authentication
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
      $_SESSION['login_error'] = 'Invalid security token';
      header('Location: ?page=login');
      exit;
    }
    
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
    $completed = $this->auth->getCompletedChallengesDetailed();
    include_once ROOT . '/app/Views/profile.php';
  }

  public function showLabs(): void
  {
    if (!$this->auth->isAuthenticated()) {
      header('Location: ?page=login');
      exit;
    }
    $user = $this->auth->getUser();
    $completed = $this->auth->getCompletedChallenges();
    // Group completed challenges by lab for quick stats
    // $completedMap = [];
    // foreach ($completed as $c) {
    //   $completedMap[$c['lab_name']][$c['challenge']] = true;
    // }
    include_once ROOT . '/app/Views/labs.php';
  }


  public function showLeaderboard(): void
  {
    $leaderboard = Auth::getLeaderboard(10);
    include_once ROOT . '/app/Views/leaderboard.php';
  }

  public function showAdminDashboard(): void
  {
    if (!$this->auth->isAdmin()) {
      header('Location: ?page=home');
      exit;
    }
    $users = Auth::getAllUsersProgress();
    include_once ROOT . '/app/Views/admin/dashboard.php';
  }

  public function handleAdminReset(): void
  {
    if (!$this->auth->isAdmin()) {
      http_response_code(403);
      echo 'Unauthorized';
      exit;
    }

    // CSRF protection for admin actions
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
      $_SESSION['admin_message'] = 'Invalid security token';
      header('Location: ?page=admin');
      exit;
    }

    $userId = (int)($_POST['user_id'] ?? 0);
    $labName = $_POST['lab_name'] ?? '';

    if ($userId && $labName) {
      Auth::resetUserLab($userId, $labName);
      $_SESSION['admin_message'] = "Lab progress reset for user ID $userId.";
    }

    header('Location: ?page=admin');
    exit;
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

    // CSRF protection for lab reset action
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
      // Allow GET requests for reset (from labs page links), but require POST token for form submissions
      // For backward compatibility with existing GET-based reset links, we skip CSRF check for GET
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $_SESSION['reset_error'] = 'Invalid security token';
        header('Location: ?page=labs');
        exit;
      }
    }

    $userId = $this->auth->getUserId();
    $sql = "DELETE FROM lab_progress WHERE user_id = :user_id AND lab_name = :lab_name";
    $this->db->query($sql, ['user_id' => $userId, 'lab_name' => $labName]);

    // Also clear any session flags used for challenges
    Session::delete('xss_lvl1_solved');
    Session::delete('xss_lvl2_solved');
    Session::delete('xss_lvl3_solved');
    Session::delete('sqli_lvl1_solved');
    Session::delete('sqli_lvl2_solved');
    Session::delete('file_upload_lvl1_solved');
    Session::delete('file_upload_lvl2_solved');
    Session::delete('uploaded_files');

    $_SESSION['reset_message'] = "Progress for $labName has been reset.";
    header('Location: ?page=labs');
    exit;
  }
}