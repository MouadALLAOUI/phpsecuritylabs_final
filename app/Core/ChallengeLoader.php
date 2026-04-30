<?php

namespace App\Core;

class ChallengeLoader
{
  private string $labsBasePath;

  public function __construct()
  {
    $this->labsBasePath = dirname(__DIR__, 2) . '/labs';
  }

  public function load(string $lab, string $challenge): ?ChallengeInterface
  {
    $mapFile = $this->labsBasePath . '/' . $lab . '/challenge_map.php';

    if (!file_exists($mapFile)) {
      return null;
    }

    $map = require $mapFile;

    if (!is_array($map) || !isset($map[$challenge])) {
      return null;
    }

    $class = $map[$challenge];

    // Ensure class exists (map file should handle any required includes/autoload)
    if (!class_exists($class)) {
      return null;
    }

    $instance = new $class();

    if (!$instance instanceof ChallengeInterface) {
      return null;
    }

    return $instance;
  }

  public function execute(string $lab, string $challenge): void
  {
    $challengeInstance = $this->load($lab, $challenge);

    if ($challengeInstance === null) {
      http_response_code(404);
      echo 'Challenge not found';
      return;
    }

    $challengeInstance->handle();
    $challengeInstance->render();
    // ✅ CRITICAL FIX: call validate() to persist completion to database
    $challengeInstance->validate();
  }
}