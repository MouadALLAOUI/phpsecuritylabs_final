<?php

namespace Labs\XSS\Challenges;

use App\Core\BaseChallenge;
use App\Core\Session;

class Level3Dom extends BaseChallenge
{
    public function __construct()
    {
        if (Session::get('xss_lvl3_solved') === true) {
            $this->completed = true;
        }
    }

    public function handle(): void
    {
        // DOM XSS is client-side; no server handling needed.
        // However, we need to capture the verify action from the payload.
        if (isset($_GET['action']) && $_GET['action'] === 'verify') {
            $this->markCompleted('xss', 'lvl3');
            Session::set('xss_lvl3_solved', true);
            $this->completed = true;
            header('Location: ?page=xss&lvl=3&status=complete');
            exit;
        }
    }

    public function render(): void
    {
        include_once ROOT . '/shared/military-ui/header.php';
?>
<div class="mission-header">
  <div class="mission-title">
    <i class="fas fa-code"></i>
    <span>MISSION: DOM INJECTION</span>
  </div>
  <div class="mission-grid">
    <div class="mission-stat">
      <div class="stat-label">OBJECTIVE</div>
      <div class="stat-value">STEAL COOKIE VIA DOM XSS</div>
    </div>
    <div class="mission-stat">
      <div class="stat-label">VULNERABILITY</div>
      <div class="stat-value warning">innerHTML from hash</div>
    </div>
  </div>
</div>

<div class="terminal-panel">
  <div class="terminal-header">
    <i class="fas fa-terminal"></i>
    <span>DATA STREAM MONITOR</span>
  </div>
  <div class="terminal-body">
    <div id="userDisplay" class="output-terminal" style="min-height: 80px;">Loading...</div>
    <div class="mil-hint-box" style="margin-top: 20px;">
      <strong>INTELLIGENCE:</strong> Append
      <code>#user=&lt;script&gt;fetch('/?page=xss&lvl=3&action=verify')&lt;/script&gt;</code> to URL.
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const hash = window.location.hash.substring(1);
  const display = document.getElementById('userDisplay');
  if (hash) {
    display.innerHTML = hash; // VULNERABLE
  } else {
    display.innerHTML = 'Awaiting data stream... Append #user=YOURPAYLOAD';
  }
});
</script>

<?php if (isset($_GET['status']) && $_GET['status'] === 'complete'): ?>
<div class="terminal-panel" style="border-color: #00ff41;">
  <div class="terminal-header" style="background: rgba(0,255,65,0.1);">
    <i class="fas fa-check-circle text-green"></i>
    <span class="text-green">MISSION ACCOMPLISHED</span>
  </div>
</div>
<?php endif; ?>

<?php
        include_once ROOT . '/shared/military-ui/footer.php';
    }

    public function validate(): bool
    {
        return Session::get('xss_lvl3_solved') === true;
    }
}