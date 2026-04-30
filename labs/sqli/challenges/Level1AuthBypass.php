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

use App\Core\BaseChallenge;
use App\Core\Database;
use App\Core\Session;

class Level1AuthBypass extends BaseChallenge
{
    private Database $db;
    private string $codename = '';
    private string $password = '';
    private string $queryResult = '';
    private bool $attempted = false;
    private array $queryLog = [];

    public function __construct()
    {
        $this->db = Database::getInstance('labs');
      
        if (Session::get('sqli_lvl1_solved') === true) {
            $this->completed = true;
        }
    }

    public function handle(): void
    {
        if ($this->isPost()) {
            $this->codename = $_POST['codename'] ?? '';
            $this->password = $_POST['password'] ?? '';
            $this->attempted = true;

            // ⚠️ VULNERABLE: Direct string concatenation - NO SANITIZATION
            // The agents table uses 'codename' not 'username', and has no password column
            // Users must inject to bypass the status check entirely
            $query = "SELECT id, codename, real_name, clearance_level, unit_id, status FROM agents WHERE codename = '{$this->codename}' AND status = '{$this->password}'";

            $this->queryLog[] = [
                'timestamp' => date('H:i:s'),
                'query' => $query
            ];

            try {
                $stmt = $this->db->query($query);
                $agent = $stmt->fetch(\PDO::FETCH_ASSOC);

                if ($agent) {
                    $this->queryResult = "ACCESS GRANTED\n\nAgent: " . htmlspecialchars($agent['codename']) . 
                                         "\nReal Name: " . htmlspecialchars($agent['real_name']) . 
                                         "\nClearance Level: " . $agent['clearance_level'] . 
                                         "\nUnit ID: " . $agent['unit_id'] . 
                                         "\nStatus: " . $agent['status'];
                    
                    if ($agent['clearance_level'] >= 5) {
                        $this->queryResult .= "\n\n★★★ TOP SECRET ACCESS OBTAINED ★★★\nChallenge complete!";
                        $this->markCompleted('sqli', 'lvl1');
                        Session::set('sqli_lvl1_solved', true);
                        $this->completed = true;
                    }
                } else {
                    $this->queryResult = "ACCESS DENIED\nInvalid credentials.\n[ATTEMPT LOGGED]";
                }
            } catch (\PDOException $e) {
                $this->queryResult = "SQL ERROR: " . $e->getMessage();
            }
        }
    }

    public function render(): void
    {
        include_once ROOT . '/shared/military-ui/header.php';
?>
<div class="mission-header">
    <div class="mission-title">
        <i class="fas fa-database"></i>
        <span>MISSION: DATABASE BREACH</span>
    </div>
    <div class="mission-grid">
        <div class="mission-stat">
            <div class="stat-label">OBJECTIVE</div>
            <div class="stat-value">BYPASS AUTHENTICATION</div>
        </div>
        <div class="mission-stat">
            <div class="stat-label">TARGET</div>
            <div class="stat-value warning">AGENTS TABLE</div>
        </div>
        <div class="mission-stat">
            <div class="stat-label">CLEARANCE</div>
            <div class="stat-value danger">LEVEL 1</div>
        </div>
    </div>
</div>

<div class="terminal-panel">
    <div class="terminal-header">
        <i class="fas fa-terminal"></i>
        <span>AUTHENTICATION TERMINAL</span>
    </div>
    <div class="terminal-body">
        <form method="POST">
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-family: monospace; margin-bottom: 5px;">AGENT CODENAME</label>
                <input type="text" name="codename" class="mil-input" 
                       placeholder="Enter codename (e.g., GHOST)" 
                       value="<?= htmlspecialchars($this->codename) ?>">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-family: monospace; margin-bottom: 5px;">STATUS CODE</label>
                <input type="password" name="password" class="mil-input" 
                       placeholder="Enter status code...">
            </div>
            <button type="submit" class="mil-button">AUTHENTICATE</button>
        </form>

        <?php if ($this->attempted): ?>
        <div class="output-terminal" style="margin-top: 20px; white-space: pre-wrap;">
            <?= htmlspecialchars($this->queryResult) ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($this->queryLog)): ?>
        <div class="output-terminal" style="margin-top: 20px; background: #000; font-size: 12px;">
            <strong>QUERY LOG:</strong><br>
            <?php foreach ($this->queryLog as $log): ?>
            [<?= $log['timestamp'] ?>] <?= htmlspecialchars($log['query']) ?><br>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="mil-hint-box" style="margin-top: 20px;">
            <strong>INTELLIGENCE:</strong> Try <code>' OR '1'='1' --</code> in the codename field to bypass status check.  
            To complete the challenge, obtain an agent with clearance level 5.
        </div>
    </div>
</div>

<?php if ($this->completed): ?>
<div class="terminal-panel" style="border-color: #00ff41;">
    <div class="terminal-header" style="background: rgba(0,255,65,0.1);">
        <i class="fas fa-check-circle text-green"></i>
        <span class="text-green">MISSION ACCOMPLISHED</span>
    </div>
    <div class="terminal-body">
        <p class="text-green">SQL Injection successful. Top‑secret access granted.</p>
    </div>
</div>
<?php endif; ?>

<?php
        include_once ROOT . '/shared/military-ui/footer.php';
    }

    public function validate(): bool
    {
        return Session::get('sqli_lvl1_solved') === true;
    }
}