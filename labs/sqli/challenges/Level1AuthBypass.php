<?php
/**
 * SQL Injection Lab - Level 1: Authentication Bypass
 * 
 * Vulnerability: Classic SQL Injection via unsanitized user input in WHERE clause
 * Attack Vector: Authentication bypass using codename field
 * Goal: Gain access to an agent account with clearance level 5 or higher
 * 
 * This challenge demonstrates SQL injection where user input is directly
 * concatenated into SQL queries without proper escaping or parameterization.
 * 
 * Target Table: agents (codename, real_name, clearance_level, unit_id, status)
 */

namespace Labs\SQLi\Challenges;

use App\Core\ChallengeInterface;
use App\Core\BaseChallenge;
use App\Core\Database;
use App\Core\Session;

class Level1AuthBypass extends BaseChallenge implements ChallengeInterface
{
    private $completed = false;
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance('labs');
        
        // Check if already solved via session
        if (Session::get('sqli_lvl1_solved') === true) {
            $this->completed = true;
        }
    }

    public function render(): string
    {
        ob_start();
        include __DIR__ . '/../views/auth_terminal.php';
        return ob_get_clean();
    }

    public function handle(array $data): array
    {
        $response = ['success' => false, 'message' => '', 'query' => '', 'result' => null, 'access_granted' => false];

        if (isset($data['codename']) || isset($data['password'])) {
            $codename = $data['codename'] ?? '';
            $password = $data['password'] ?? '';

            // ⚠️ VULNERABLE: Direct string concatenation - NO SANITIZATION
            // The agents table uses 'codename' not 'username', and has no password column
            // Users must inject to bypass the password check entirely
            $query = "SELECT id, codename, real_name, clearance_level, unit_id, status FROM agents WHERE codename = '$codename' AND status = '$password'";
            
            $response['query'] = $query; // Show the raw query for educational purposes

            try {
                // Execute the vulnerable query
                $stmt = $this->db->query($query);
                $agent = $stmt->fetch(\PDO::FETCH_ASSOC);

                if ($agent) {
                    $response['success'] = true;
                    $response['access_granted'] = true;
                    $response['result'] = [
                        'id' => $agent['id'],
                        'codename' => $agent['codename'],
                        'real_name' => $agent['real_name'],
                        'clearance_level' => $agent['clearance_level'],
                        'unit_id' => $agent['unit_id'],
                        'status' => $agent['status']
                    ];
                    
                    // Check if they extracted high-clearance access (level 5 = top secret)
                    if ($agent['clearance_level'] >= 5) {
                        $this->markCompleted();
                        Session::set('sqli_lvl1_solved', true);
                        $this->completed = true;
                        $response['message'] = '★★★ ACCESS GRANTED: TOP SECRET clearance verified. Agent ' . htmlspecialchars($agent['codename']) . ' authenticated. Challenge Complete! ★★★';
                    } else {
                        $response['message'] = 'ACCESS GRANTED: Welcome, Agent ' . htmlspecialchars($agent['codename']) . '. Clearance Level: ' . $agent['clearance_level'] . '. Note: Higher clearance (Level 5+) required for full access.';
                    }
                } else {
                    $response['message'] = 'ACCESS DENIED: Invalid credentials or agent not found';
                }
            } catch (\PDOException $e) {
                $response['message'] = 'SQL ERROR: ' . $e->getMessage();
                $response['success'] = false;
                $response['access_granted'] = false;
            }
        }

        return $response;
    }

    public function validate(array $data): bool
    {
        // Check session flag set during handle()
        if (Session::get('sqli_lvl1_solved') === true) {
            return true;
        }
        return false;
    }

    public function getHint(): string
    {
        return "The query checks codename AND status fields. Use SQL injection to bypass the status check. Try: GHOST' -- or use ' OR '1'='1' -- to return all agents. To complete the challenge, access an agent with clearance level 5 or higher.";
    }

    public function getTitle(): string
    {
        return "Secure Authentication Gateway";
    }

    public function getDescription(): string
    {
        return "Bypass the authentication terminal to gain unauthorized access to classified agent records. Extract credentials for a Level 5 cleared agent.";
    }
}
