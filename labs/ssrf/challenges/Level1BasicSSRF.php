<?php

namespace Labs\SSRF\Challenges;

use App\Core\BaseChallenge;

/**
 * Level 1: Basic SSRF Challenge
 * Students learn to exploit basic server-side request forgery vulnerabilities
 */
class Level1BasicSSRF extends BaseChallenge
{
    private $targetUrl;
    
    public function __construct()
    {
        parent::__construct();
        $this->targetUrl = $this->getInput('url');
    }
    
    /**
     * Render the challenge interface
     */
    public function render(): string
    {
        include ROOT . '/labs/ssrf/views/level1.php';
        return '';
    }
    
    /**
     * Handle form submissions and user input
     */
    public function handle(): void
    {
        if ($this->isPost()) {
            // Intentional vulnerability: No URL validation
            // Students should exploit this to access internal services
            if (!empty($this->targetUrl)) {
                // Vulnerable code - DO NOT FIX
                // This is the intentional SSRF vulnerability
                $response = file_get_contents($this->targetUrl);
                echo "<pre>" . htmlspecialchars($response) . "</pre>";
            }
        }
    }
    
    /**
     * Validate the solution (called when student submits flag)
     */
    public function validate(): bool
    {
        // Check if student successfully accessed internal endpoint
        return false; // Placeholder - implement actual validation
    }
}
