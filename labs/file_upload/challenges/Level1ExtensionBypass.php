<?php
/**
 * File Upload Lab - Level 1
 * 
 * Vulnerability: Weak file validation (extension only, no content check)
 * Attack Vector: Upload malicious file (web shell) by bypassing extension filter
 * 
 * This challenge demonstrates insecure file upload where only the file extension
 * is checked, but the actual content type and file signature are not validated.
 */

namespace App\Labs\FileUpload\Challenges;

use App\Core\ChallengeInterface;

class Level1ExtensionBypass implements ChallengeInterface
{
    private $completed = false;
    private $uploadDir;
    private $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

    public function __construct()
    {
        $this->uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function render(): string
    {
        ob_start();
        include __DIR__ . '/views/upload_terminal.php';
        return ob_get_clean();
    }

    public function handle(array $data): array
    {
        $response = [
            'success' => false,
            'message' => '',
            'filename' => '',
            'filepath' => '',
            'scan_log' => []
        ];

        // Add fake scan log entries
        $response['scan_log'][] = '[SYSTEM] Initializing file transfer protocol...';
        $response['scan_log'][] = '[SCAN] Analyzing uploaded file...';

        if (isset($_FILES['upload']) && $_FILES['upload']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['upload'];
            $originalName = $file['name'];
            $tmpName = $file['tmp_name'];
            $fileSize = $file['size'];
            
            // Get file extension
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            
            $response['scan_log'][] = "[SCAN] File size: " . number_format($fileSize) . " bytes";
            $response['scan_log'][] = "[SCAN] Original filename: " . basename($originalName);
            $response['scan_log'][] = "[SCAN] Detected extension: ." . strtoupper($extension);

            // ⚠️ VULNERABLE: Only checking extension, NOT content type or file signature
            // No MIME type verification, no magic byte checking
            if (in_array($extension, $this->allowedExtensions)) {
                $response['scan_log'][] = "[CHECK] Extension validation: PASSED";
                $response['scan_log'][] = "[WARN] Content-type verification: DISABLED";
                $response['scan_log'][] = "[WARN] Magic byte analysis: DISABLED";
                
                // Generate unique filename but preserve extension
                $newFilename = uniqid('intel_') . '.' . $extension;
                $destination = $this->uploadDir . $newFilename;

                if (move_uploaded_file($tmpName, $destination)) {
                    $response['success'] = true;
                    $response['filename'] = $newFilename;
                    $response['filepath'] = '/labs/file_upload/uploads/' . $newFilename;
                    
                    $response['scan_log'][] = "[TRANSFER] File uploaded successfully";
                    $response['scan_log'][] = "[STORE] Saved to: " . $newFilename;
                    
                    // Check if it's actually a PHP file (web shell)
                    // Read first few bytes to check for PHP tags
                    $content = file_get_contents($destination);
                    if (strpos($content, '<?php') !== false || strpos($content, '<?') !== false || strpos($content, '<%=') !== false) {
                        $response['message'] = '⚠️ WARNING: Executable code detected in image file!';
                        $response['scan_log'][] = "[ALERT] EXECUTABLE CODE DETECTED IN UPLOADED FILE";
                        $response['scan_log'][] = "[ALERT] Potential web shell uploaded!";
                        $response['scan_log'][] = "[SUCCESS] Challenge complete - File filter bypassed";
                        
                        // Mark as completed if they uploaded a PHP file disguised as image
                        $this->markCompleted();
                    } else {
                        $response['message'] = 'File uploaded successfully. Image verified.';
                        $response['scan_log'][] = "[VERIFY] File appears to be valid image";
                    }
                } else {
                    $response['message'] = 'File transfer failed';
                    $response['scan_log'][] = "[ERROR] File transfer failed";
                }
            } else {
                $response['message'] = 'FILE REJECTED: Invalid file type. Only JPG, PNG, GIF allowed.';
                $response['scan_log'][] = "[CHECK] Extension validation: FAILED";
                $response['scan_log'][] = "[REJECT] File type not permitted";
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
        return false;
    }

    public function getHint(): string
    {
        return "The system only checks file extensions, not actual content. Try uploading a PHP file with a .jpg extension. You can also try using null bytes or double extensions like shell.php.jpg";
    }

    public function getTitle(): string
    {
        return "Intelligence Upload System";
    }

    public function getDescription(): string
    {
        return "Bypass file validation to upload executable intelligence payloads to the secure server.";
    }
}
