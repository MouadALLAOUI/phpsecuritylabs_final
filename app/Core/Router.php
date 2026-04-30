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

    // Restrict to known routes only (prevents directory traversal)
    if (!array_key_exists($page, $this->routes)) {
      http_response_code(404);
      echo 'Page not found';
      return;
    }

    $file = dirname(__DIR__, 2) . $this->routes[$page];

    if (!file_exists($file)) {
      http_response_code(404);
      echo 'Page not found';
      return;
    }

    require $file;
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
}