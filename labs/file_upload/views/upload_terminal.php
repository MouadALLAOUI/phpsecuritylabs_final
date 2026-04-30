<?php
/**
 * File Upload Level 1 - Upload Terminal View
 * 
 * VULNERABLE: Only checks file extension, not content type or magic bytes
 */

// Get challenge instance
$challenge = null;
if (isset($this)) {
    $challenge = $this;
}

// Handle form submission
$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $challenge) {
    $result = $challenge->handle($_POST);
}
?>
<div class="mil-mission-container">
    <!-- Mission Header -->
    <div class="mil-mission-header">
        <div class="mil-mission-title">
            <span class="mil-icon">◈</span>
            <span>MISSION: PAYLOAD DELIVERY</span>
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
            <span class="mil-value">Upload executable payload disguised as image</span>
        </div>
        <div class="mil-brief-section">
            <span class="mil-label">TARGET:</span>
            <span class="mil-value">Secure File Transfer System</span>
        </div>
        <div class="mil-brief-section">
            <span class="mil-label">VULNERABILITY:</span>
            <span class="mil-value">Extension-only validation (no content check)</span>
        </div>
        <div class="mil-brief-section">
            <span class="mil-label">GOAL:</span>
            <span class="mil-value">Bypass filter to upload web shell (.php as .jpg)</span>
        </div>
    </div>

    <!-- Upload Terminal -->
    <div class="mil-terminal-panel">
        <div class="mil-terminal-header">
            <span class="mil-terminal-title">SECURE FILE TRANSFER PROTOCOL</span>
            <div class="mil-terminal-controls">
                <span class="mil-control-dot"></span>
                <span class="mil-control-dot"></span>
                <span class="mil-control-dot"></span>
            </div>
        </div>
        
        <div class="mil-terminal-body">
            <!-- Upload Form -->
            <form method="POST" enctype="multipart/form-data" class="mil-upload-form">
                <div class="mil-file-input-wrapper">
                    <label class="mil-file-label">
                        <input type="file" name="upload" class="mil-file-input" accept="image/*">
                        <span class="mil-file-placeholder">SELECT INTELLIGENCE FILE...</span>
                    </label>
                    <div class="mil-file-info" id="fileInfo"></div>
                </div>
                
                <div class="mil-upload-rules">
                    <div class="mil-rule-title">ACCEPTED FORMATS:</div>
                    <ul class="mil-rule-list">
                        <li><span class="mil-format-badge">JPG</span></li>
                        <li><span class="mil-format-badge">JPEG</span></li>
                        <li><span class="mil-format-badge">PNG</span></li>
                        <li><span class="mil-format-badge">GIF</span></li>
                    </ul>
                    <div class="mil-rule-warning">
                        <span class="mil-warn-icon">⚠</span> Maximum size: 5MB
                    </div>
                </div>
                
                <button type="submit" class="mil-btn mil-btn-primary">
                    <span class="mil-btn-icon">▲</span> INITIATE TRANSFER
                </button>
            </form>

            <!-- Scan Log -->
            <?php if ($result && isset($result['scan_log'])): ?>
            <div class="mil-scan-log">
                <div class="mil-log-header">
                    <span class="mil-log-title">TRANSFER LOG:</span>
                    <span class="mil-log-status <?php echo $result['success'] ? 'status-success' : 'status-error'; ?>">
                        <?php echo $result['success'] ? 'COMPLETE' : 'FAILED'; ?>
                    </span>
                </div>
                <div class="mil-log-entries" id="scanLog">
                    <?php foreach ($result['scan_log'] as $logEntry): ?>
                    <div class="mil-log-entry">
                        <span class="mil-log-time"><?php echo date('H:i:s'); ?></span>
                        <span class="mil-log-msg"><?php echo htmlspecialchars($logEntry); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Result Display -->
            <?php if ($result): ?>
            <div class="mil-result-panel <?php echo $result['success'] ? 'mil-result-success' : 'mil-result-error'; ?>">
                <div class="mil-result-header">
                    <span class="mil-result-icon"><?php echo $result['success'] ? '✓' : '✗'; ?></span>
                    <span class="mil-result-title"><?php echo htmlspecialchars($result['message']); ?></span>
                </div>
                
                <?php if ($result['success'] && isset($result['filename'])): ?>
                <div class="mil-file-details">
                    <div class="mil-detail-row">
                        <span class="mil-detail-label">FILENAME:</span>
                        <span class="mil-detail-value"><?php echo htmlspecialchars($result['filename']); ?></span>
                    </div>
                    <?php if (isset($result['filepath'])): ?>
                    <div class="mil-detail-row">
                        <span class="mil-detail-label">PATH:</span>
                        <span class="mil-detail-value"><?php echo htmlspecialchars($result['filepath']); ?></span>
                    </div>
                    <div class="mil-detail-action">
                        <a href="<?php echo htmlspecialchars($result['filepath']); ?>" target="_blank" class="mil-link">
                            <span class="mil-link-icon">↗</span> VIEW UPLOADED FILE
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- System Status -->
            <div class="mil-system-log" id="systemLog">
                <div class="mil-log-entry">
                    <span class="mil-log-time">[SYSTEM]</span>
                    <span class="mil-log-msg">File transfer protocol initialized...</span>
                </div>
                <div class="mil-log-entry">
                    <span class="mil-log-time">[SYSTEM]</span>
                    <span class="mil-log-msg">Security scan engine: ACTIVE</span>
                </div>
                <div class="mil-log-entry">
                    <span class="mil-log-time">[WARN]</span>
                    <span class="mil-log-msg">SECURITY NOTICE: Deep packet inspection DISABLED</span>
                </div>
                <div class="mil-log-entry">
                    <span class="mil-log-time">[WARN]</span>
                    <span class="mil-log-msg">MIME type verification: OFFLINE</span>
                </div>
            </div>

            <!-- Instructions -->
            <div class="mil-instructions">
                <div class="mil-instruction-title">OPERATOR NOTES:</div>
                <ul class="mil-instruction-list">
                    <li>The system validates uploaded files by extension only</li>
                    <li>Content-type headers and file signatures are NOT checked</li>
                    <li>Create a PHP web shell and rename it with an allowed extension</li>
                    <li>Example payload: <code>&lt;?php system($_GET['cmd']); ?&gt;</code></li>
                    <li>Save as <code>shell.jpg</code> and upload to bypass the filter</li>
                </ul>
                <div class="mil-hint-box">
                    <span class="mil-label">INTELLIGENCE:</span>
                    <p>The upload system trusts file extensions without verifying actual content. A PHP file renamed to .jpg will pass validation but still execute as PHP when accessed.</p>
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

