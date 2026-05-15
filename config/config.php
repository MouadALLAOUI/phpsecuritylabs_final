<?php
// -- config.php --
// Centralized configuration for database credentials

if (!function_exists('loadEnv')) {
  function loadEnv($path = __DIR__ . '/../.env')
  {
    if (!file_exists($path)) {
      throw new Exception('.env file not found at ' . $path);
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
      if (strpos(trim($line), '#') === 0) continue;
      $parts = explode('=', $line, 2);
      if (count($parts) === 2) {
        $key = trim($parts[0]);
        $value = trim($parts[1]);
        putenv("$key=$value");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
      }
    }
  }
}

try {
  loadEnv();
  $host = $_ENV['DB_HOST'] ?? 'localhost';
  $db   = $_ENV['DB_NAME'] ?? 'php_security_labs_app';
  $db_labs   = $_ENV['DB_LABS_NAME'] ?? 'php_security_labs_challenges';
  $user = $_ENV['DB_USER'] ?? 'root';
  $pass = $_ENV['DB_PASS'] ?? '';
  return [
    'host'     => $host,
    'port'     => '3306',
    'dbname'   => $db,
    'dbname_labs'   => $db_labs,
    'user'     => $user,
    'password' => $pass,
    'charset'  => 'utf8mb4',
  ];
} catch (\Throwable $th) {
  // Fallback to default values if .env is missing or unreadable
  // In production, you should ensure .env exists and is properly configured
  error_log('Config warning: Using default database configuration. ' . $th->getMessage());
  return [
    'host'     => 'localhost',
    'port'     => '3306',
    'dbname'   => 'php_security_labs_app',
    'dbname_labs'   => 'php_security_labs_challenges',
    'user'     => 'root',
    'password' => '',
    'charset'  => 'utf8mb4',
  ];
}