<?php

namespace App\Core;

use PDO;
// use PDOException;

class Database
{
  // private static ?Database $instance = null;
  private static ?array $instances = [];
  private PDO $pdo;

  private function __construct(array $config)
  {
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

  /**
   * @param string $dbType 'app' or 'labs'
   */
  public static function getInstance(string $dbType = 'app'): self
  {
    if (!isset(self::$instances[$dbType])) {
      $config = self::loadConfig($dbType);
      self::$instances[$dbType] = new self($config);
    }
    return self::$instances[$dbType];
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

  private static function loadConfig(string $dbType): array
  {
    $configPath = dirname(__DIR__, 2) . '/config/config.php';

    if (!file_exists($configPath)) {
      throw new \RuntimeException('Configuration file not found at ' . $configPath);
    }

    $config = require $configPath;
    if (!is_array($config)) {
      throw new \RuntimeException('Config file must return an array');
    }

    if ($dbType === 'labs') {
      if (!isset($config['dbname_labs'])) {
        throw new \RuntimeException('dbname_labs not defined in config');
      }
      $config['dbname'] = $config['dbname_labs'];
    } else {
      if (!isset($config['dbname'])) {
        throw new \RuntimeException('dbname not defined in config');
      }
    }

    return $config;
  }

  private function __clone() {}
  public function __wakeup()
  {
    throw new \Exception("Cannot unserialize singleton");
  }
}