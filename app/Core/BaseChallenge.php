<?php

namespace App\Core;

abstract class BaseChallenge implements ChallengeInterface
{
  protected array $input = [];
  protected bool $completed = false;

  public function __construct()
  {
    // Use explicit $_POST or $_GET instead of $_REQUEST to avoid confusion
    // and potential security issues from COOKIE data mixing
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->input = $this->sanitizeInput($_POST);
    } else {
      $this->input = $this->sanitizeInput($_GET);
    }
  }

  protected function getInput(string $key, mixed $default = null): mixed
  {
    return $this->input[$key] ?? $default;
  }

  protected function isPost(): bool
  {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
  }

  private function sanitizeInput(array $data): array
  {
    $sanitized = [];
    foreach ($data as $key => $value) {
      if (is_string($value)) {
        $sanitized[$key] = trim($value);
      } else {
        $sanitized[$key] = $value;
      }
    }
    return $sanitized;
  }

  /**
   * Mark this challenge as completed in the database for the logged-in user
   */
  protected function markCompleted(string $lab, string $challenge): void
  {
    // Only mark once per request and only if user is logged in
    if ($this->completed) {
      return;
    }

    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
      // Not logged in – cannot save progress
      error_log("markCompleted: no user_id in session");
      return;
    }

    try {
      $auth = new Auth(); // Auth uses Database singleton
      $auth->completeChallenge($userId, $lab, $challenge);
      $this->completed = true;
      error_log("Challenge completed: $lab / $challenge for user $userId");
    } catch (\Exception $e) {
      error_log("markCompleted failed: " . $e->getMessage());
    }
  }

  abstract public function render(): void;
  abstract public function handle(): void;
  abstract public function validate(): bool;
}