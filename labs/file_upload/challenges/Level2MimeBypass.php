<?php
/**
 * File Upload Lab - Level 2: MIME Type Bypass
 * 
 * Vulnerability: Validates only Content-Type header, not actual file content (magic bytes)
 * Attack Vector: Upload PHP web shell with spoofed MIME type
 * 
 * This challenge demonstrates insecure file upload where the Content-Type header
 * is checked but can be easily spoofed, and no magic byte verification is performed.
 */

namespace Labs\FileUpload\Challenges;

use App\Core\ChallengeInterface;
use App\Core\BaseChallenge;
use App\Core\Session;

class Level2MimeBypass extends BaseChallenge implements ChallengeInterface
{
    private $completed = false;
    private $uploadDir;
    private $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
    private $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

    public function __construct()
    {
        $this->uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
        
        // Check if already solved via session
        if (Session::get('file_upload_lvl2_solved') === true) {
            $this->completed = true;
        }
    }

    public function render(): void
    {
        include_once ROOT . '/shared/military-ui/header.php';
?>
<div class="mission-header">
  <div class="mission-title">
    <i class="fas fa-file-upload"></i>
    <span>MISSION: MIME TYPE BYPASS</span>
  </div>
  <div class="mission-grid">
    <div class="mission-stat">
      <div class="stat-label">OBJECTIVE</div>
      <div class="stat-value">UPLOAD WEB SHELL</div>
    </div>
    <div class="mission-stat">
      <div class="stat-label">TARGET</div>
      <div class="stat-value warning">MIME VALIDATION</div>
    </div>
    <div class="mission-stat">
      <div class="stat-label">TECHNIQUE</div>
      <div class="stat-value danger">HEADER SPOOFING</div>
    </div>
  </div>
</div>

<div class="terminal-panel">
  <div class="terminal-header">
    <i class="fas fa-upload"></i>
    <span>SECURE FILE TRANSFER PROTOCOL</span>
  </div>
  <div class="terminal-body">
    <form method="POST" enctype="multipart/form-data">
      <div style="margin-bottom: 20px;">
        <label style="display: block; font-family: monospace; margin-bottom: 5px;">SELECT INTELLIGENCE PAYLOAD</label>
        <input type="file" name="upload" class="mil-input" accept="image/*">
      </div>
      <button type="submit" class="mil-button">UPLOAD</button>
    </form>

    <?php if ($this->completed): ?>
    <div class="terminal-panel" style="border-color: #00ff41; margin-top: 20px;">
      <div class="terminal-header" style="background: rgba(0,255,65,0.1);">
        <i class="fas fa-check-circle text-green"></i>
        <span class="text-green">MISSION ACCOMPLISHED</span>
      </div>
      <div class="terminal-body">
        <p class="text-green">Successfully bypassed MIME type validation and uploaded executable payload.</p>
      </div>
    </div>
    <?php endif; ?>

    <div class="mil-hint-box" style="margin-top: 20px;">
      <strong>INTELLIGENCE HINT:</strong><br>
      The system only checks the Content-Type header sent by your browser, not the actual file content. Use browser dev tools or a proxy (Burp Suite) to change the Content-Type to 'image/jpeg' when uploading a .php file. Alternatively, rename your PHP file to shell.jpg and upload with spoofed MIME type.
    </div>
  </div>
</div>
<?php
        include_once ROOT . '/shared/military-ui/footer.php';
    }

    public function handle(): void
    {
        if (isset($_FILES['upload']) && $_FILES['upload']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['upload'];
            $originalName = $file['name'];
            $tmpName = $file['tmp_name'];
            $fileSize = $file['size'];
            $fileType = $file['type']; // ⚠️ VULNERABLE: User-controlled Content-Type header
            
            // Get file extension
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            // ⚠️ VULNERABLE: Only checking Content-Type header (easily spoofed)
            // No magic byte verification, no getimagesize() check
            if (!in_array($fileType, $this->allowedMimeTypes)) {
                return;
            }

            // Also check extension (but this is also bypassable with double extensions or valid image names)
            if (!in_array($extension, $this->allowedExtensions)) {
                return;
            }
            
            // Generate unique filename but preserve extension
            $newFilename = uniqid('intel_mime_') . '.' . $extension;
            $destination = $this->uploadDir . $newFilename;

            if (move_uploaded_file($tmpName, $destination)) {
                // Check if it's actually a PHP file (web shell) despite having image MIME type
                // Attacker would need to spoof Content-Type as image while uploading PHP
                $content = file_get_contents($destination);
                if (strpos($content, '<?php') !== false || strpos($content, '<?') !== false || strpos($content, '<%=') !== false) {
                    Session::set('file_upload_lvl2_solved', true);
                    $this->completed = true;
                    $this->markCompleted('file_upload', 'lvl2');
                }
            }
        }
    }

    public function validate(array $data): bool
    {
        // Check session flag set during handle()
        if (Session::get('file_upload_lvl2_solved') === true) {
            return true;
        }
        return false;
    }

    public function getHint(): string
    {
        return "The system only checks the Content-Type header sent by your browser, not the actual file content. Use browser dev tools or a proxy (Burp Suite) to change the Content-Type to 'image/jpeg' when uploading a .php file. Alternatively, rename your PHP file to shell.jpg and upload with spoofed MIME type.";
    }

    public function getTitle(): string
    {
        return "Enhanced File Transfer System";
    }

    public function getDescription(): string
    {
        return "Bypass MIME type validation to upload executable intelligence payloads. The system checks Content-Type headers but not actual file content.";
    }
}
