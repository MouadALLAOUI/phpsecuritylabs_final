<?php

namespace Labs\SSRF\Challenges;

use App\Core\BaseChallenge;
use App\Core\Session;

/**
 * Level 1: Basic SSRF Challenge
 * Students learn to exploit basic server-side request forgery vulnerabilities
 */
class Level1BasicSSRF extends BaseChallenge
{
    private $targetUrl;
    public $response = '';
    public $error = '';
    public $solved = false;
    
    public function __construct()
    {
        parent::__construct();
        $this->targetUrl = $this->getInput('url');
        if (Session::get('ssrf_lvl1_solved') === true) {
            $this->completed = true;
            $this->solved = true;
        }
    }
    
    /**
     * Render the challenge interface
     */
    public function render(): void
    {
        include_once ROOT . '/shared/header.php';
        include_once ROOT . '/shared/sidebar.php';
        
        echo '<div class="lg:ml-64 p-6 min-h-[80vh] theme-transition">';
        include ROOT . '/labs/ssrf/views/level1.php';
        echo '</div>';
        
        include_once ROOT . '/shared/footer.php';
    }
    
    /**
     * Handle form submissions and user input
     */
    public function handle(): void
    {
        if ($this->isPost() && !empty($this->targetUrl)) {
            // Intentional vulnerability: No URL validation
            // Students should exploit this to access internal services
            
            // Offline/safe simulation to prevent internet dependencies:
            if (stripos($this->targetUrl, 'localhost/secret') !== false || 
                stripos($this->targetUrl, '127.0.0.1/secret') !== false ||
                stripos($this->targetUrl, '127.0.0.1:80/secret') !== false ||
                stripos($this->targetUrl, 'localhost:80/secret') !== false) {
                
                $this->response = "HTTP/1.1 200 OK\r\nContent-Type: text/plain\r\n\r\nSUCCESS! Decryption key retrieved:\nFLAG{SSRF_LOCAL_INTRA_NET_ACCESSED}";
                Session::set('ssrf_lvl1_solved', true);
                $this->markCompleted('ssrf', 'lvl1');
                $this->completed = true;
                $this->solved = true;
            } else {
                if (stripos($this->targetUrl, 'http://') === 0 || stripos($this->targetUrl, 'https://') === 0) {
                    $parsed = parse_url($this->targetUrl);
                    $host = $parsed['host'] ?? '';
                    if ($host === 'localhost' || $host === '127.0.0.1') {
                        $this->response = "HTTP/1.1 404 Not Found\r\nContent-Type: text/html\r\n\r\nDid you forget the /secret path?";
                    } else {
                        $this->response = "HTTP/1.1 200 OK\r\nContent-Type: text/html\r\n\r\n<html><body><h1>Fetched " . htmlspecialchars($host) . " successfully!</h1></body></html>";
                    }
                } else {
                    $this->error = "Error: Invalid URL scheme. Only HTTP and HTTPS are supported.";
                }
            }
        }
    }
    
    /**
     * Validate the solution (called when student submits flag)
     */
    public function validate(): bool
    {
        return Session::get('ssrf_lvl1_solved') === true;
    }
}
