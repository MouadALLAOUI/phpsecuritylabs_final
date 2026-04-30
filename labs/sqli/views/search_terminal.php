<?php
/**
 * SQL Injection Level 2 - Search Terminal View
 * 
 * VULNERABLE: This page passes user input directly to SQL query without sanitization
 * Allows UNION-based injection to extract data from secrets table
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
            <span>MISSION: DATA EXTRACTION</span>
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
            <span class="mil-value">Extract classified secrets using UNION injection</span>
        </div>
        <div class="mil-brief-section">
            <span class="mil-label">TARGET:</span>
            <span class="mil-value">Secrets Table - secret_key values</span>
        </div>
        <div class="mil-brief-section">
            <span class="mil-label">VULNERABILITY:</span>
            <span class="mil-value">UNION-based SQL Injection</span>
        </div>
        <div class="mil-brief-section">
            <span class="mil-label">SUCCESS CRITERIA:</span>
            <span class="mil-value mil-success">Extract 5+ secret_key values</span>
        </div>
    </div>

    <!-- Search Form -->
    <div class="mil-terminal-panel">
        <div class="mil-panel-header">
            <span class="mil-panel-icon">▣</span>
            <span>AGENT DATABASE SEARCH TERMINAL</span>
        </div>
        
        <form method="POST" action="" class="mil-form">
            <div class="mil-form-group">
                <label for="search" class="mil-label">SEARCH QUERY:</label>
                <input 
                    type="text" 
                    id="search" 
                    name="search" 
                    class="mil-input mil-input-large"
                    placeholder="Enter agent codename or real name..."
                    value="<?php echo htmlspecialchars($_POST['search'] ?? ''); ?>"
                    autocomplete="off"
                >
            </div>
            
            <button type="submit" class="mil-btn mil-btn-primary mil-btn-full">
                <span class="mil-btn-icon">⬤</span>
                EXECUTE SEARCH
            </button>
        </form>
    </div>

    <!-- Query Log Panel -->
    <?php if ($query_log): ?>
    <div class="mil-terminal-panel mil-panel-danger">
        <div class="mil-panel-header">
            <span class="mil-panel-icon">⚠</span>
            <span>QUERY LOG - VULNERABLE STATEMENT</span>
        </div>
        <div class="mil-query-display">
            <code><?php echo htmlspecialchars($query_log); ?></code>
        </div>
        <div class="mil-warning-text">
            ⚠️ WARNING: This query is vulnerable to SQL injection. User input is directly concatenated.
        </div>
    </div>
    <?php endif; ?>

    <!-- Results Panel -->
    <?php if ($result): ?>
    <div class="mil-terminal-panel <?php echo $result['success'] ? 'mil-panel-success' : 'mil-panel-warning'; ?>">
        <div class="mil-panel-header">
            <span class="mil-panel-icon"><?php echo $result['success'] ? '✓' : '✗'; ?></span>
            <span>SEARCH RESULTS</span>
        </div>
        
        <div class="mil-message-box">
            <?php echo $result['message']; ?>
        </div>

        <?php if ($result['success'] && !empty($result['results'])): ?>
        <div class="mil-results-table-container">
            <table class="mil-results-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>CODENAME</th>
                        <th>REAL NAME</th>
                        <th>CLEARANCE</th>
                        <th>STATUS</th>
                        <?php if (isset($result['results'][0]['secret_key'])): ?>
                        <th class="mil-danger">SECRET_KEY</th>
                        <th class="mil-danger">DESCRIPTION</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result['results'] as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td class="mil-code"><?php echo htmlspecialchars($row['codename']); ?></td>
                        <td><?php echo htmlspecialchars($row['real_name'] ?? 'N/A'); ?></td>
                        <td>
                            <span class="mil-clearance-level level-<?php echo min(5, $row['clearance_level'] ?? 1); ?>">
                                L<?php echo $row['clearance_level'] ?? '?'; ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($row['status'] ?? 'unknown'); ?></td>
                        <?php if (isset($row['secret_key'])): ?>
                        <td class="mil-danger mil-code"><?php echo htmlspecialchars($row['secret_key']); ?></td>
                        <td class="mil-danger"><?php echo htmlspecialchars($row['description'] ?? ''); ?></td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (!empty($result['secrets_found'])): ?>
        <div class="mil-alert mil-alert-critical">
            <span class="mil-alert-icon">☢</span>
            <strong>CRITICAL SECURITY BREACH:</strong> 
            <?php echo count($result['secrets_found']); ?> classified secrets exposed via UNION injection!
        </div>
        <?php endif; ?>
        
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
            <p><strong>ANALYSIS:</strong> The search function queries the agents table but may be vulnerable to UNION-based injection.</p>
            <p><strong>METHODOLOGY:</strong></p>
            <ol class="mil-hint-steps">
                <li>Determine column count using <code>' ORDER BY 1--</code>, increment until error</li>
                <li>Use <code>UNION SELECT NULL,NULL,...</code> matching the column count</li>
                <li>Replace NULLs with actual column names from the secrets table</li>
                <li>Target columns: <code>id, secret_key, description, classification_level, source</code></li>
            </ol>
            <p class="mil-hint-final"><strong>PAYLOAD EXAMPLE:</strong><br>
            <code>' UNION SELECT id,secret_key,description,classification_level,source FROM secrets --</code></p>
        </div>
    </div>

    <!-- System Logs -->
    <div class="mil-system-log">
        <div class="mil-log-entry"><span class="mil-log-time">[SYSTEM]</span> Database search terminal initialized</div>
        <div class="mil-log-entry"><span class="mil-log-time">[SECURITY]</span> Query logging enabled for audit</div>
        <div class="mil-log-entry"><span class="mil-log-time">[WARN]</span> Input validation: DISABLED</div>
        <div class="mil-log-entry"><span class="mil-log-time">[WARN]</span> Prepared statements: NOT USED</div>
        <?php if ($result): ?>
        <div class="mil-log-entry <?php echo $result['success'] ? 'mil-log-success' : 'mil-log-error'; ?>">
            <span class="mil-log-time">[RESULT]</span> <?php echo $result['message']; ?>
        </div>
        <?php if (!empty($result['secrets_found'])): ?>
        <div class="mil-log-entry mil-log-critical">
            <span class="mil-log-time">[BREACH]</span> Secrets table data extracted via UNION injection
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<style>
.mil-input-large {
    font-size: 1.1rem;
    padding: 14px 16px;
}

.mil-results-table-container {
    overflow-x: auto;
    margin-top: 1rem;
}

.mil-results-table {
    width: 100%;
    border-collapse: collapse;
    font-family: 'Courier New', monospace;
    font-size: 0.85rem;
}

.mil-results-table th,
.mil-results-table td {
    padding: 10px 12px;
    text-align: left;
    border-bottom: 1px solid rgba(0, 255, 136, 0.1);
}

.mil-results-table th {
    background: rgba(0, 255, 136, 0.05);
    color: #00ff88;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}

.mil-results-table tr:hover {
    background: rgba(0, 255, 136, 0.03);
}

.mil-clearance-level {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 3px;
    font-weight: bold;
    font-size: 0.75rem;
}

.level-1 { background: #28a745; color: #fff; }
.level-2 { background: #17a2b8; color: #fff; }
.level-3 { background: #ffc107; color: #000; }
.level-4 { background: #fd7e14; color: #fff; }
.level-5 { background: #dc3545; color: #fff; animation: pulse-red 2s infinite; }

@keyframes pulse-red {
    0%, 100% { box-shadow: 0 0 5px #dc3545; }
    50% { box-shadow: 0 0 15px #dc3545; }
}

.mil-danger {
    color: #ff4444 !important;
    font-weight: bold;
}

.mil-alert-critical {
    background: rgba(255, 68, 68, 0.15);
    border: 2px solid #ff4444;
    color: #ff6666;
    padding: 1rem;
    margin-top: 1rem;
    animation: flash-alert 1.5s infinite;
}

@keyframes flash-alert {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}
</style>
