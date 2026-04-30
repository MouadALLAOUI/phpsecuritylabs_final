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

namespace App\Labs\XSS\Challenges;

use App\Core\ChallengeInterface;
use App\Core\Database;

class Level3Dom implements ChallengeInterface
{
    private $completed = false;

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

        // Handle verification action (called by the payload via AJAX)
        if (isset($data['action']) && $data['action'] === 'verify') {
            $this->markCompleted();
            $response['success'] = true;
            $response['message'] = 'Payload executed successfully. Challenge complete.';
        }

        return $response;
    }

    public function validate(array $data): bool
    {
        // For DOM XSS, validation happens client-side when the payload executes
        // We just check if the verification action was called
        return isset($data['action']) && $data['action'] === 'verify';
    }

    public function getHint(): string
    {
        return "DOM XSS occurs when user input from the URL (like #hash) is written directly to the page without sanitization. Try injecting an image tag with an onerror handler that calls the verification endpoint.";
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
