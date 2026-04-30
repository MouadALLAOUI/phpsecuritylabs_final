<?php
/**
 * SQL Injection Lab - Level 2: UNION-Based Data Extraction
 * 
 * Vulnerability: UNION-based SQL Injection allowing data extraction from other tables
 * Attack Vector: Search functionality that queries agents table but allows UNION to secrets table
 * Goal: Extract all secret_key values from the secrets table
 * 
 * This challenge demonstrates how attackers can use UNION SELECT to combine results
 * from multiple tables, extracting sensitive data they shouldn't have access to.
 */

namespace Labs\SQLi\Challenges;

use App\Core\ChallengeInterface;
use App\Core\BaseChallenge;
use App\Core\Database;
use App\Core\Session;

class Level2UnionExtraction extends BaseChallenge implements ChallengeInterface
{
    private $completed = false;
    private $db;
    private $extractedSecrets = [];

    public function __construct()
    {
        $this->db = Database::getInstance('labs');
        
        // Check if already solved via session
        if (Session::get('sqli_lvl2_solved') === true) {
            $this->completed = true;
        }
    }

    public function render(): string
    {
        ob_start();
        include __DIR__ . '/../views/search_terminal.php';
        return ob_get_clean();
    }

    public function handle(array $data): array
    {
        $response = [
            'success' => false, 
            'message' => '', 
            'query' => '', 
            'results' => [],
            'secrets_found' => []
        ];

        if (isset($data['search'])) {
            $searchTerm = $data['search'] ?? '';

            // ⚠️ VULNERABLE: Direct string concatenation in search query
            // The query searches agents by codename or real_name
            $query = "SELECT id, codename, real_name, clearance_level, status FROM agents WHERE codename LIKE '%$searchTerm%' OR real_name LIKE '%$searchTerm%'";
            
            $response['query'] = $query;

            try {
                // Execute the vulnerable query
                $stmt = $this->db->query($query);
                $agents = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                if ($agents) {
                    $response['success'] = true;
                    $response['results'] = $agents;
                    $response['message'] = count($agents) . ' agent record(s) found.';
                    
                    // Track which secrets were "exposed" via injection
                    // If UNION was used to extract from secrets table, mark those
                    foreach ($agents as $agent) {
                        // Check if any result contains secret_key pattern (from UNION injection)
                        if (isset($agent['secret_key']) || isset($agent['description'])) {
                            $response['secrets_found'][] = $agent;
                        }
                    }
                    
                    // If they extracted 5+ secrets, they completed the challenge
                    if (count($response['secrets_found']) >= 5) {
                        $this->markCompleted();
                        Session::set('sqli_lvl2_solved', true);
                        $this->completed = true;
                        $response['message'] = '★★★ CRITICAL BREACH DETECTED: ' . count($response['secrets_found']) . ' classified secrets extracted! Challenge Complete! ★★★';
                    }
                } else {
                    $response['message'] = 'No agents found matching search criteria.';
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
        // Check session flag set during handle()
        if (Session::get('sqli_lvl2_solved') === true) {
            return true;
        }
        return false;
    }

    public function getHint(): string
    {
        return "Use UNION SELECT to extract data from the secrets table. First determine column count with ORDER BY, then use UNION SELECT NULL,NULL,... to match columns. Example: ' UNION SELECT id,secret_key,description,classification_level,source FROM secrets --";
    }

    public function getTitle(): string
    {
        return "Agent Search Database";
    }

    public function getDescription(): string
    {
        return "Extract classified secrets from the secure database using UNION-based SQL injection. Recover at least 5 secret_key values.";
    }
}
