<?php

namespace App\Core;

class LabEngine
{
  private array $config;
  private string $storageKey = 'lab_vulnerabilities';
  
  public function __construct(array $config = [])
  {
    // Initialize session if not already started
    if (session_status() === PHP_SESSION_NONE) {
      Session::start();
    }
    
    // Try to load from session first, then use provided config
    $savedConfig = $_SESSION[$this->storageKey] ?? null;
    if ($savedConfig !== null && is_array($savedConfig)) {
      $this->config = $savedConfig;
    } else {
      $this->config = $config;
    }
  }
  
  public function isVulnerable(string $lab, string $challenge): bool
  {
    return $this->config[$lab][$challenge] ?? true;
  }
  
  public function setVulnerable(string $lab, string $challenge, bool $vulnerable): void
  {
    $this->config[$lab][$challenge] = $vulnerable;
    $this->persistToSession();
  }
  
  public function toggleVulnerability(string $lab, string $challenge): bool
  {
    $currentState = $this->isVulnerable($lab, $challenge);
    $newState = !$currentState;
    $this->setVulnerable($lab, $challenge, $newState);
    return $newState;
  }
  
  public function getConfig(): array
  {
    return $this->config;
  }
  
  public function resetToDefault(array $defaultConfig): void
  {
    $this->config = $defaultConfig;
    $this->persistToSession();
  }
  
  /**
   * Persist vulnerability configuration to session storage
   */
  private function persistToSession(): void
  {
    $_SESSION[$this->storageKey] = $this->config;
  }
  
  /**
   * Save vulnerabilities to database for permanent persistence
   * Requires user to be logged in
   */
  public function saveToDatabase(): bool
  {
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
      return false;
    }
    
    try {
      $db = Database::getInstance('app');
      $pdo = $db->getConnection();
      
      // Check if record exists
      $stmt = $pdo->prepare("SELECT id FROM lab_settings WHERE user_id = ?");
      $stmt->execute([$userId]);
      $exists = $stmt->fetch();
      
      $jsonConfig = json_encode($this->config);
      
      if ($exists) {
        // Update existing record
        $stmt = $pdo->prepare(
          "UPDATE lab_settings SET vulnerabilities = ?, updated_at = NOW() WHERE user_id = ?"
        );
        $stmt->execute([$jsonConfig, $userId]);
      } else {
        // Insert new record
        $stmt = $pdo->prepare(
          "INSERT INTO lab_settings (user_id, vulnerabilities, created_at, updated_at) VALUES (?, ?, NOW(), NOW())"
        );
        $stmt->execute([$userId, $jsonConfig]);
      }
      
      return true;
    } catch (\Exception $e) {
      error_log("LabEngine: Failed to save to database: " . $e->getMessage());
      return false;
    }
  }
  
  /**
   * Load vulnerabilities from database
   */
  public function loadFromDatabase(): bool
  {
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
      return false;
    }
    
    try {
      $db = Database::getInstance('app');
      $pdo = $db->getConnection();
      
      $stmt = $pdo->prepare("SELECT vulnerabilities FROM lab_settings WHERE user_id = ?");
      $stmt->execute([$userId]);
      $result = $stmt->fetch();
      
      if ($result && $result['vulnerabilities']) {
        $this->config = json_decode($result['vulnerabilities'], true) ?? $this->config;
        $this->persistToSession();
        return true;
      }
      
      return false;
    } catch (\Exception $e) {
      error_log("LabEngine: Failed to load from database: " . $e->getMessage());
      return false;
    }
  }
}
