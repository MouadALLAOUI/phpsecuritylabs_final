<?php
/**
 * XSS Level 3 - DOM XSS Challenge
 * 
 * Vulnerability: Client-side DOM manipulation using innerHTML with unsanitized hash input
 * Attack Vector: URL Hash (#) -> document.write/innerHTML -> Script Execution
 * 
 * This challenge demonstrates DOM-based XSS where the payload never touches the server.
 * The vulnerability exists entirely in the client-side JavaScript.
 */

namespace Labs\XSS\Challenges;

use App\Core\ChallengeInterface;
use App\Core\BaseChallenge;
use App\Core\Session;

class Level3Dom extends BaseChallenge implements ChallengeInterface
{
    private $completed = false;

    public function __construct()
    {
        // Check if already solved via session
        if (Session::get('xss_lvl3_solved') === true) {
            $this->completed = true;
        }
    }

    public function render(): string
    {
        // Include the military-themed view
        ob_start();
        include __DIR__ . '/views/dom_terminal.php';
        return ob_get_clean();
    }

    public function handle(array $data): array
    {
        $response = ['success' => false, 'message' => ''];

        // Handle verification action (called by the payload via AJAX or direct access)
        if (isset($data['action']) && $data['action'] === 'verify') {
            $this->markCompleted();
            Session::set('xss_lvl3_solved', true);
            $this->completed = true;
            $response['success'] = true;
            $response['message'] = 'Payload executed successfully. Challenge complete.';
        }

        return $response;
    }

    public function validate(array $data): bool
    {
        // For DOM XSS, validation happens when verify action is called
        // Also check session flag set by attacker.php redirect
        if (Session::get('xss_lvl3_solved') === true) {
            return true;
        }
        
        return isset($data['action']) && $data['action'] === 'verify';
    }

    public function getHint(): string
    {
        return "DOM XSS occurs when user input from the URL (like #hash) is written directly to the page without sanitization. Try injecting an image tag with an onerror handler that calls fetch('challenge.php?page=lvl3&action=verify') and then redirects to /public/attacker.php?level=3&cookie='+document.cookie";
    }

    public function getTitle(): string
    {
        return "DOM Injection Terminal";
    }

    public function getDescription(): string
    {
        return "Intercept and manipulate data packets in the DOM stream. The terminal processes incoming data fragments without proper validation.";
    }
}
