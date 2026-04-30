<?php

/**
 * File Upload Lab - Level 1
 * 
 * Vulnerability: Weak file validation (extension only, no content check)
 */

namespace Labs\FileUpload\Challenges;

use App\Core\BaseChallenge;
use App\Core\Session;

class Level1ExtensionBypass extends BaseChallenge
{
  private string $uploadDir;
  private string $message = '';
  private array $uploadLog = [];
  private bool $attempted = false;
  private bool $uploadSuccess = false;

  public function __construct()
  {
    $this->uploadDir = ROOT . '/storage/uploads/';
    if (!is_dir($this->uploadDir)) {
      mkdir($this->uploadDir, 0755, true);
    }

    if (Session::get('file_upload_solved') === true) {
      $this->completed = true;
    }
  }

  public function handle(): void
  {
    if ($this->isPost() && isset($_FILES['upload'])) {
      $this->attempted = true;
      $file = $_FILES['upload'];
      $originalName = $file['name'];
      $tmpName = $file['tmp_name'];
      $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

      $this->uploadLog[] = ['time' => date('H:i:s'), 'msg' => "Processing: $originalName"];

      // ⚠️ VULNERABLE: Only checks extension
      $allowed = ['jpg', 'jpeg', 'png', 'gif'];
      if (in_array($extension, $allowed)) {
        $this->uploadLog[] = ['time' => date('H:i:s'), 'msg' => "Extension check PASSED"];
        $newName = uniqid('upload_') . '.' . $extension;
        $dest = $this->uploadDir . $newName;

        if (move_uploaded_file($tmpName, $dest)) {
          $this->uploadSuccess = true;
          $this->message = "File uploaded successfully: $newName";
          $this->uploadLog[] = ['time' => date('H:i:s'), 'msg' => "Saved as: $newName"];

          // Check for PHP code inside
          $content = file_get_contents($dest);
          if (strpos($content, '<?php') !== false) {
            $this->message .= " [BACKDOOR DETECTED] Challenge complete!";
            $this->markCompleted('file_upload', 'lvl1');
            Session::set('file_upload_solved', true);
            $this->completed = true;
            $this->uploadLog[] = ['time' => date('H:i:s'), 'msg' => "ALERT: PHP code detected in uploaded file"];
          }
        } else {
          $this->message = "Upload failed.";
          $this->uploadLog[] = ['time' => date('H:i:s'), 'msg' => "Move failed"];
        }
      } else {
        $this->message = "Rejected: Invalid file type. Only JPG, PNG, GIF allowed.";
        $this->uploadLog[] = ['time' => date('H:i:s'), 'msg' => "Extension check FAILED"];
      }
    }
  }

  public function render(): void
  {
    include_once ROOT . '/shared/military-ui/header.php';
?>
<div class="mission-header">
  <div class="mission-title">
    <i class="fas fa-upload"></i>
    <span>MISSION: PAYLOAD DELIVERY</span>
  </div>
  <div class="mission-grid">
    <div class="mission-stat">
      <div class="stat-label">OBJECTIVE</div>
      <div class="stat-value">UPLOAD WEB SHELL</div>
    </div>
    <div class="mission-stat">
      <div class="stat-label">TARGET</div>
      <div class="stat-value warning">SECURE FILE TRANSFER</div>
    </div>
    <div class="mission-stat">
      <div class="stat-label">CLEARANCE</div>
      <div class="stat-value danger">LEVEL 1</div>
    </div>
  </div>
</div>

<div class="terminal-panel">
  <div class="terminal-header">
    <i class="fas fa-terminal"></i>
    <span>FILE TRANSFER TERMINAL</span>
  </div>
  <div class="terminal-body">
    <form method="POST" enctype="multipart/form-data">
      <div style="margin-bottom: 20px;">
        <label style="display: block; font-family: monospace; margin-bottom: 5px;">SELECT FILE</label>
        <input type="file" name="upload" class="mil-input">
      </div>
      <button type="submit" class="mil-button">UPLOAD</button>
    </form>

    <?php if ($this->attempted): ?>
    <div class="output-terminal" style="margin-top: 20px;">
      <?= htmlspecialchars($this->message) ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($this->uploadLog)): ?>
    <div class="output-terminal" style="margin-top: 20px; background: #000; font-size: 12px;">
      <strong>TRANSFER LOG:</strong><br>
      <?php foreach ($this->uploadLog as $log): ?>
      [<?= $log['time'] ?>] <?= htmlspecialchars($log['msg']) ?><br>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="mil-hint-box" style="margin-top: 20px;">
      <strong>INTELLIGENCE:</strong> Only extension is checked. Create a PHP file named <code>shell.jpg</code>
      containing <code>&lt;?php system($_GET['cmd']); ?&gt;</code>.
    </div>
  </div>
</div>

<?php if ($this->completed): ?>
<div class="terminal-panel" style="border-color: #00ff41;">
  <div class="terminal-header" style="background: rgba(0,255,65,0.1);">
    <i class="fas fa-check-circle text-green"></i>
    <span class="text-green">MISSION ACCOMPLISHED</span>
  </div>
  <div class="terminal-body">
    <p class="text-green">File upload bypassed. Backdoor deployed.</p>
  </div>
</div>
<?php endif; ?>

<?php
    include_once ROOT . '/shared/military-ui/footer.php';
  }

  public function validate(): bool
  {
    return Session::get('file_upload_solved') === true;
  }
}