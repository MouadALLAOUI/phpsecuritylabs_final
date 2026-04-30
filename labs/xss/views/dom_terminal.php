<?php
/**
 * XSS Level 3 - DOM Terminal View
 * 
 * VULNERABLE: This page reads from window.location.hash and writes to innerHTML
 * without any sanitization, creating a DOM XSS vulnerability.
 */

// Get challenge instance for hint
$challenge = null;
if (isset($this)) {
    $challenge = $this;
}
?>
<div class="mil-mission-container">
    <!-- Mission Header -->
    <div class="mil-mission-header">
        <div class="mil-mission-title">
            <span class="mil-icon">◈</span>
            <span>MISSION: DOM INJECTION</span>
        </div>
        <div class="mil-mission-meta">
            <div class="mil-clearance-badge clearance-3">LEVEL 3</div>
            <div class="mil-status-indicator active"></div>
        </div>
    </div>

    <!-- Mission Objective -->
    <div class="mil-mission-brief">
        <div class="mil-brief-section">
            <span class="mil-label">OBJECTIVE:</span>
            <span class="mil-value">Inject payload via URL hash to execute code in DOM</span>
        </div>
        <div class="mil-brief-section">
            <span class="mil-label">TARGET:</span>
            <span class="mil-value">Client-Side Data Terminal</span>
        </div>
        <div class="mil-brief-section">
            <span class="mil-label">VULNERABILITY:</span>
            <span class="mil-value">DOM-based XSS (unsanitized innerHTML)</span>
        </div>
        <div class="mil-brief-section">
            <span class="mil-label">PAYLOAD GOAL:</span>
            <span class="mil-value">Exfiltrate session cookie to attacker server</span>
        </div>
    </div>

    <!-- Terminal Interface -->
    <div class="mil-terminal-panel">
        <div class="mil-terminal-header">
            <span class="mil-terminal-title">DATA STREAM MONITOR</span>
            <div class="mil-terminal-controls">
                <span class="mil-control-dot"></span>
                <span class="mil-control-dot"></span>
                <span class="mil-control-dot"></span>
            </div>
        </div>
        
        <div class="mil-terminal-body">
            <!-- User Info Display (VULNERABLE AREA) -->
            <div class="mil-data-display">
                <div class="mil-data-label">INCOMING DATA FRAGMENT:</div>
                <div id="userDisplay" class="mil-user-display">
                    <!-- VULNERABLE: This gets populated from window.location.hash -->
                    Waiting for data stream...
                </div>
            </div>

            <!-- System Log -->
            <div class="mil-system-log" id="systemLog">
                <div class="mil-log-entry">
                    <span class="mil-log-time">[SYSTEM]</span>
                    <span class="mil-log-msg">Terminal initialized...</span>
                </div>
                <div class="mil-log-entry">
                    <span class="mil-log-time">[SYSTEM]</span>
                    <span class="mil-log-msg">Monitoring data stream on channel #hash...</span>
                </div>
                <div class="mil-log-entry">
                    <span class="mil-log-time">[SYSTEM]</span>
                    <span class="mil-log-msg">WARNING: Input validation disabled</span>
                </div>
            </div>

            <!-- Instructions -->
            <div class="mil-instructions">
                <div class="mil-instruction-title">OPERATOR NOTES:</div>
                <ul class="mil-instruction-list">
                    <li>The terminal reads user data from the URL hash parameter (#user=NAME)</li>
                    <li>Data is rendered directly into the DOM without sanitization</li>
                    <li>Craft a payload that executes JavaScript when the page loads</li>
                    <li>Your payload must call: <code>fetch('challenge.php?page=lvl3&action=verify')</code></li>
                    <li>Then redirect to: <code>/public/attacker.php?level=3&cookie=DOCUMENT_COOKIE</code></li>
                </ul>
                <div class="mil-example-url">
                    <span class="mil-label">EXAMPLE URL:</span>
                    <code id="exampleUrl">.../challenge.php?page=lvl3#user=TESTUSER</code>
                </div>
            </div>
        </div>
    </div>

    <!-- Hint Section -->
    <?php if ($challenge): ?>
    <div class="mil-hint-panel" id="hintPanel" style="display: none;">
        <div class="mil-hint-title">INTELLIGENCE BRIEF:</div>
        <div class="mil-hint-content"><?php echo htmlspecialchars($challenge->getHint()); ?></div>
    </div>
    <button class="mil-btn mil-btn-secondary" onclick="toggleHint()">REQUEST INTELLIGENCE</button>
    <?php endif; ?>

    <!-- Success Message -->
    <div class="mil-success-message" id="successMessage" style="display: none;">
        <div class="mil-success-icon">✓</div>
        <div class="mil-success-text">
            <div>PAYLOAD EXECUTED SUCCESSFULLY</div>
            <div class="mil-success-sub">Session cookie exfiltrated. Challenge complete.</div>
        </div>
    </div>
</div>

<script>
// VULNERABLE CODE - DO NOT SANITIZE
// This is intentionally insecure to demonstrate DOM XSS

(function() {
    const logContainer = document.getElementById('systemLog');
    const userDisplay = document.getElementById('userDisplay');
    const successMessage = document.getElementById('successMessage');

    function addLog(type, message) {
        const time = new Date().toLocaleTimeString('en-US', { hour12: false });
        const entry = document.createElement('div');
        entry.className = 'mil-log-entry';
        entry.innerHTML = `<span class="mil-log-time">[${type}]</span><span class="mil-log-msg">${message}</span>`;
        logContainer.appendChild(entry);
        logContainer.scrollTop = logContainer.scrollHeight;
    }

    // Get the hash value from URL
    const hash = window.location.hash.substring(1); // Remove the # symbol
    
    if (hash) {
        addLog('RECV', `Data fragment received: ${hash.substring(0, 50)}...`);
        
        // Parse the hash parameter (format: user=VALUE)
        const params = new URLSearchParams(hash);
        const userData = params.get('user') || 'UNKNOWN';
        
        addLog('PROC', `Processing user data: ${userData}`);
        
        // ⚠️ VULNERABLE: Writing user input directly to innerHTML
        // This allows script injection via the hash parameter
        userDisplay.innerHTML = `Welcome, Agent: <span class="mil-highlight">${userData}</span>`;
        
        addLog('REND', 'Data rendered to display (NO SANITIZATION)');
    } else {
        addLog('IDLE', 'No data fragment detected in URL hash');
        userDisplay.innerHTML = '<span class="mil-muted">Awaiting data stream... Append #user=NAME to URL</span>';
    }

    // Check if payload already executed (for after redirect)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('status') === 'complete') {
        successMessage.style.display = 'flex';
        addLog('SUCCESS', 'Challenge verification confirmed');
    }
})();

