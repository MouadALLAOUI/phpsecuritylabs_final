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

    public function render(): string
    {
        ob_start();
        include __DIR__ . '/../views/mime_upload_terminal.php';
        return ob_get_clean();
    }

    public function handle(array $data): array
    {
        $response = [
            'success' => false,
            'message' => '',
            'filename' => '',
            'filepath' => '',
            'scan_log' => [],
            'mime_check' => '',
            'extension_check' => ''
        ];

        // Add fake scan log entries
        $response['scan_log'][] = '[SYSTEM] Initializing secure file transfer protocol...';
        $response['scan_log'][] = '[SCAN] Analyzing uploaded file...';

        if (isset($_FILES['upload']) && $_FILES['upload']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['upload'];
            $originalName = $file['name'];
            $tmpName = $file['tmp_name'];
            $fileSize = $file['size'];
            $fileType = $file['type']; // ⚠️ VULNERABLE: User-controlled Content-Type header
            
            // Get file extension
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            
            $response['scan_log'][] = "[SCAN] File size: " . number_format($fileSize) . " bytes";
            $response['scan_log'][] = "[SCAN] Original filename: " . basename($originalName);
            $response['scan_log'][] = "[SCAN] Reported MIME type: " . $fileType;
            $response['scan_log'][] = "[SCAN] Detected extension: ." . strtoupper($extension);

            // ⚠️ VULNERABLE: Only checking Content-Type header (easily spoofed)
            // No magic byte verification, no getimagesize() check
            if (in_array($fileType, $this->allowedMimeTypes)) {
                $response['mime_check'] = 'PASSED';
                $response['scan_log'][] = "[CHECK] MIME type validation: PASSED";
            } else {
                $response['mime_check'] = 'FAILED';
                $response['scan_log'][] = "[CHECK] MIME type validation: FAILED";
                $response['scan_log'][] = "[REJECT] Invalid MIME type: " . $fileType;
                $response['message'] = 'FILE REJECTED: Invalid content type. Only JPEG, PNG, GIF allowed.';
                return $response;
            }

            // Also check extension (but this is also bypassable with double extensions or valid image names)
            if (in_array($extension, $this->allowedExtensions)) {
                $response['extension_check'] = 'PASSED';
                $response['scan_log'][] = "[CHECK] Extension validation: PASSED";
            } else {
                $response['extension_check'] = 'FAILED';
                $response['scan_log'][] = "[CHECK] Extension validation: FAILED";
                $response['scan_log'][] = "[REJECT] Invalid extension: ." . $extension;
                $response['message'] = 'FILE REJECTED: Invalid file extension. Only JPG, PNG, GIF allowed.';
                return $response;
            }

            $response['scan_log'][] = "[WARN] Magic byte analysis: DISABLED";
            $response['scan_log'][] = "[WARN] Image signature verification: SKIPPED";
            $response['scan_log'][] = "[WARN] Content inspection: NOT PERFORMED";
            
            // Generate unique filename but preserve extension
            $newFilename = uniqid('intel_mime_') . '.' . $extension;
            $destination = $this->uploadDir . $newFilename;

            if (move_uploaded_file($tmpName, $destination)) {
                $response['success'] = true;
                $response['filename'] = $newFilename;
                $response['filepath'] = '/labs/file_upload/uploads/' . $newFilename;
                
                $response['scan_log'][] = "[TRANSFER] File uploaded successfully";
                $response['scan_log'][] = "[STORE] Saved to: " . $newFilename;
                
                // Check if it's actually a PHP file (web shell) despite having image MIME type
                // Attacker would need to spoof Content-Type as image while uploading PHP
                $content = file_get_contents($destination);
                if (strpos($content, '<?php') !== false || strpos($content, '<?') !== false || strpos($content, '<%=') !== false) {
                    $response['message'] = '⚠️ WARNING: Executable code detected in file with image MIME type!';
                    $response['scan_log'][] = "[ALERT] EXECUTABLE CODE DETECTED DESPITE VALID MIME TYPE";
                    $response['scan_log'][] = "[ALERT] MIME type spoofing successful!";
                    $response['scan_log'][] = "[ALERT] Potential web shell uploaded!";
                    $response['scan_log'][] = "[SUCCESS] Challenge complete - MIME filter bypassed";
                    
                    // Mark as completed if they uploaded a PHP file with spoofed MIME type
                    $this->markCompleted();
                    Session::set('file_upload_lvl2_solved', true);
                    $this->completed = true;
                } else {
                    $response['message'] = 'File uploaded successfully. MIME type verified.';
                    $response['scan_log'][] = "[VERIFY] File appears to be valid image";
                }
            } else {
                $response['message'] = 'File transfer failed';
                $response['scan_log'][] = "[ERROR] File transfer failed";
            }
        } elseif (isset($_FILES['upload'])) {
            $response['message'] = 'File upload error: ' . $this->getUploadErrorMessage($_FILES['upload']['error']);
            $response['scan_log'][] = "[ERROR] " . $response['message'];
        } else {
            $response['message'] = 'No file selected for transfer';
            $response['scan_log'][] = "[IDLE] Awaiting file selection...";
        }

        return $response;
    }

    private function getUploadErrorMessage($errorCode): string
    {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds server maximum size',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds form maximum size',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Server temporary directory missing',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'PHP extension stopped the upload'
        ];
        return $errors[$errorCode] ?? 'Unknown upload error';
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
