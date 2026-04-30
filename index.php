<?php

/**
 * PHP Security Labs – Root Front Controller
 */

define('ROOT', __DIR__);

// PSR‑4‑like autoloader
spl_autoload_register(function (string $class) {
  $prefixMap = [
    'App\\'  => ROOT . '/app/',
    'Labs\\' => ROOT . '/labs/',
  ];

  foreach ($prefixMap as $prefix => $baseDir) {
    $len = strlen($prefix);
    if (strncmp($class, $prefix, $len) === 0) {
      $relative = substr($class, $len);
      $file = $baseDir . str_replace('\\', '/', $relative) . '.php';
      if (file_exists($file)) {
        require $file;
        return;
      }
    }
  }
});
session_start(); // <-- ADD THIS LINE
use App\Core\App;
use App\Core\ChallengeLoader;

// -------------------------------------------------------------------
// 1. Lab challenge with explicit level (?page=xss&lvl=1)
// -------------------------------------------------------------------
$page = $_GET['page'] ?? null;
$lvl  = $_GET['lvl']  ?? null;

if ($page && $lvl !== null && $page !== 'home') {
  $loader = new ChallengeLoader();
  $loader->execute($page, 'lvl' . $lvl);
  exit;
}

// -------------------------------------------------------------------
// 2. Standard routing (home page, lab index, etc.)
// -------------------------------------------------------------------
$app = new App();
$app->run();
