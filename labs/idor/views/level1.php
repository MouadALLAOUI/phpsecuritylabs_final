<?php
/**
 * IDOR Lab - Level 1 View Template
 */
?>
<div class="mission-header">
  <div class="mission-title">
    <i class="fas fa-id-card"></i>
    <span>MISSION: IDOR DIRECT BREACH</span>
  </div>
  <div class="mission-grid">
    <div class="mission-stat">
      <div class="stat-label">OBJECTIVE</div>
      <div class="stat-value">FETCH CLASSIFIED RECORD</div>
    </div>
    <div class="mission-stat">
      <div class="stat-label">TARGET</div>
      <div class="stat-value warning">AGENT DATABASE</div>
    </div>
    <div class="mission-stat">
      <div class="stat-label">CLEARANCE</div>
      <div class="stat-value danger">LEVEL 5 ACCESS</div>
    </div>
  </div>
</div>

<!-- Mission Briefing -->
<div class="mission-briefing" style="background: rgba(255, 170, 0, 0.05); border-color: #ffaa00;">
  <div class="briefing-icon" style="color: #ffaa00;"><i class="fas fa-bullseye"></i></div>
  <div class="briefing-content">
    <h3 style="color: #ffaa00;">Mission Objective</h3>
    <p>This operations viewer displays agent records by ID. Your logged-in student account (GHOST) represents Agent ID 3.</p>
    <p>Exploit Insecure Direct Object References (IDOR) to access other agents' records. Retrieve the classified data belonging to the Lead Administrator (Agent ID 1) to retrieve the flag.</p>
  </div>
</div>

<!-- Error/Alert messages -->
<?php if (!empty($this->error)): ?>
<div class="alert-box alert-error" style="background: rgba(220, 38, 38, 0.1); border: 1px solid #dc2626; color: #f87171; padding: 15px; margin-bottom: 20px;">
  <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($this->error) ?>
</div>
<?php endif; ?>

<!-- Main Interaction Terminal -->
<div class="terminal-panel">
  <div class="terminal-header">
    <i class="fas fa-terminal"></i>
    <span>AGENT RECORD DIRECTORY QUERY</span>
  </div>
  <div class="terminal-body">
    <form method="POST">
      <div style="margin-bottom: 20px;">
        <label style="display: block; font-family: var(--font-mono); font-size: 12px; color: var(--text-secondary); margin-bottom: 8px;">
          <i class="fas fa-fingerprint"></i> SPECIFY OPERATIONAL AGENT RECORD INDEX (ID)
        </label>
        <input type="text" name="id" class="mil-input" placeholder="e.g. 3" 
          value="<?= htmlspecialchars($this->agentId) ?>" maxlength="10">
      </div>
      <button type="submit" class="mil-button">
        <i class="fas fa-database"></i> QUERY DIRECTORY
      </button>
    </form>

    <!-- Query Results Display -->
    <?php if ($this->attempted && $this->agentData): ?>
    <div class="output-terminal" style="margin-top: 20px; border-color: #00ff41; background: rgba(0, 255, 65, 0.02);">
      <strong style="color: #00ff41; font-family: var(--font-mono);"><i class="fas fa-user-shield"></i> INTEL SUMMARY RETRIEVED:</strong>
      <div style="margin-top: 15px; font-family: var(--font-mono); font-size: 12px; line-height: 1.8;">
        <div>• AGENT REGISTRY INDEX : <span style="color: #00ff41;"><?= htmlspecialchars($this->agentData['id']) ?></span></div>
        <div>• AGENT CODENAME        : <span style="color: #00ff41;"><?= htmlspecialchars($this->agentData['codename']) ?></span></div>
        <div>• REAL NAME            : <span style="color: #00ff41;"><?= htmlspecialchars($this->agentData['real_name']) ?></span></div>
        <div>• CLEARANCE LEVEL      : <span style="color: #ffaa00; font-weight: bold;"><?= htmlspecialchars($this->agentData['clearance_level']) ?></span></div>
        <div>• ACTIVE STATUS        : <span style="color: #00ff41; font-weight: bold;"><?= htmlspecialchars($this->agentData['status']) ?></span></div>
        
        <?php if (!empty($this->agentData['flag'])): ?>
        <div style="margin-top: 15px; padding: 10px; border: 1px dashed #ffaa00; background: rgba(255,170,0,0.05); color: #ffaa00;">
          <strong>★ CLASSIFIED OPERATIONAL KEY DETECTED:</strong><br>
          <span style="font-size: 13px; font-weight: bold;"><?= htmlspecialchars($this->agentData['flag']) ?></span>
        </div>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- Success Banner -->
<?php if ($this->solved): ?>
<div class="success-banner" style="margin-top: 20px; background: rgba(0, 255, 65, 0.1); border-color: #00ff41;">
  <i class="fas fa-check-circle" style="color: #00ff41;"></i>
  <div>
    <h3 style="color: #00ff41; font-family: var(--font-mono);">MISSION ACCOMPLISHED</h3>
    <p>You successfully bypassed object-level access controls and read a high-clearance agent profile!</p>
  </div>
</div>
<?php endif; ?>

<!-- Hint briefings -->
<div class="mil-hint-box" style="margin-top: 20px;">
  <strong>INTELLIGENCE BRIEFING (HINT):</strong><br>
  Query your own ID (3) to see your record. Note that the application accepts arbitrary ID indexes in the query fields. Try iterating the ID to 1 to breach the Lead Administrator's profile.
</div>
