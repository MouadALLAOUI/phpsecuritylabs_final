<?php
// -- config.php --
// Centralized configuration for database credentials

if (!function_exists('e')) {
  function e(?string $value): string
  {
    if ($value === null) {
      return '';
    }
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
  }
}

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

  // 1. Fail closed if APP_DEBUG is missing
  if (!isset($_ENV['APP_DEBUG'])) {
    throw new Exception("APP_DEBUG is missing in .env configurations.");
  }

  // 2. Fail closed if explicit DB host, name, user, or pass are missing
  if (!isset($_ENV['DB_HOST']) || !isset($_ENV['DB_NAME']) || !isset($_ENV['DB_LABS_NAME']) || !isset($_ENV['DB_USER']) || !isset($_ENV['DB_PASS'])) {
    throw new Exception("Explicit database configuration variables (DB_HOST, DB_NAME, DB_LABS_NAME, DB_USER, DB_PASS) are required in .env.");
  }

  $host = $_ENV['DB_HOST'];
  $db   = $_ENV['DB_NAME'];
  $db_labs = $_ENV['DB_LABS_NAME'];
  $user = $_ENV['DB_USER'];
  $pass = $_ENV['DB_PASS'];

  return [
    'host'     => $host,
    'port'     => $_ENV['DB_PORT'] ?? '3306',
    'dbname'   => $db,
    'dbname_labs'   => $db_labs,
    'user'     => $user,
    'password' => $pass,
    'charset'  => 'utf8mb4',
  ];
} catch (\Throwable $th) {
  // Fail closed by throwing an Exception and logging error
  error_log('Config fatal error: ' . $th->getMessage());
  throw new Exception('Database configuration baseline failure: ' . $th->getMessage());
}