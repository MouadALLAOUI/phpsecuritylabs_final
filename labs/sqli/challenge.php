<?php
/**
 * SQL INJECTION LAB - MIL-OPS INTERFACE
 * Mission: Database Breach
 * Target: Personnel Database
 */

namespace Labs\SQLi;

use App\Core\BaseChallenge;
use App\Core\Session;

class DatabaseBreach extends BaseChallenge
{
    private string $username = '';
    private string $password = '';
    private string $queryResult = '';
    private bool $loginAttempted = false;
    private bool $solved = false;
    private array $queryLog = [];

    public function handle(): void
    {
        // Check if already solved
        if (Session::get('sqli_solved') === true) {
            $this->solved = true;
        }

        if ($this->isPost()) {
            $this->username = $_POST['username'] ?? '';
            $this->password = $_POST['password'] ?? '';
            $this->loginAttempted = true;

            // Log the query for display
            $rawQuery = "SELECT * FROM agents WHERE username = '{$this->username}' AND password = '{$this->password}'";
            $this->queryLog[] = [
                'timestamp' => date('H:i:s'),
                'query' => $rawQuery,
                'status' => 'executed'
            ];

            // ⚠️ VULNERABILITY: Direct string interpolation - SQL Injection possible
            // Simulating vulnerable database query
            if ($this->username === 'admin' && $this->password === 'secret123') {
                $this->queryResult = "LOGIN SUCCESSFUL\n\nWelcome, Administrator.\nAccess Level: TOP SECRET\n\n=== AGENT DATABASE EXTRACT ===\nID | CODENAME | STATUS | CLEARANCE\n001 | GHOST | ACTIVE | LEVEL 4\n002 | VIPER | ACTIVE | LEVEL 3\n003 | PHANTOM | MIA | LEVEL 4\n004 | ECHO | DECEASED | LEVEL 2\n005 | RAVEN | ACTIVE | LEVEL 3\n\n[CLASSIFIED DATA UNLOCKED]";
                $this->solved = true;
                Session::set('sqli_solved', true);
                $this->queryLog[count($this->queryLog) - 1]['status'] = 'success';
            } elseif (strpos($this->username, "' OR '1'='1") !== false || 
                      strpos($this->username, "' OR 1=1--") !== false ||
                      strpos($this->username, "admin'--") !== false) {
                // Detect common SQL injection patterns
                $this->queryResult = "LOGIN SUCCESSFUL\n\n⚠️ SQL INJECTION DETECTED ⚠️\n\nBypass successful. Authentication circumvented.\n\n=== FULL DATABASE DUMP ===\nTABLE: agents\n+----+----------+------------+-----------+\n| id | username | password   | clearance |\n+----+----------+------------+-----------+\n| 1  | admin    | secret123  | LEVEL 5   |\n| 2  | agent1   | pass456    | LEVEL 2   |\n| 3  | agent2   | word789    | LEVEL 3   |\n| 4  | guest    | guest      | LEVEL 1   |\n+----+----------+------------+-----------+\n\n[TARGET COMPROMISED - MISSION COMPLETE]";
                $this->solved = true;
                Session::set('sqli_solved', true);
                $this->queryLog[count($this->queryLog) - 1]['status'] = 'injection_detected';
            } else {
                $this->queryResult = "AUTHENTICATION FAILED\n\nInvalid credentials provided.\nAccess denied.\n\n[ATTEMPT LOGGED - SECURITY ALERT TRIGGERED]";
                $this->queryLog[count($this->queryLog) - 1]['status'] = 'failed';
            }
        }
    }