// File input handler
const fileInput = document.querySelector('.mil-file-input');
const fileInfo = document.getElementById('fileInfo');

if (fileInput) {
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const size = (file.size / 1024).toFixed(2);
            fileInfo.innerHTML = `
                <span class="mil-file-selected">
                    <span class="mil-file-name">${file.name}</span>
                    <span class="mil-file-size">(${size} KB)</span>
                </span>
            `;
        } else {
            fileInfo.innerHTML = '';
        }
    });
}

// Auto-scroll log
const logContainer = document.getElementById('scanLog');
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

.mil-upload-form {
    margin-bottom: 20px;
}

.mil-file-input-wrapper {
    margin-bottom: 20px;
}

.mil-file-label {
    display: block;
    padding: 40px 20px;
    border: 2px dashed #333;
    background: rgba(0, 0, 0, 0.3);
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
}

.mil-file-label:hover {
    border-color: #00ff41;
    background: rgba(0, 255, 65, 0.05);
}

.mil-file-input {
    display: none;
}

.mil-file-placeholder {
    font-family: 'Courier New', monospace;
    color: #666;
    font-size: 1.1em;
}

.mil-file-info {
    margin-top: 10px;
    text-align: center;
}

.mil-file-selected {
    font-family: 'Courier New', monospace;
    color: #00ff41;
}

.mil-file-name {
    font-weight: bold;
}

