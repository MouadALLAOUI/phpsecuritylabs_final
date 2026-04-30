<?php
/**
 * FILE UPLOAD LAB - MIL-OPS INTERFACE
 * Mission: Intel Upload
 * Target: Secure File Transfer System
 */

namespace Labs\FileUpload;

use App\Core\BaseChallenge;
use App\Core\Session;

class IntelUpload extends BaseChallenge
{
    private string $uploadResult = '';
    private bool $uploadAttempted = false;
    private bool $solved = false;
    private array $fileLog = [];
    private array $uploadedFiles = [];

    public function handle(): void
    {
        // Check if already solved
        if (Session::get('file_upload_solved') === true) {
            $this->solved = true;
        }

        // Load existing uploaded files from session
        $this->uploadedFiles = Session::get('uploaded_files') ?? [];

        if ($this->isPost() && isset($_FILES['intel_file'])) {
            $this->uploadAttempted = true;
            $file = $_FILES['intel_file'];
            
            // Log the upload attempt
            $logEntry = [
                'timestamp' => date('H:i:s'),
                'filename' => $file['name'],
                'size' => $file['size'],
                'type' => $file['type'],
                'status' => 'scanning'
            ];

            // ⚠️ VULNERABILITY: Weak file validation
            // Only checking extension, not actual file content
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt'];
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            // Simulate scanning
            usleep(500000); // 0.5 second delay for effect

            // Check for PHP/shell extensions that should be blocked but aren't properly validated
            $dangerousExtensions = ['php', 'php3', 'php4', 'php5', 'phtml', 'exe', 'sh', 'bash'];
            
            if (in_array($extension, $allowedExtensions)) {
                // Even "allowed" extensions can be dangerous if they contain malicious content
                $uploadDir = ROOT . '/storage/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                // Generate unique filename but keep original extension
                $newFilename = uniqid('intel_') . '_' . $file['name'];
                $destination = $uploadDir . $newFilename;
                
                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $logEntry['status'] = 'accepted';
                    $this->uploadResult = "FILE ACCEPTED\n\n[SCAN COMPLETE]\nFile passed initial security checks.\nUploaded to secure storage.\n\n⚠️ WARNING: File content was NOT validated.\nOnly extension was checked.\n\n[TARGET VULNERABLE - CONTENT BYPASS POSSIBLE]";
                    
                    // Check if it's a PHP file disguised with allowed extension
                    $content = file_get_contents($destination);
                    if (strpos($content, '<?php') !== false || strpos($content, '<?') !== false) {
                        $this->uploadResult .= "\n\n[BACKDOOR DETECTED IN UPLOADED FILE]\nPHP code found in supposedly safe file!\n\n[MALICIOUS UPLOAD SUCCESSFUL]";
                        $this->solved = true;
                        Session::set('file_upload_solved', true);
                        $logEntry['status'] = 'backdoor_detected';
                    }
                    
                    $this->uploadedFiles[] = [
                        'name' => $file['name'],
                        'new_name' => $newFilename,
                        'size' => $file['size'],
                        'timestamp' => date('H:i:s')
                    ];
                    Session::set('uploaded_files', $this->uploadedFiles);
                } else {
                    $logEntry['status'] = 'failed';
                    $this->uploadResult = "UPLOAD FAILED\n\nUnable to save file to secure storage.\nContact system administrator.";
                }
            } elseif (in_array($extension, $dangerousExtensions)) {
                $logEntry['status'] = 'blocked_dangerous';
                $this->uploadResult = "⛔ FILE BLOCKED ⛔\n\nDangerous file type detected: .{$extension}\n\n[SECURITY PROTOCOL TRIGGERED]\n[ATTEMPT LOGGED]";
            } else {
                $logEntry['status'] = 'blocked_unknown';
                $this->uploadResult = "⛔ FILE REJECTED ⛔\n\nUnknown file type: .{$extension}\n\nAllowed types: JPG, JPEG, PNG, GIF, PDF, TXT\n\n[UPLOAD DENIED]";
            }
            
            $this->fileLog[] = $logEntry;
        }
    }

    public function render(): void
    {
        include_once ROOT . '/shared/military-ui/header.php';
?>

<!-- MISSION HEADER -->
<div class="mission-header">
  <div class="mission-title">
    <i class="fas fa-file-upload"></i>
    <span>MISSION: INTEL UPLOAD</span>
  </div>
  
  <div class="mission-grid">
    <div class="mission-stat">
      <div class="stat-label">OBJECTIVE</div>
      <div class="stat-value">BYPASS FILE VALIDATION</div>
    </div>
    <div class="mission-stat">
      <div class="stat-label">TARGET SYSTEM</div>
      <div class="stat-value warning">SECURE FILE TRANSFER</div>
    </div>
    <div class="mission-stat">
      <div class="stat-label">CLEARANCE REQUIRED</div>
      <div class="stat-value danger">LEVEL 2</div>
    </div>
    <div class="mission-stat">
      <div class="stat-label">VULNERABILITY TYPE</div>
      <div class="stat-value text-blue">UNRESTRICTED UPLOAD</div>
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
      <span class="text-green">OPERATOR,</span> the enemy uses this secure file transfer system 
      to share classified intelligence. The system claims to validate uploads, but our analysts 
      have identified critical weaknesses.
    </p>
    <p style="color: var(--text-secondary); margin-bottom: 15px;">
      Your objective: Upload a malicious payload that bypasses the file validation. The system 
      only checks file extensions, not actual content. A PHP backdoor disguised as an image 
      should slip through undetected.
    </p>
    <div style="background: rgba(255, 176, 0, 0.1); border-left: 3px solid var(--mil-amber); padding: 12px; font-family: var(--font-mono); font-size: 12px;">
      <span class="text-amber"><i class="fas fa-lightbulb"></i> INTELLIGENCE HINT:</span><br>
      Create a file named <code class="bg-panel-light px-1">shell.jpg</code> containing:<br>
      <code class="bg-panel-light px-1" style="display: block; margin-top: 5px;">
        &lt;?php system($_GET['cmd']); ?&gt;
      </code>
      The system will accept it because of the .jpg extension, but the server will still execute the PHP code.
    </div>
  </div>
</div>

<!-- UPLOAD TERMINAL -->
<div class="terminal-panel">
  <div class="terminal-header">
    <i class="fas fa-terminal"></i>
    <span>SECURE FILE TRANSFER</span>
  </div>
  <div class="terminal-body">
    <form method="POST" enctype="multipart/form-data" id="uploadForm">
      <div style="margin-bottom: 20px;">
        <label style="display: block; font-family: var(--font-mono); font-size: 12px; color: var(--text-secondary); margin-bottom: 8px;">
          <i class="fas fa-folder-open"></i> SELECT INTELLIGENCE FILE
        </label>
        <input type="file" name="intel_file" class="mil-input" accept=".jpg,.jpeg,.png,.gif,.pdf,.txt">
        <p style="font-family: var(--font-mono); font-size: 10px; color: var(--text-dim); margin-top: 8px;">
          <i class="fas fa-info-circle"></i> Allowed formats: JPG, JPEG, PNG, GIF, PDF, TXT | Max size: 10MB
        </p>
      </div>
      
      <button type="submit" class="mil-button amber">
        <i class="fas fa-upload"></i> UPLOAD TO SECURE SERVER
      </button>
    </form>
  </div>
</div>

<!-- UPLOAD RESULT -->
<?php if ($this->uploadAttempted): ?>
<div class="terminal-panel">
  <div class="terminal-header">
    <i class="fas fa-desktop"></i>
    <span>UPLOAD STATUS</span>
  </div>
  <div class="terminal-body">
    <div class="output-terminal <?= strpos($this->uploadResult, 'BLOCKED') !== false || strpos($this->uploadResult, 'REJECTED') !== false ? 'error' : '' ?>">
<?= htmlspecialchars($this->uploadResult) ?>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- FILE LOG -->
<?php if (!empty($this->fileLog)): ?>
<div class="terminal-panel">
  <div class="terminal-header">
    <i class="fas fa-scroll"></i>
    <span>TRANSFER LOG</span>
  </div>
  <div class="terminal-body">
    <div class="output-terminal" style="min-height: 100px;">
<?php foreach ($this->fileLog as $log): ?>
      <div style="margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px dashed var(--border-color);">
        <span class="text-dim" style="font-size: 10px;">[<?= htmlspecialchars($log['timestamp']) ?>]</span>
        <?php 
        $statusClass = 'text-green';
        $statusText = '[OK]';
        if ($log['status'] === 'blocked_dangerous' || $log['status'] === 'blocked_unknown') {
            $statusClass = 'text-red';
            $statusText = '[BLOCKED]';
        } elseif ($log['status'] === 'backdoor_detected') {
            $statusClass = 'text-amber';
            $statusText = '[BACKDOOR]';
        } elseif ($log['status'] === 'failed') {
            $statusClass = 'text-red';
            $statusText = '[FAILED]';
        }
        ?>
        <span class="<?= $statusClass ?>"><?= $statusText ?></span>
        <br>
        <span style="color: var(--text-secondary);">File:</span> <code style="color: var(--mil-green);"><?= htmlspecialchars($log['filename']) ?></code>
        <span style="color: var(--text-dim); margin-left: 10px;">(<?= number_format($log['size']) ?> bytes)</span>
      </div>
<?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- UPLOADED FILES LIST -->
<?php if (!empty($this->uploadedFiles)): ?>
<div class="terminal-panel">
  <div class="terminal-header">
    <i class="fas fa-folder"></i>
    <span>UPLOADED FILES</span>
  </div>
  <div class="terminal-body">
    <div class="output-terminal" style="min-height: 80px;">
<?php foreach ($this->uploadedFiles as $file): ?>
      <div style="padding: 5px 0; border-bottom: 1px solid var(--border-color);">
        <i class="fas fa-file text-green"></i>
        <code style="color: var(--mil-green);"><?= htmlspecialchars($file['name']) ?></code>
        <span class="text-dim" style="font-size: 10px; margin-left: 10px;">
          [<?= htmlspecialchars($file['timestamp']) ?>] <?= number_format($file['size']) ?> bytes
        </span>
      </div>
<?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- SECURITY SCANNER SIMULATION -->
<div class="terminal-panel">
  <div class="terminal-header">
    <i class="fas fa-shield-virus"></i>
    <span>SECURITY SCAN PARAMETERS</span>
  </div>
  <div class="terminal-body">
    <div class="output-terminal" style="min-height: 100px;">
<span class="text-blue">[CONFIG]</span> MIME Type Check: <span class="text-red">DISABLED</span>
<span class="text-blue">[CONFIG]</span> Content Validation: <span class="text-red">DISABLED</span>
<span class="text-blue">[CONFIG]</span> Extension Whitelist: <span class="text-green">ENABLED</span>
<span class="text-blue">[CONFIG]</span> Magic Bytes Check: <span class="text-red">DISABLED</span>
<span class="text-blue">[CONFIG]</span> File Signature Analysis: <span class="text-red">DISABLED</span>

<span class="text-amber">[WARNING]</span> Current security configuration is CRITICALLY WEAK
<span class="text-amber">[WARNING]</span> Extension-only validation is easily bypassed
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
      <i class="fas fa-trophy"></i> INTEL UPLOAD BREACH SUCCESSFUL<br><br>
      You have successfully bypassed the file upload validation.<br>
      A PHP backdoor was uploaded and is now accessible on the server.<br><br>
      <span class="text-dim">Technique: Extension Bypass / Content-Type Manipulation</span>
    </p>
  </div>
</div>
<?php endif; ?>

<script>
// Initialize File Upload Lab specific functionality
document.addEventListener('DOMContentLoaded', function() {
  console.log('%c FILE UPLOAD LAB LOADED ', 'background: #00d9ff; color: black;');
  
  // Add form submission effect
  const form = document.getElementById('uploadForm');
  if (form) {
    form.addEventListener('submit', function() {
      const messages = [
        'INITIATING UPLOAD...',
        'SCANNING FILE...',
        'CHECKING MIME TYPE...',
        'VALIDATING EXTENSION...',
        'PROCESSING...'
      ];
      simulateLoading(messages, 2000);
    });
  }
});
</script>

<?php
        include_once ROOT . '/shared/military-ui/footer.php';
    }

    public function validate(): bool
    {
        return Session::get('file_upload_solved') === true;
    }
}
