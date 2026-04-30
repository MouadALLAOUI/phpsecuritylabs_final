<?php
/**
 * SQL Injection Level 1 - Auth Terminal View
 * 
 * VULNERABLE: This page passes user input directly to SQL query without sanitization
 */

// Get challenge instance
$challenge = null;
if (isset($this)) {
    $challenge = $this;
}

// Handle form submission
$result = null;
$query_log = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $challenge) {
    $result = $challenge->handle($_POST);
    if (isset($result['query'])) {
        $query_log = $result['query'];
    }
}
?>
<div class="mil-mission-container">
    <!-- Mission Header -->
    <div class="mil-mission-header">
        <div class="mil-mission-title">
            <span class="mil-icon">◈</span>
            <span>MISSION: DATABASE BREACH</span>
        </div>
        <div class="mil-mission-meta">
            <div class="mil-clearance-badge clearance-2">LEVEL 1</div>
            <div class="mil-status-indicator active"></div>
        </div>
    </div>

    <!-- Mission Objective -->
    <div class="mil-mission-brief">
        <div class="mil-brief-section">
            <span class="mil-label">OBJECTIVE:</span>
            <span class="mil-value">Bypass authentication to access admin records</span>
        </div>
        <div class="mil-brief-section">
            <span class="mil-label">TARGET:</span>
            <span class="mil-value">Personnel Database - Agents Table</span>
        </div>
        <div class="mil-brief-section">
            <span class="mil-label">VULNERABILITY:</span>
            <span class="mil-value">SQL Injection (unsanitized WHERE clause)</span>
        </div>
        <div class="mil-brief-section">
            <span class="mil-label">GOAL:</span>
            <span class="mil-value">Extract agent with clearance level 5 or higher</span>
        </div>
    </div>

    <!-- Login Terminal -->
    <div class="mil-terminal-panel">
        <div class="mil-terminal-header">
            <span class="mil-terminal-title">SECURE AUTHENTICATION GATEWAY</span>
            <div class="mil-terminal-controls">
                <span class="mil-control-dot"></span>
                <span class="mil-control-dot"></span>
                <span class="mil-control-dot"></span>
            </div>
        </div>
        
        <div class="mil-terminal-body">
            <!-- Login Form -->
            <form method="POST" class="mil-auth-form">
                <div class="mil-form-group">
                    <label class="mil-form-label">AGENT ID / USERNAME</label>
                    <input type="text" name="username" class="mil-form-input" placeholder="Enter agent identifier..." autocomplete="off">
                </div>
                
                <div class="mil-form-group">
                    <label class="mil-form-label">ACCESS CODE / PASSWORD</label>
                    <input type="password" name="password" class="mil-form-input" placeholder="Enter access code..." autocomplete="off">
                </div>
                
                <button type="submit" class="mil-btn mil-btn-primary">
                    <span class="mil-btn-icon">⬤</span> AUTHENTICATE
                </button>
            </form>

            <!-- Query Log (shows the actual SQL query executed) -->
            <?php if ($query_log): ?>
            <div class="mil-query-log">
                <div class="mil-query-label">EXECUTED QUERY:</div>
                <code class="mil-query-code"><?php echo htmlspecialchars($query_log); ?></code>
            </div>
            <?php endif; ?>

            <!-- Result Display -->
            <?php if ($result): ?>
            <div class="mil-result-panel <?php echo $result['success'] ? 'mil-result-success' : 'mil-result-error'; ?>">
                <div class="mil-result-header">
                    <span class="mil-result-icon"><?php echo $result['success'] ? '✓' : '✗'; ?></span>
                    <span class="mil-result-title"><?php echo htmlspecialchars($result['message']); ?></span>
                </div>
                
                <?php if ($result['success'] && isset($result['result'])): ?>
                <div class="mil-agent-record">
                    <div class="mil-record-field">
                        <span class="mil-field-label">AGENT ID:</span>
                        <span class="mil-field-value"><?php echo htmlspecialchars($result['result']['id']); ?></span>
                    </div>
                    <div class="mil-record-field">
                        <span class="mil-field-label">USERNAME:</span>
                        <span class="mil-field-value"><?php echo htmlspecialchars($result['result']['username']); ?></span>
                    </div>
                    <div class="mil-record-field">
                        <span class="mil-field-label">CLEARANCE:</span>
                        <span class="mil-field-value clearance-<?php echo min($result['result']['clearance'], 5); ?>">
                            LEVEL <?php echo htmlspecialchars($result['result']['clearance']); ?>
                        </span>
                    </div>
                    <div class="mil-record-field">
                        <span class="mil-field-label">DEPARTMENT:</span>
                        <span class="mil-field-value"><?php echo htmlspecialchars($result['result']['department']); ?></span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- System Log -->
            <div class="mil-system-log" id="systemLog">
                <div class="mil-log-entry">
                    <span class="mil-log-time">[SYSTEM]</span>
                    <span class="mil-log-msg">Database connection established...</span>
                </div>
                <div class="mil-log-entry">
                    <span class="mil-log-time">[SYSTEM]</span>
                    <span class="mil-log-msg">Agents table loaded (classified records)</span>
                </div>
                <div class="mil-log-entry">
                    <span class="mil-log-time">[WARN]</span>
                    <span class="mil-log-msg">SECURITY ALERT: Input validation disabled on authentication gateway</span>
                </div>
            </div>

            <!-- Instructions -->
            <div class="mil-instructions">
                <div class="mil-instruction-title">OPERATOR NOTES:</div>
                <ul class="mil-instruction-list">
                    <li>The authentication system queries the agents database table</li>
                    <li>User input is directly concatenated into the SQL query</li>
                    <li>Craft a payload to bypass authentication without valid credentials</li>
                    <li>Classic payload: <code>' OR '1'='1' --</code></li>
                    <li>Advanced: Use UNION to extract data from other tables</li>
                </ul>
                <div class="mil-hint-box">
                    <span class="mil-label">INTELLIGENCE:</span>
                    <p>The database contains agents with clearance levels 1-5. You need to access an account with level 5 clearance to complete the mission.</p>
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
</div>

