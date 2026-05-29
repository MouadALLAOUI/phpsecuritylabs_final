<?php

/**
 * SQL Injection Lab - Level 2: UNION-Based Data Extraction
 * 
 * Vulnerability: UNION-based SQL Injection allowing data extraction from other tables
 * Attack Vector: Search functionality that queries agents table but allows UNION to secrets table
 * Goal: Extract all secret_key values from the secrets table
 */

namespace Labs\SQLi\Challenges;

use App\Core\BaseChallenge;
use App\Core\Database;
use App\Core\Session;

class Level2UnionExtraction extends BaseChallenge
{
    private $db;
    private $searchTerm = '';
    private $queryResult = '';
    private $attempted = false;
    private $agents = [];
    private $secretsFound = [];
    private $queryLog = [];

    public function __construct()
    {
        $this->db = Database::getInstance('labs');

        if (Session::get('sqli_lvl2_solved') === true) {
            $this->completed = true;
        }
    }

    public function handle(): void
    {
        if ($this->isPost() && isset($_POST['search'])) {
            $this->searchTerm = $_POST['search'];
            $this->attempted = true;

            // ⚠️ VULNERABLE: Direct string concatenation
            $query = "SELECT id, codename, real_name, clearance_level, status FROM agents WHERE codename LIKE '%{$this->searchTerm}%' OR real_name LIKE '%{$this->searchTerm}%'";

            $this->queryLog[] = ['time' => date('H:i:s'), 'query' => $query];

            try {
                $stmt = $this->db->query($query);
                $this->agents = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                if ($this->agents) {
                    $this->queryResult = count($this->agents) . " agent(s) found.\n";

                    // Check if any result contains columns from secrets table
                    foreach ($this->agents as $agent) {
                        if (isset($agent['secret_key']) || isset($agent['description'])) {
                            $this->secretsFound[] = $agent;
                        }
                    }

                    if (count($this->secretsFound) >= 5) {
                        $this->queryResult .= "\n★★★ CRITICAL BREACH: " . count($this->secretsFound) . " classified secrets extracted! ★★★\n";
                        $this->markCompleted('sqli', 'lvl2');
                        Session::set('sqli_lvl2_solved', true);
                        $this->completed = true;
                    }
                } else {
                    $this->queryResult = "No agents found.";
                }
            } catch (\PDOException $e) {
                // Log the detailed error server-side, show generic message to user
                error_log("SQLi Lab Level2 PDO Error: " . $e->getMessage());
                $this->queryResult = "A database error occurred. Please try again.";
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
    <span>MISSION: UNION EXTRACTION</span>
  </div>
  <div class="mission-grid">
    <div class="mission-stat">
      <div class="stat-label">OBJECTIVE</div>
      <div class="stat-value">EXTRACT 5+ SECRETS</div>
    </div>
    <div class="mission-stat">
      <div class="stat-label">TARGET</div>
      <div class="stat-value warning">SECRETS TABLE</div>
    </div>
    <div class="mission-stat">
      <div class="stat-label">TECHNIQUE</div>
      <div class="stat-value danger">UNION INJECTION</div>
    </div>
  </div>
</div>

<div class="terminal-panel">
  <div class="terminal-header">
    <i class="fas fa-search"></i>
    <span>AGENT SEARCH DATABASE</span>
  </div>
  <div class="terminal-body">
    <form method="POST">
      <div style="margin-bottom: 20px;">
        <label style="display: block; font-family: monospace; margin-bottom: 5px;">SEARCH AGENTS</label>
        <input type="text" name="search" class="mil-input" placeholder="Enter codename or real name..."
          value="<?= htmlspecialchars($this->searchTerm) ?>" maxlength="500">
      </div>
      <button type="submit" class="mil-button">SEARCH</button>
    </form>

    <?php if ($this->attempted): ?>
    <div class="output-terminal" style="margin-top: 20px; white-space: pre-wrap;">
      <?= htmlspecialchars($this->queryResult) ?>
    </div>

    <?php if (!empty($this->agents)): ?>
    <div class="output-terminal" style="margin-top: 20px;">
      <strong>AGENTS FOUND:</strong><br>
      <?php foreach ($this->agents as $agent): ?>
      • <?= htmlspecialchars($agent['codename']) ?> (Clearance: <?= $agent['clearance_level'] ?>)<br>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($this->secretsFound)): ?>
    <div class="output-terminal" style="margin-top: 20px; border-color: #ff9500;">
      <strong class="text-amber">⚠️ CLASSIFIED DATA LEAKED:</strong><br>
      <?php foreach ($this->secretsFound as $secret): ?>
      • <?= htmlspecialchars($secret['secret_key'] ?? $secret['description'] ?? '?') ?><br>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <?php if (!empty($this->queryLog)): ?>
    <div class="output-terminal" style="margin-top: 20px; background: #000; font-size: 12px;">
      <strong>QUERY LOG:</strong><br>
      <?php foreach ($this->queryLog as $log): ?>
      [<?= $log['time'] ?>] <?= htmlspecialchars($log['query']) ?><br>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="mil-hint-box" style="margin-top: 20px;">
      <strong>INTELLIGENCE HINT:</strong><br>
      Use UNION SELECT to extract from secrets table. First determine column count with ORDER BY, then:<br>
      <code>' UNION SELECT id,secret_key,description,classification_level,source FROM secrets --</code>
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
    <p class="text-green">Successfully extracted classified secrets using UNION injection.</p>
  </div>
</div>
<?php endif; ?>

<?php
        include_once ROOT . '/shared/military-ui/footer.php';
    }

    public function validate(): bool
    {
        return Session::get('sqli_lvl2_solved') === true;
    }
}