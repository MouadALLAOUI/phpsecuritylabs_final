<?php

namespace App\Core;

interface ChallengeInterface
{
  public function render(): void;
  public function handle(): void;
  public function validate(): bool;
}
