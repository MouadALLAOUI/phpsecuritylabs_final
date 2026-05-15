<?php

namespace Labs\IDOR\Challenges;

use App\Core\BaseChallenge;

/**
 * Level 1: Basic IDOR Challenge
 */
class Level1BasicIDOR extends BaseChallenge
{
    public function render(): string
    {
        include ROOT . '/labs/idor/views/level1.php';
        return '';
    }
    
    public function handle(): void
    {
        if ($this->isPost()) {
            // Intentional vulnerability - no authorization check
            // Students should exploit this to access other users' data
        }
    }
    
    public function validate(): bool
    {
        return false; // Placeholder
    }
}