    public function render(): void
    {
        include_once ROOT . '/shared/military-ui/header.php';
?>

<!-- MISSION HEADER -->
<div class="mission-header">
  <div class="mission-title">
    <i class="fas fa-database"></i>
    <span>MISSION: DATABASE BREACH</span>
  </div>
  
  <div class="mission-grid">
    <div class="mission-stat">
      <div class="stat-label">OBJECTIVE</div>
      <div class="stat-value">EXTRACT AGENT RECORDS</div>
    </div>
    <div class="mission-stat">
      <div class="stat-label">TARGET SYSTEM</div>
      <div class="stat-value warning">PERSONNEL DATABASE</div>
    </div>
    <div class="mission-stat">
      <div class="stat-label">CLEARANCE REQUIRED</div>
      <div class="stat-value danger">LEVEL 2</div>
    </div>
    <div class="mission-stat">
      <div class="stat-label">VULNERABILITY TYPE</div>
      <div class="stat-value text-blue">SQL INJECTION</div>
    </div>
  </div>
</div>

<!-- BRIEFING PANEL -->
<div class="terminal-panel">
  <div class="terminal-header">
    <i class="fas fa-file-contract"></i>
    <span>MISSION BRIEFING</span>
  </div>
  <div class="terminal-body">
    <p style="color: var(--text-secondary); margin-bottom: 15px;">
      <span class="text-green">OPERATOR,</span> your mission is to infiltrate the enemy personnel database. 
      Standard authentication protocols are in place, but intelligence suggests the system is vulnerable to 
      <span class="text-amber">query manipulation attacks</span>.
    </p>
    <p style="color: var(--text-secondary); margin-bottom: 15px;">
      The database contains classified agent information. Your objective is to bypass authentication and 
      extract all records. No brute force - use intelligence.
    </p>
    <div style="background: rgba(255, 176, 0, 0.1); border-left: 3px solid var(--mil-amber); padding: 12px; font-family: var(--font-mono); font-size: 12px;">
      <span class="text-amber"><i class="fas fa-lightbulb"></i> INTELLIGENCE HINT:</span><br>
      The login form directly concatenates user input into SQL queries. Consider how special characters 
      might affect query logic. Common payloads: <code class="bg-panel-light px-1">' OR '1'='1</code>, 
      <code class="bg-panel-light px-1">admin'--</code>
    </div>
  </div>
</div>

<!-- LOGIN TERMINAL -->
<div class="terminal-panel">
  <div class="terminal-header">
    <i class="fas fa-terminal"></i>
    <span>AUTHENTICATION TERMINAL</span>
  </div>
  <div class="terminal-body">
    <form method="POST" id="sqliForm">
      <div style="margin-bottom: 20px;">
        <label style="display: block; font-family: var(--font-mono); font-size: 12px; color: var(--text-secondary); margin-bottom: 8px;">
          <i class="fas fa-user"></i> OPERATOR ID
        </label>
        <input type="text" name="username" class="mil-input" placeholder="Enter operator ID..." value="<?= htmlspecialchars($this->username) ?>" autocomplete="off">
      </div>
      
      <div style="margin-bottom: 20px;">
        <label style="display: block; font-family: var(--font-mono); font-size: 12px; color: var(--text-secondary); margin-bottom: 8px;">
          <i class="fas fa-key"></i> ACCESS CODE
        </label>
        <input type="password" name="password" class="mil-input" placeholder="Enter access code..." autocomplete="off">
      </div>
      
      <button type="submit" class="mil-button">
        <i class="fas fa-sign-in-alt"></i> AUTHENTICATE
      </button>
    </form>
  </div>
</div>

<!-- QUERY LOG TERMINAL -->
<?php if (!empty($this->queryLog)): ?>
<div class="terminal-panel">
  <div class="terminal-header">
    <i class="fas fa-scroll"></i>
    <span>QUERY EXECUTION LOG</span>
  </div>
  <div class="terminal-body">
    <div class="output-terminal" style="min-height: 100px;">
<?php foreach ($this->queryLog as $log): ?>
      <div style="margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px dashed var(--border-color);">
        <span class="text-dim" style="font-size: 10px;">[<?= htmlspecialchars($log['timestamp']) ?>]</span>
        <?php if ($log['status'] === 'success' || $log['status'] === 'injection_detected'): ?>
          <span class="text-green">[SUCCESS]</span>
        <?php elseif ($log['status'] === 'failed'): ?>
          <span class="text-red">[FAILED]</span>
        <?php endif; ?>
        <br>
        <code style="color: var(--mil-green);"><?= htmlspecialchars($log['query']) ?></code>
      </div>
<?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- RESULT TERMINAL -->
<?php if ($this->loginAttempted): ?>
<div class="terminal-panel">
  <div class="terminal-header">
    <i class="fas fa-desktop"></i>
    <span>QUERY RESULT OUTPUT</span>
  </div>
  <div class="terminal-body">
    <div class="output-terminal <?= $this->solved ? '' : 'error' ?>">
<?= htmlspecialchars($this->queryResult) ?>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- SUCCESS STATE -->
<?php if ($this->solved): ?>
<div class="terminal-panel" style="border-color: var(--mil-green);">
  <div class="terminal-header" style="background: rgba(0, 255, 65, 0.1);">
    <i class="fas fa-check-circle text-green"></i>
    <span class="text-green">MISSION ACCOMPLISHED</span>
  </div>
  <div class="terminal-body">
    <p class="text-green" style="font-family: var(--font-mono);">
      <i class="fas fa-trophy"></i> DATABASE BREACH SUCCESSFUL<br><br>
      You have successfully exploited the SQL injection vulnerability.<br>
      All agent records have been extracted.<br><br>
      <span class="text-dim">Technique: Authentication bypass via SQL injection</span>
    </p>
  </div>
</div>
<?php endif; ?>

<script>
// Initialize SQLi Lab specific functionality
document.addEventListener('DOMContentLoaded', function() {
  console.log('%c SQL INJECTION LAB LOADED ', 'background: #ff3333; color: white;');
  
  // Add form submission effect
  const form = document.getElementById('sqliForm');
  if (form) {
    form.addEventListener('submit', function() {
      showLoading('EXECUTING QUERY...');
      setTimeout(() => {
        hideLoading();
      }, 800);
    });
  }
});
</script>

<?php
        include_once ROOT . '/shared/military-ui/footer.php';
    }

    public function validate(): bool
    {
        return Session::get('sqli_solved') === true;
    }
}
