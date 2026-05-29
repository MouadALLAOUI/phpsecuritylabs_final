<?php
/**
 * Separate Bootstrap for API endpoints - no web router
 */

define('ROOT', __DIR__);

// PSR-4-like autoloader for dynamic App and Labs namespaces
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
      // Strategy: normalise both sides by lowercasing and stripping underscores.
      if ($prefix === 'Labs\\') {
        static $labDirMap = null;
        if ($labDirMap === null) {
          $labDirMap = [];
          foreach (scandir($baseDir) as $entry) {
            if ($entry !== '.' && $entry !== '..' && is_dir($baseDir . $entry)) {
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
