<?php

namespace App\Core;

use App\Controllers\AuthController;

class Router
{
  private array $routes = [
    'home' => '/app/Views/home.php',
    'login'   => null,   // handled by controller
    'logout'  => null,
    'profile' => null,
    'labs'    => null,
    'xss_admin_reports' => '/labs/xss/admin_reports.php',
  ];

  public function __construct()
  {
    // Dynamically register available lab routes
    $this->registerLabRoutes();
  }

  public function dispatch(string $uri, string $method): void
  {
    $page = $_GET['page'] ?? 'home';
    $action = $_GET['action'] ?? null;
    $authController = new AuthController();

    // Handle authentication routes
    if ($page === 'login') {
      if ($action === 'do') {
        $authController->handleLogin();
      } else {
        $authController->showLogin();
      }
      return;
    }
    if ($page === 'logout') {
      $authController->handleLogout();
      return;
    }
    if ($page === 'profile') {
      $authController->showProfile();
      return;
    }
    if ($page === 'labs') {
      // Check if reset is requested
      if (isset($_GET['reset'])) {
        $labName = $_GET['reset'];
        $authController->resetLab($labName);
        return;
      }
      $authController->showLabs();
      return;
    }

    if ($page === 'leaderboard') {
      $authController->showLeaderboard();
      return;
    }
    if ($page === 'admin') {
      if ($action === 'reset') {
        $authController->handleAdminReset();
      } else {
        $authController->showAdminDashboard();
      }
      return;
    }
    if ($page === 'patch_xss' || $page === 'patch_sqli' || $page === 'patch_fileupload') {
      $this->renderPatchReport($page);
      return;
    }

    // Restrict to known routes only (prevents directory traversal)
    if (!array_key_exists($page, $this->routes)) {
      $this->render404();
      return;
    }

    $file = dirname(__DIR__, 2) . $this->routes[$page];

    if (!file_exists($file)) {
      $this->render404();
      return;
    }

    require $file;
  }

  /**
   * Render a styled 404 error page
   */
  private function render404(): void
  {
    http_response_code(404);
    include_once ROOT . '/shared/header.php';
    ?>
    <div class="max-w-2xl mx-auto py-12 px-4">
      <div class="bg-white rounded-lg shadow-md p-8 text-center">
        <div class="mb-6">
          <i class="fas fa-exclamation-triangle text-6xl text-red-500"></i>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-4">404 - Page Not Found</h1>
        <p class="text-gray-600 mb-6">The page you are looking for does not exist or has been moved.</p>
        <div class="flex justify-center gap-4">
          <a href="?page=home" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
            <i class="fas fa-home mr-2"></i> Go to Dashboard
          </a>
          <a href="?page=labs" class="px-6 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
            <i class="fas fa-flask mr-2"></i> Browse Labs
          </a>
        </div>
      </div>
    </div>
    <?php
    include_once ROOT . '/shared/footer.php';
  }

  private function registerLabRoutes(): void
  {
    $labsDir = dirname(__DIR__, 2) . '/labs';

    if (!is_dir($labsDir)) {
      return;
    }

    foreach (scandir($labsDir) as $lab) {
      if ($lab === '.' || $lab === '..' || !is_dir("$labsDir/$lab")) {
        continue;
      }

      $indexFile = "$labsDir/$lab/index.php";
      if (file_exists($indexFile)) {
        $this->routes[$lab] = "/labs/$lab/index.php";
      }
    }
  }

  private function renderPatchReport(string $page): void
  {
    $file = dirname(__DIR__, 2) . "/app/Views/{$page}.php";

    if (!file_exists($file)) {
      http_response_code(404);
      echo 'Patch report not found';
      return;
    }

    require $file;
  }
}