<script>
function toggleHint() {
    const hintPanel = document.getElementById('hintPanel');
    hintPanel.style.display = hintPanel.style.display === 'none' ? 'block' : 'none';
}

// Auto-scroll log
const logContainer = document.getElementById('systemLog');
if (logContainer) {
    logContainer.scrollTop = logContainer.scrollHeight;
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

.clearance-2 {
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

.mil-auth-form {
    margin-bottom: 20px;
}

.mil-form-group {
    margin-bottom: 15px;
}

.mil-form-label {
    display: block;
    font-family: 'Courier New', monospace;
    font-size: 0.85em;
    color: #666;
    margin-bottom: 8px;
}

.mil-form-input {
    width: 100%;
    padding: 12px 15px;
    background: #000;
    border: 1px solid #333;
    color: #00ff41;
    font-family: 'Courier New', monospace;
    font-size: 1em;
    transition: all 0.3s;
}

.mil-form-input:focus {
    outline: none;
    border-color: #00ff41;
    box-shadow: 0 0 10px rgba(0, 255, 65, 0.3);
}

.mil-btn {
    padding: 12px 25px;
    font-family: 'Courier New', monospace;
    border: none;
    cursor: pointer;
    transition: all 0.3s;
    font-weight: bold;
}

.mil-btn-primary {
    background: #00ff41;
    color: #000;
    border: 1px solid #00ff41;
}

.mil-btn-primary:hover {
    background: #00cc33;
    box-shadow: 0 0 15px rgba(0, 255, 65, 0.5);
}

.mil-btn-icon {
    margin-right: 8px;
}

.mil-btn-secondary {
    background: transparent;
    border: 1px solid #ff9500;
    color: #ff9500;
    margin-top: 15px;
}

.mil-btn-secondary:hover {
    background: rgba(255, 149, 0, 0.1);
    box-shadow: 0 0 10px rgba(255, 149, 0, 0.3);
}

.mil-query-log {
    background: #000;
    border: 1px solid #ff9500;
    padding: 15px;
    margin: 20px 0;
}

.mil-query-label {
    font-family: 'Courier New', monospace;
    font-size: 0.8em;
    color: #ff9500;
    margin-bottom: 10px;
}

.mil-query-code {
    display: block;
    font-family: 'Courier New', monospace;
    color: #fff;
    word-break: break-all;
    line-height: 1.6;
}

.mil-result-panel {
    border: 1px solid;
    padding: 20px;
    margin: 20px 0;
}

.mil-result-success {
    background: rgba(0, 255, 65, 0.05);
    border-color: #00ff41;
}

.mil-result-error {
    background: rgba(255, 0, 0, 0.05);
    border-color: #ff0000;
}

.mil-result-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
}

.mil-result-icon {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.mil-result-success .mil-result-icon {
    background: #00ff41;
    color: #000;
}

.mil-result-error .mil-result-icon {
    background: #ff0000;
    color: #fff;
}

.mil-result-title {
    font-family: 'Courier New', monospace;
    font-size: 1.1em;
}

.mil-result-success .mil-result-title {
    color: #00ff41;
}

.mil-result-error .mil-result-title {
    color: #ff0000;
}

.mil-agent-record {
    background: rgba(0, 0, 0, 0.3);
    padding: 15px;
    border-left: 3px solid #00ff41;
}

.mil-record-field {
    display: flex;
    margin-bottom: 10px;
    font-family: 'Courier New', monospace;
}

.mil-record-field:last-child {
    margin-bottom: 0;
}

.mil-field-label {
    color: #666;
    width: 120px;
    flex-shrink: 0;
}

.mil-field-value {
    color: #00ff41;
}

.clearance-5 {
    color: #ff0000;
    font-weight: bold;
    text-shadow: 0 0 5px rgba(255, 0, 0, 0.5);
}

.mil-system-log {
    background: #000;
    border: 1px solid #333;
    height: 150px;
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

.mil-hint-box {
    background: rgba(255, 149, 0, 0.05);
    border-left: 3px solid #ff9500;
    padding: 10px 15px;
    margin-top: 15px;
}

.mil-hint-box p {
    font-family: 'Courier New', monospace;
    color: #ccc;
    margin: 0;
    line-height: 1.6;
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
