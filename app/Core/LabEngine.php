<?php

namespace App\Core;

class LabEngine
{
  private array $config;

  public function __construct(array $config = [])
  {
    $this->config = $config;
  }

  public function isVulnerable(string $lab, string $challenge): bool
  {
    return $this->config[$lab][$challenge] ?? true;
  }

  public function setVulnerable(string $lab, string $challenge, bool $vulnerable): void
  {
    $this->config[$lab][$challenge] = $vulnerable;
  }

  public function getConfig(): array
  {
    return $this->config;
  }
}
