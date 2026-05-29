<?php

/**
 * PHP Security Labs – Root Front Controller
 */

define('ROOT', __DIR__);

// PSR-4-like autoloader
spl_autoload_register(function (string $class) {
  $prefixMap = [
    'App\\'  => ROOT . '/app/',
    'Labs\\' => ROOT . '/labs/',
  ];

  foreach ($prefixMap as $prefix => $baseDir) {
    $len = strlen($prefix);
    if (strncmp($class, $prefix, $len) === 0) {
      $relative = substr($class, $len);

      // For Labs namespace: map the first segment (e.g. "FileUpload", "XSS", "SQLi")
      // to the actual on-disk directory name (e.g. "file_upload", "xss", "sqli").
      // Strategy: normalise both sides by lowercasing and stripping underscores,
      // then look up the real directory name from a static cache.
      if ($prefix === 'Labs\\') {
        static $labDirMap = null;
        if ($labDirMap === null) {
          $labDirMap = [];
          foreach (scandir($baseDir) as $entry) {
            if ($entry !== '.' && $entry !== '..' && is_dir($baseDir . $entry)) {
              // key = lowercase, underscores removed
              $key = strtolower(str_replace('_', '', $entry));
              $labDirMap[$key] = $entry;
            }
          }
        }
        $parts = explode('\\', $relative);
        if (isset($parts[0])) {
          $key = strtolower(str_replace('_', '', $parts[0]));
          if (isset($labDirMap[$key])) {
            $parts[0] = $labDirMap[$key];
          }
        }
        $relative = implode('\\', $parts);
      }

      $file = $baseDir . str_replace('\\', '/', $relative) . '.php';
      if (file_exists($file)) {
        require $file;
        return;
      }
    }
  }
});


// Load environment configuration early
require_once __DIR__ . '/config/config.php';

// Start secure centralized session
\App\Core\Session::start();

use App\Core\App;
use App\Core\ChallengeLoader;

// -------------------------------------------------------------------
// 1. Lab challenge with explicit level (?page=xss&lvl=1, ?page=sqli&lvl=1, etc.)
// -------------------------------------------------------------------
$page = $_GET['page'] ?? null;
$lvl  = $_GET['lvl']  ?? null;

// If a user accesses a lab's main page without a level, redirect to level 1 of that lab
if ($page && $lvl === null && in_array($page, ['xss', 'sqli', 'file_upload', 'csrf', 'xxe', 'ssrf', 'idor', 'path_traversal', 'deserialization', 'jwt'])) {
  header("Location: ?page={$page}&lvl=1");
  exit;
}

if ($page && $lvl !== null && in_array($page, ['xss', 'sqli', 'file_upload', 'csrf', 'xxe', 'ssrf', 'idor', 'path_traversal', 'deserialization', 'jwt'])) {
  $enableVulns = getenv('LABS_ENABLE_INTENTIONAL_VULNS') ?: ($_ENV['LABS_ENABLE_INTENTIONAL_VULNS'] ?? 'false');
  if (trim(strtolower($enableVulns)) !== 'true') {
    http_response_code(403);
    die('Error: Vulnerable labs are disabled in this environment (LABS_ENABLE_INTENTIONAL_VULNS is false).');
  }
  $loader = new ChallengeLoader();
  $loader->execute($page, 'lvl' . $lvl);
  exit;
}

// -------------------------------------------------------------------
// 2. Standard routing (home page, lab index, etc.)
// -------------------------------------------------------------------
$app = new App();
$app->run();