function toggleHint() {
    const hintPanel = document.getElementById('hintPanel');
    hintPanel.style.display = hintPanel.style.display === 'none' ? 'block' : 'none';
}
</script>

<style>
.mil-mission-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
}

.mil-mission-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid #00ff41;
    padding-bottom: 15px;
    margin-bottom: 20px;
}

.mil-mission-title {
    font-family: 'Courier New', monospace;
    font-size: 1.5em;
    color: #00ff41;
    text-shadow: 0 0 10px rgba(0, 255, 65, 0.5);
}

.mil-icon {
    margin-right: 10px;
}

.mil-mission-meta {
    display: flex;
    align-items: center;
    gap: 15px;
}

.mil-clearance-badge {
    background: #1a1a2e;
    border: 1px solid #00ff41;
    color: #00ff41;
    padding: 5px 15px;
    font-family: 'Courier New', monospace;
    font-weight: bold;
    font-size: 0.9em;
}

.clearance-3 {
    border-color: #ff9500;
    color: #ff9500;
    text-shadow: 0 0 5px rgba(255, 149, 0, 0.5);
}

.mil-status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #333;
    animation: pulse 2s infinite;
}

.mil-status-indicator.active {
    background: #00ff41;
    box-shadow: 0 0 10px #00ff41;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.mil-mission-brief {
    background: rgba(10, 20, 30, 0.8);
    border: 1px solid #333;
    padding: 15px;
    margin-bottom: 20px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 10px;
}

.mil-brief-section {
    display: flex;
    flex-direction: column;
}

.mil-label {
    font-family: 'Courier New', monospace;
    font-size: 0.8em;
    color: #666;
    margin-bottom: 5px;
}

.mil-value {
    font-family: 'Courier New', monospace;
    color: #00ff41;
    font-size: 0.95em;
}

.mil-terminal-panel {
    background: #0a0f14;
    border: 1px solid #333;
    border-radius: 5px;
    overflow: hidden;
    margin-bottom: 20px;
}

