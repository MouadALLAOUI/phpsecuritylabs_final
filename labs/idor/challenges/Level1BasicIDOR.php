<?php

namespace Labs\IDOR\Challenges;

use App\Core\BaseChallenge;
use App\Core\Database;
use App\Core\Session;

/**
 * Level 1: Basic IDOR Challenge
 */
class Level1BasicIDOR extends BaseChallenge
{
    public $agentId = '';
    public $agentData = null;
    public $error = '';
    public $attempted = false;
    public $solved = false;

    public function __construct()
    {
        parent::__construct();
        if (Session::get('idor_lvl1_solved') === true) {
            $this->completed = true;
            $this->solved = true;
        }
    }

    public function render(): void
    {
        include_once ROOT . '/shared/military-ui/header.php';
        
        include ROOT . '/labs/idor/views/level1.php';
        
        include_once ROOT . '/shared/military-ui/footer.php';
    }
    
    public function handle(): void
    {
        if ($this->isPost()) {
            $this->agentId = $this->getInput('id');
            $this->attempted = true;
            
            if (empty($this->agentId)) {
                $this->error = "Error: Missing operational agent index.";
                return;
            }

            // Intentional vulnerability - direct query without any authorization check
            $db = Database::getInstance('labs');
            try {
                $stmt = $db->prepare("SELECT id, codename, real_name, clearance_level, status FROM agents WHERE id = :id");
                $stmt->execute(['id' => $this->agentId]);
                $agent = $stmt->fetch(\PDO::FETCH_ASSOC);
                
                if ($agent) {
                    $this->agentData = $agent;
                    // Check solve condition: they accessed another agent's data (especially level 5 agent)
                    if ($agent['clearance_level'] >= 5) {
                        $this->agentData['flag'] = "FLAG{IDOR_DIRECT_OBJECT_UNAUTHORIZED}";
                        Session::set('idor_lvl1_solved', true);
                        $this->markCompleted('idor', 'lvl1');
                        $this->completed = true;
                        $this->solved = true;
                    }
                } else {
                    $this->error = "Access Denied: Agent record index not found in active operations directory.";
                }
            } catch (\Exception $e) {
                $this->error = "Database query failure: " . $e->getMessage();
            }
        }
    }
    
    public function validate(): bool
    {
        return Session::get('idor_lvl1_solved') === true;
    }
}
