<?php
/**
 * File Upload Level 2 - MIME Type Bypass Terminal View
 * 
 * VULNERABLE: Only checks Content-Type header, not actual file content (magic bytes)
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
            <span>MISSION: MIME SPOOFING</span>
        </div>
        <div class="mil-mission-meta">
            <div class="mil-clearance-badge clearance-3">LEVEL 2</div>
            <div class="mil-status-indicator active"></div>
        </div>
    </div>

    <!-- Mission Objective -->
    <div class="mil-mission-brief">
        <div class="mil-brief-section">
            <span class="mil-label">OBJECTIVE:</span>
            <span class="mil-value">Bypass MIME type validation with spoofed headers</span>
        </div>
        <div class="mil-brief-section">
            <span class="mil-label">TARGET:</span>
            <span class="mil-value">Enhanced File Transfer System</span>
        </div>
        <div class="mil-brief-section">
            <span class="mil-label">VULNERABILITY:</span>
            <span class="mil-value">Content-Type header trust (no magic byte check)</span>
        </div>
        <div class="mil-brief-section">
            <span class="mil-label">GOAL:</span>
            <span class="mil-value">Upload PHP shell with image/jpg MIME type</span>
        </div>
    </div>

    <!-- Upload Form -->
    <div class="mil-terminal-panel">
        <div class="mil-panel-header">
            <span class="mil-panel-icon">▣</span>
            <span>SECURE FILE TRANSFER PROTOCOL v2.0</span>
        </div>
        
        <form method="POST" action="" enctype="multipart/form-data" class="mil-form">
            <div class="mil-form-group">
                <label for="upload" class="mil-label">SELECT INTELLIGENCE FILE:</label>
                <input 
                    type="file" 
                    id="upload" 
                    name="upload" 
                    class="mil-file-input"
                    accept="image/jpeg,image/png,image/gif"
                >
                <div class="mil-file-hint">
                    Accepted formats: JPEG, PNG, GIF | Maximum size: 5MB
                </div>
            </div>
            
            <div class="mil-form-group mil-advanced-info">
                <label class="mil-label">MIME TYPE VALIDATION:</label>
                <div class="mil-validation-status">
                    <span class="mil-status-item">
                        <span class="mil-status-dot"></span>
                        Content-Type Header Check: <strong>ACTIVE</strong>
                    </span>
                    <span class="mil-status-item mil-status-warning">
                        <span class="mil-status-dot"></span>
                        Magic Byte Verification: <strong>DISABLED</strong>
                    </span>
                    <span class="mil-status-item mil-status-warning">
                        <span class="mil-status-dot"></span>
                        Image Signature Check: <strong>SKIPPED</strong>
                    </span>
                </div>
            </div>
            
            <button type="submit" class="mil-btn mil-btn-primary mil-btn-full">
                <span class="mil-btn-icon">⬤</span>
                INITIATE SECURE TRANSFER
            </button>
        </form>
    </div>

    <!-- Scan Log Panel -->
    <?php if ($result && !empty($result['scan_log'])): ?>
    <div class="mil-terminal-panel <?php echo $result['success'] ? 'mil-panel-success' : 'mil-panel-warning'; ?>">
        <div class="mil-panel-header">
            <span class="mil-panel-icon"><?php echo $result['success'] ? '✓' : '⚠'; ?></span>
            <span>TRANSFER LOG</span>
        </div>
        
        <div class="mil-scan-log">
            <?php foreach ($result['scan_log'] as $logEntry): ?>
            <div class="mil-log-entry"><?php echo $logEntry; ?></div>
            <?php endforeach; ?>
        </div>
        
        <?php if (isset($result['mime_check']) || isset($result['extension_check'])): ?>
        <div class="mil-validation-results">
            <div class="mil-validation-row">
                <span>MIME Type Check:</span>
                <span class="mil-validation-<?php echo strtolower($result['mime_check'] ?? 'PENDING'); ?>">
                    <?php echo $result['mime_check'] ?? 'N/A'; ?>
                </span>
            </div>
            <div class="mil-validation-row">
                <span>Extension Check:</span>
                <span class="mil-validation-<?php echo strtolower($result['extension_check'] ?? 'PENDING'); ?>">
                    <?php echo $result['extension_check'] ?? 'N/A'; ?>
                </span>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="mil-message-box">
            <?php echo $result['message']; ?>
        </div>
        
        <?php if ($result['success'] && isset($result['filename'])): ?>
        <div class="mil-upload-success">
            <div class="mil-file-info">
                <span class="mil-label">UPLOADED FILE:</span>
                <span class="mil-code"><?php echo htmlspecialchars($result['filename']); ?></span>
            </div>
            <div class="mil-file-info">
                <span class="mil-label">STORAGE PATH:</span>
                <span class="mil-code"><?php echo htmlspecialchars($result['filepath']); ?></span>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Intelligence Brief (Hint) -->
    <div class="mil-terminal-panel mil-panel-info">
        <div class="mil-panel-header">
            <span class="mil-panel-icon">?</span>
            <span>INTELLIGENCE BRIEF</span>
        </div>
        <div class="mil-hint-content">
            <p><strong>ANALYSIS:</strong> The enhanced file transfer system validates the Content-Type HTTP header but does not verify actual file content.</p>
            <p><strong>ATTACK VECTOR:</strong> HTTP headers are client-controlled and can be easily modified.</p>
            <p><strong>METHODOLOGY:</strong></p>
            <ol class="mil-hint-steps">
                <li>Create a PHP web shell: <code>&lt;?php system($_GET['cmd']); ?&gt;</code></li>
                <li>Name it with an image extension: <code>shell.jpg</code></li>
                <li>Use browser DevTools Network tab or a proxy (Burp Suite) to intercept the upload</li>
                <li>Modify the <code>Content-Type</code> header from <code>application/x-php</code> to <code>image/jpeg</code></li>
                <li>Forward the request - the server trusts the spoofed MIME type</li>
            </ol>
            <p class="mil-hint-final"><strong>ALTERNATIVE:</strong> Some browsers allow changing file type filter to "All Files" which may send different MIME types.</p>
        </div>
    </div>

    <!-- System Logs -->
    <div class="mil-system-log">
        <div class="mil-log-entry"><span class="mil-log-time">[SYSTEM]</span> File transfer protocol v2.0 initialized</div>
        <div class="mil-log-entry"><span class="mil-log-time">[SECURITY]</span> MIME type validation: ENABLED</div>
        <div class="mil-log-entry"><span class="mil-log-time">[WARN]</span> Magic byte analysis: DISABLED</div>
        <div class="mil-log-entry"><span class="mil-log-time">[WARN]</span> Content inspection: NOT PERFORMED</div>
        <div class="mil-log-entry"><span class="mil-log-time">[INFO]</span> Trust model: Client-supplied headers</div>
        <?php if ($result): ?>
        <div class="mil-log-entry <?php echo $result['success'] ? 'mil-log-success' : 'mil-log-error'; ?>">
            <span class="mil-log-time">[RESULT]</span> <?php echo $result['message']; ?>
        </div>
        <?php if (strpos($result['message'], 'WARNING') !== false): ?>
        <div class="mil-log-entry mil-log-critical">
            <span class="mil-log-time">[BREACH]</span> MIME type spoofing detected - executable uploaded as image
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<style>
.mil-file-input {
    display: block;
    width: 100%;
    padding: 12px;
    background: rgba(0, 0, 0, 0.3);
    border: 1px dashed rgba(0, 255, 136, 0.3);
    color: #00ff88;
    font-family: 'Courier New', monospace;
    cursor: pointer;
}

.mil-file-input:hover {
    border-color: rgba(0, 255, 136, 0.6);
    background: rgba(0, 255, 136, 0.05);
}

.mil-file-hint {
    margin-top: 8px;
    font-size: 0.8rem;
    color: rgba(0, 255, 136, 0.6);
}

.mil-advanced-info {
    margin-top: 1rem;
    padding: 1rem;
    background: rgba(255, 193, 7, 0.05);
    border: 1px solid rgba(255, 193, 7, 0.2);
}

.mil-validation-status {
    display: flex;
    flex-direction: column;
    gap: 8px;
    font-size: 0.85rem;
}

.mil-status-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.mil-status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #00ff88;
}

.mil-status-warning .mil-status-dot {
    background: #ffc107;
}

.mil-validation-results {
    margin: 1rem 0;
    padding: 1rem;
    background: rgba(0, 0, 0, 0.2);
    border-radius: 4px;
}

.mil-validation-row {
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
    font-family: 'Courier New', monospace;
    font-size: 0.85rem;
}

.mil-validation-passed {
    color: #00ff88;
    font-weight: bold;
}

.mil-validation-failed {
    color: #ff4444;
    font-weight: bold;
}

.mil-validation-pending {
    color: #ffc107;
}

.mil-upload-success {
    margin-top: 1rem;
    padding: 1rem;
    background: rgba(0, 255, 136, 0.05);
    border: 1px solid rgba(0, 255, 136, 0.2);
}

.mil-file-info {
    display: block;
    margin-bottom: 8px;
    font-size: 0.85rem;
}

.mil-file-info:last-child {
    margin-bottom: 0;
}
</style>