.mil-terminal-header {
    background: #1a1a2e;
    padding: 10px 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #333;
}

.mil-terminal-title {
    font-family: 'Courier New', monospace;
    color: #00ff41;
    font-size: 0.9em;
}

.mil-terminal-controls {
    display: flex;
    gap: 8px;
}

.mil-control-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #333;
}

.mil-terminal-body {
    padding: 20px;
}

.mil-data-display {
    background: #000;
    border: 1px solid #00ff41;
    padding: 15px;
    margin-bottom: 20px;
    min-height: 60px;
}

.mil-data-label {
    font-family: 'Courier New', monospace;
    font-size: 0.8em;
    color: #666;
    margin-bottom: 10px;
}

.mil-user-display {
    font-family: 'Courier New', monospace;
    color: #fff;
    font-size: 1.1em;
}

.mil-highlight {
    color: #00ff41;
    font-weight: bold;
}

.mil-muted {
    color: #666;
    font-style: italic;
}

.mil-system-log {
    background: #000;
    border: 1px solid #333;
    height: 200px;
    overflow-y: auto;
    padding: 10px;
    font-family: 'Courier New', monospace;
    font-size: 0.85em;
    margin-bottom: 20px;
}

.mil-log-entry {
    margin-bottom: 5px;
    display: flex;
    gap: 10px;
}

.mil-log-time {
    color: #666;
}

.mil-log-msg {
    color: #00ff41;
}

.mil-instructions {
    background: rgba(10, 20, 30, 0.8);
    border: 1px solid #333;
    padding: 15px;
}

.mil-instruction-title {
    font-family: 'Courier New', monospace;
    color: #ff9500;
    margin-bottom: 10px;
    font-weight: bold;
}

.mil-instruction-list {
    list-style: none;
    padding: 0;
    margin: 0 0 15px 0;
}

.mil-instruction-list li {
    font-family: 'Courier New', monospace;
    color: #ccc;
    margin-bottom: 8px;
    padding-left: 15px;
    position: relative;
}

.mil-instruction-list li:before {
    content: '►';
    position: absolute;
    left: 0;
    color: #00ff41;
    font-size: 0.8em;
}

.mil-instruction-list code {
    background: #1a1a2e;
    padding: 2px 6px;
    border-radius: 3px;
    color: #ff9500;
}

.mil-example-url {
    background: #1a1a2e;
    padding: 10px;
    border-left: 3px solid #ff9500;
}

.mil-example-url code {
    display: block;
    margin-top: 5px;
    word-break: break-all;
    color: #fff;
}

.mil-hint-panel {
    background: rgba(255, 149, 0, 0.1);
    border: 1px solid #ff9500;
    padding: 15px;
    margin: 20px 0;
}

.mil-hint-title {
    font-family: 'Courier New', monospace;
    color: #ff9500;
    margin-bottom: 10px;
    font-weight: bold;
}

.mil-hint-content {
    font-family: 'Courier New', monospace;
    color: #ccc;
    line-height: 1.6;
}

.mil-btn {
    padding: 10px 20px;
    font-family: 'Courier New', monospace;
    border: none;
    cursor: pointer;
    transition: all 0.3s;
}

.mil-btn-secondary {
    background: transparent;
    border: 1px solid #ff9500;
    color: #ff9500;
}

.mil-btn-secondary:hover {
    background: rgba(255, 149, 0, 0.1);
    box-shadow: 0 0 10px rgba(255, 149, 0, 0.3);
}

.mil-success-message {
    display: flex;
    align-items: center;
    gap: 15px;
    background: rgba(0, 255, 65, 0.1);
    border: 1px solid #00ff41;
    padding: 20px;
    margin-top: 20px;
}

.mil-success-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #00ff41;
    color: #000;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5em;
    font-weight: bold;
}

.mil-success-text {
    font-family: 'Courier New', monospace;
    color: #00ff41;
}

.mil-success-sub {
    font-size: 0.85em;
    color: #666;
    margin-top: 5px;
}

/* Scrollbar styling */
.mil-system-log::-webkit-scrollbar {
    width: 8px;
}

.mil-system-log::-webkit-scrollbar-track {
    background: #0a0f14;
}

.mil-system-log::-webkit-scrollbar-thumb {
    background: #333;
    border-radius: 4px;
}

.mil-system-log::-webkit-scrollbar-thumb:hover {
    background: #00ff41;
}
</style>
