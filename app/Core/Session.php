<?php

namespace App\Core;

class Session
{
  public static function start(): void
  {
    if (session_status() === PHP_SESSION_NONE) {
      $secure = false;
      $appEnv = getenv('APP_ENV') ?: ($_ENV['APP_ENV'] ?? 'production');
      if (trim(strtolower($appEnv)) === 'production') {
        $secure = true;
      }
      session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax'
      ]);
      session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
  }

  public static function getCsrfToken(): string
  {
    self::start();
    return $_SESSION['csrf_token'];
  }

  public static function validateCsrfToken(?string $token): bool
  {
    if (empty($token)) {
      return false;
    }
    self::start();
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
  }

  public static function set(string $key, $value): void
  {
    $_SESSION[$key] = $value;
  }

  public static function get(string $key, $default = null)
  {
    return $_SESSION[$key] ?? $default;
  }

  public static function has(string $key): bool
  {
    return isset($_SESSION[$key]);
  }

  public static function delete(string $key): void
  {
    unset($_SESSION[$key]);
  }

  public static function destroy(): void
  {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(
        session_name(),
        '',
        time() - 42000, // Expire in the past 
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
      );
    }
    session_destroy();
  }
}