.mil-file-size {
    color: #666;
    margin-left: 10px;
}

.mil-upload-rules {
    background: rgba(0, 0, 0, 0.3);
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid #333;
}

.mil-rule-title {
    font-family: 'Courier New', monospace;
    color: #666;
    font-size: 0.85em;
    margin-bottom: 10px;
}

.mil-rule-list {
    list-style: none;
    padding: 0;
    margin: 0 0 10px 0;
    display: flex;
    gap: 10px;
}

.mil-format-badge {
    background: #1a1a2e;
    border: 1px solid #00ff41;
    color: #00ff41;
    padding: 5px 12px;
    font-family: 'Courier New', monospace;
    font-size: 0.85em;
    border-radius: 3px;
}

.mil-rule-warning {
    font-family: 'Courier New', monospace;
    color: #ff9500;
    font-size: 0.85em;
}

.mil-warn-icon {
    margin-right: 5px;
}

.mil-btn {
    padding: 12px 25px;
    font-family: 'Courier New', monospace;
    border: none;
    cursor: pointer;
    transition: all 0.3s;
    font-weight: bold;
    width: 100%;
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
    width: auto;
}

.mil-btn-secondary:hover {
    background: rgba(255, 149, 0, 0.1);
    box-shadow: 0 0 10px rgba(255, 149, 0, 0.3);
}

.mil-scan-log {
    background: #000;
    border: 1px solid #333;
    margin: 20px 0;
}

.mil-log-header {
    background: #1a1a2e;
    padding: 10px 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #333;
}

.mil-log-title {
    font-family: 'Courier New', monospace;
    color: #00ff41;
    font-size: 0.9em;
}

.mil-log-status {
    font-family: 'Courier New', monospace;
    font-size: 0.85em;
    padding: 3px 10px;
    border-radius: 3px;
}

.status-success {
    background: rgba(0, 255, 65, 0.2);
    color: #00ff41;
    border: 1px solid #00ff41;
}

.status-error {
    background: rgba(255, 0, 0, 0.2);
    color: #ff0000;
    border: 1px solid #ff0000;
}

.mil-log-entries {
    max-height: 200px;
    overflow-y: auto;
    padding: 10px;
}

.mil-log-entry {
    font-family: 'Courier New', monospace;
    font-size: 0.85em;
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

.mil-file-details {
    background: rgba(0, 0, 0, 0.3);
    padding: 15px;
    border-left: 3px solid #00ff41;
}

.mil-detail-row {
    display: flex;
    margin-bottom: 10px;
    font-family: 'Courier New', monospace;
}

.mil-detail-row:last-child {
    margin-bottom: 10px;
}

.mil-detail-label {
    color: #666;
    width: 100px;
    flex-shrink: 0;
}

.mil-detail-value {
    color: #00ff41;
    word-break: break-all;
}

.mil-detail-action {
    margin-top: 10px;
}

.mil-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #ff9500;
    text-decoration: none;
    font-family: 'Courier New', monospace;
    font-size: 0.9em;
    padding: 8px 15px;
    border: 1px solid #ff9500;
    border-radius: 3px;
    transition: all 0.3s;
}

.mil-link:hover {
    background: rgba(255, 149, 0, 0.1);
    box-shadow: 0 0 10px rgba(255, 149, 0, 0.3);
}

.mil-link-icon {
    font-size: 1.2em;
}

.mil-system-log {
    background: #000;
    border: 1px solid #333;
    height: 120px;
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
.mil-log-entries::-webkit-scrollbar,
.mil-system-log::-webkit-scrollbar {
    width: 8px;
}

.mil-log-entries::-webkit-scrollbar-track,
.mil-system-log::-webkit-scrollbar-track {
    background: #0a0f14;
}

.mil-log-entries::-webkit-scrollbar-thumb,
.mil-system-log::-webkit-scrollbar-thumb {
    background: #333;
    border-radius: 4px;
}

.mil-log-entries::-webkit-scrollbar-thumb:hover,
.mil-system-log::-webkit-scrollbar-thumb:hover {
    background: #00ff41;
}
</style>
