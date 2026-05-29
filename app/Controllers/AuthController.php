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
    if (!isset($_POST['csrf_token']) || !Session::validateCsrfToken($_POST['csrf_token'])) {
      $_SESSION['login_error'] = 'Invalid security token';
      header('Location: ?page=login');
      exit;
    }
    
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $userKey = 'user_' . preg_replace('/[^a-z0-9_-]/', '', strtolower($username));
    $ipKey = 'ip_' . preg_replace('/[^a-f0-9.:]/i', '', $clientIp);
    
    $throttleFile = ROOT . '/storage/logs/login_throttle.json';
    $currentTime = time();
    $window = 300; // 5 minutes
    $maxAttempts = 5;
    
    // Load and clean throttle data
    $throttleData = [];
    if (file_exists($throttleFile)) {
      $throttleData = json_decode(file_get_contents($throttleFile), true) ?? [];
    }
    
    // Clean expired entries
    foreach ($throttleData as $key => $timestamps) {
      $throttleData[$key] = array_filter($timestamps, function ($ts) use ($currentTime, $window) {
        return ($currentTime - $ts) < $window;
      });
    }
    
    // Check if throttled
    $ipAttempts = count($throttleData[$ipKey] ?? []);
    $userAttempts = count($throttleData[$userKey] ?? []);
    
    if ($ipAttempts >= $maxAttempts || $userAttempts >= $maxAttempts) {
      $_SESSION['login_error'] = 'Too many login attempts. Please try again in 5 minutes.';
      header('Location: ?page=login');
      exit;
    }

    if ($this->auth->login($username, $password)) {
      // Clear throttling on success
      unset($throttleData[$ipKey]);
      unset($throttleData[$userKey]);
      file_put_contents($throttleFile, json_encode($throttleData), LOCK_EX);
      
      header('Location: ?page=profile');
    } else {
      // Record failed attempt
      $throttleData[$ipKey][] = $currentTime;
      $throttleData[$userKey][] = $currentTime;
      file_put_contents($throttleFile, json_encode($throttleData), LOCK_EX);
      
      $_SESSION['login_error'] = 'Invalid credentials';
      header('Location: ?page=login');
    }
    exit;
  }

  public function handleLogout(): void
  {
    $this->auth->logout();
    // Clear agent codename on logout to prevent persistence across sessions
    if (isset($_SESSION['agent_codename'])) {
      unset($_SESSION['agent_codename']);
    }
    header('Location: ?page=home');
    exit;
  }

  public function showProfile(): void
  {
    if (!$this->auth->isAuthenticated()) {
      http_response_code(401);
      include_once ROOT . '/shared/header.php';
      echo '<div class="lg:ml-64 p-6 min-h-[85vh] theme-transition flex items-center justify-center">
              <div class="bg-slate-900 border border-slate-800 rounded-xl p-8 text-center max-w-sm w-full mx-4 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1.5 bg-red-600"></div>
                <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4 block"></i>
                <h3 class="text-sm font-mono font-bold text-red-400 uppercase tracking-widest">Error 401: Unauthorized</h3>
                <p class="text-xs text-slate-400 mt-2 leading-relaxed">Secure link authentication required. Please login as a valid operator.</p>
              </div>
            </div>';
      include_once ROOT . '/shared/footer.php';
      exit;
    }
    $user = $this->auth->getUser();
    $completed = $this->auth->getCompletedChallengesDetailed();
    include_once ROOT . '/app/Views/profile.php';
  }

  public function showLabs(): void
  {
    if (!$this->auth->isAuthenticated()) {
      http_response_code(401);
      include_once ROOT . '/shared/header.php';
      echo '<div class="lg:ml-64 p-6 min-h-[85vh] theme-transition flex items-center justify-center">
              <div class="bg-slate-900 border border-slate-800 rounded-xl p-8 text-center max-w-sm w-full mx-4 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1.5 bg-red-600"></div>
                <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4 block"></i>
                <h3 class="text-sm font-mono font-bold text-red-400 uppercase tracking-widest">Error 401: Unauthorized</h3>
                <p class="text-xs text-slate-400 mt-2 leading-relaxed">Secure link authentication required. Please login as a valid operator.</p>
              </div>
            </div>';
      include_once ROOT . '/shared/footer.php';
      exit;
    }
    $user = $this->auth->getUser();
    $completed = $this->auth->getCompletedChallenges();
    include_once ROOT . '/app/Views/labs.php';
  }


  public function showLeaderboard(): void
  {
    $leaderboard = Auth::getLeaderboard(10);
    include_once ROOT . '/app/Views/leaderboard.php';
  }

  public function showAdminDashboard(): void
  {
    if (!$this->auth->isAuthenticated()) {
      http_response_code(401);
      include_once ROOT . '/shared/header.php';
      echo '<div class="lg:ml-64 p-6 min-h-[85vh] theme-transition flex items-center justify-center">
              <div class="bg-slate-900 border border-slate-800 rounded-xl p-8 text-center max-w-sm w-full mx-4 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1.5 bg-red-600"></div>
                <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4 block"></i>
                <h3 class="text-sm font-mono font-bold text-red-400 uppercase tracking-widest">Error 401: Unauthorized</h3>
                <p class="text-xs text-slate-400 mt-2 leading-relaxed">Secure link authentication required. Please login as a valid operator.</p>
              </div>
            </div>';
      include_once ROOT . '/shared/footer.php';
      exit;
    }
    if (!$this->auth->isAdmin()) {
      http_response_code(403);
      include_once ROOT . '/shared/header.php';
      echo '<div class="lg:ml-64 p-6 min-h-[85vh] theme-transition flex items-center justify-center">
              <div class="bg-slate-900 border border-slate-800 rounded-xl p-8 text-center max-w-sm w-full mx-4 shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1.5 bg-red-600"></div>
                <i class="fas fa-hand-holding-hand text-4xl text-red-500 mb-4 block"></i>
                <h3 class="text-sm font-mono font-bold text-red-400 uppercase tracking-widest">Error 403: Forbidden</h3>
                <p class="text-xs text-slate-400 mt-2 leading-relaxed">Commander credentials are required to access this roster database.</p>
              </div>
            </div>';
      include_once ROOT . '/shared/footer.php';
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
    if (!isset($_POST['csrf_token']) || !Session::validateCsrfToken($_POST['csrf_token'])) {
      $_SESSION['admin_message'] = 'Invalid security token';
      header('Location: ?page=admin');
      exit;
    }

    $userId = (int)($_POST['user_id'] ?? 0);
    $labName = $_POST['lab_name'] ?? '';

    $validLabs = ['xss', 'sqli', 'file_upload', 'csrf', 'xxe', 'ssrf', 'idor', 'jwt', 'path_traversal', 'deserialization'];
    if (!in_array($labName, $validLabs, true)) {
      $_SESSION['admin_message'] = 'Invalid lab name';
      header('Location: ?page=admin');
      exit;
    }

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

    // Enforce POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      echo 'Method Not Allowed';
      exit;
    }

    // CSRF protection for lab reset action
    if (!isset($_POST['csrf_token']) || !Session::validateCsrfToken($_POST['csrf_token'])) {
      $_SESSION['reset_error'] = 'Invalid security token';
      header('Location: ?page=labs');
      exit;
    }

    $validLabs = ['xss', 'sqli', 'file_upload', 'csrf', 'xxe', 'ssrf', 'idor', 'jwt', 'path_traversal', 'deserialization'];
    if (!in_array($labName, $validLabs, true)) {
      $_SESSION['reset_error'] = 'Invalid lab name';
      header('Location: ?page=labs');
      exit;
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