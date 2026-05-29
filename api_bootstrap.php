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
      $file = $baseDir . str_replace('\\', '/', $relative) . '.php';
      if (file_exists($file)) {
        require $file;
        return;
      }
    }
  }
});

// Initialize session if not active
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
