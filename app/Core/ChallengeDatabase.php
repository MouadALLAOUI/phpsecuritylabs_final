<?php

namespace App\Core;

use PDO;

class ChallengeDatabase
{
  private static ?ChallengeDatabase $instance = null;
  private PDO $pdo;

  private function __construct(array $config)
  {
    // Override the database name to the challenges DB
    $config['dbname'] = 'php_security_labs_challenges';

    $dsn = sprintf(
      'mysql:host=%s;port=%s;dbname=%s;charset=%s',
      $config['host'],
      $config['port'] ?? '3306',
      $config['dbname'],
      $config['charset'] ?? 'utf8mb4'
    );

    $options = [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $this->pdo = new PDO($dsn, $config['user'], $config['password'], $options);
  }

  public static function getInstance(): self
  {
    if (self::$instance === null) {
      $config = self::loadConfig();
      self::$instance = new self($config);
    }
    return self::$instance;
  }

  public function getConnection(): PDO
  {
    return $this->pdo;
  }

  public function query(string $sql, array $params = []): \PDOStatement
  {
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
  }

  private static function loadConfig(): array
  {
    // Reuse the same configuration loading as Database
    $host = getenv('DB_HOST');
    if ($host !== false) {
      return [
        'host'     => $host,
        'port'     => getenv('DB_PORT') ?: '3306',
        'dbname'   => getenv('DB_LABS_NAME'), // will be overridden
        'user'     => getenv('DB_USER'),
        'password' => getenv('DB_PASS'),
        'charset'  => getenv('DB_CHARSET') ?: 'utf8mb4',
      ];
    }

    $configPath = dirname(__DIR__, 2) . '/config/config.php';
    if (file_exists($configPath)) {
      $config = require $configPath;
      if (is_array($config)) {
        return $config;
      }
    }

    throw new \RuntimeException('Database configuration not found.');
  }

  private function __clone() {}
  public function __wakeup()
  {
    throw new \Exception("Cannot unserialize singleton");
  }
}