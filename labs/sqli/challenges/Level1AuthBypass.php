<?php
/**
 * SQL Injection Lab - Level 1
 * 
 * Vulnerability: Classic SQL Injection via unsanitized user input in WHERE clause
 * Attack Vector: Authentication bypass and data extraction
 * 
 * This challenge demonstrates SQL injection where user input is directly
 * concatenated into SQL queries without proper escaping or parameterization.
 */

namespace App\Labs\SQLi\Challenges;

use App\Core\ChallengeInterface;
use App\Core\Database;

class Level1AuthBypass implements ChallengeInterface
{
    private $completed = false;
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance('labs');
    }

    public function render(): string
    {
        ob_start();
        include __DIR__ . '/views/auth_terminal.php';
        return ob_get_clean();
    }

    public function handle(array $data): array
    {
        $response = ['success' => false, 'message' => '', 'query' => '', 'result' => null];

        if (isset($data['username']) || isset($data['password'])) {
            $username = $data['username'] ?? '';
            $password = $data['password'] ?? '';

            // ⚠️ VULNERABLE: Direct string concatenation - NO SANITIZATION
            $query = "SELECT * FROM agents WHERE username = '$username' AND password = '$password'";
            
            $response['query'] = $query; // Show the raw query for educational purposes

            try {
                // Execute the vulnerable query
                $stmt = $this->db->query($query);
                $user = $stmt->fetch(\PDO::FETCH_ASSOC);

                if ($user) {
                    $response['success'] = true;
                    $response['result'] = [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'clearance' => $user['clearance_level'],
                        'department' => $user['department']
                    ];
                    
                    // Check if they extracted admin access (clearance level 5)
                    if ($user['clearance_level'] >= 5) {
                        $this->markCompleted();
                        $response['message'] = 'ACCESS GRANTED: Administrative privileges obtained. Challenge complete.';
                    } else {
                        $response['message'] = 'ACCESS GRANTED: Welcome, Agent ' . htmlspecialchars($user['username']);
                    }
                } else {
                    $response['message'] = 'ACCESS DENIED: Invalid credentials';
                }
            } catch (\PDOException $e) {
                $response['message'] = 'SQL ERROR: ' . $e->getMessage();
                $response['success'] = false;
            }
        }

        return $response;
    }

    public function validate(array $data): bool
    {
        // Validation is handled in handle() by checking clearance level
        return false;
    }

    public function getHint(): string
    {
        return "SQL Injection allows you to manipulate database queries. Try using a classic payload like ' OR '1'='1 to bypass authentication. To extract all records, use UNION-based injection.";
    }

    public function getTitle(): string
    {
        return "Authentication Terminal";
    }

    public function getDescription(): string
    {
        return "Bypass the authentication system to gain unauthorized access to classified agent records.";
    }
}
