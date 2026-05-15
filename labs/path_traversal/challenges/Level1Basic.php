<?php

namespace Labs\PathTraversal\Challenges;

use App\Core\BaseChallenge;

/**
 * Level 1: Basic Path Traversal Challenge
 * 
 * INTENTIONAL VULNERABILITY: This challenge demonstrates path traversal
 * by allowing unsanitized file path input.
 */
class Level1Basic extends BaseChallenge
{
    private $baseDir = '/var/www/html/uploads/';
    
    public function render(): string
    {
        $filename = $this->getInput('file', 'welcome.txt');
        $content = '';
        $fileContent = null;
        $error = null;
        
        if ($this->isPost()) {
            $result = $this->handle();
            if (isset($result['error'])) {
                $error = $result['error'];
            } elseif (isset($result['content'])) {
                $fileContent = $result['content'];
            }
        }
        
        ob_start();
        ?>
        <div class="container mt-4">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h3><i class="fas fa-folder-open"></i> Level 1: Basic Path Traversal</h3>
                        </div>
                        <div class="card-body">
                            <p class="lead">You have discovered a file viewer that allows you to read arbitrary files.</p>
                            
                            <div class="alert alert-info">
                                <strong>Goal:</strong> Exploit the path traversal vulnerability to read sensitive system files like <code>/etc/passwd</code>.
                            </div>
                            
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                            
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="file" class="form-label">File to view:</label>
                                    <input type="text" class="form-control" id="file" name="file" value="<?php echo htmlspecialchars($filename); ?>" placeholder="Enter filename">
                                    <small class="form-text text-muted">Try accessing files outside the uploads directory</small>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-eye"></i> View File
                                </button>
                            </form>
                            
                            <?php if ($fileContent !== null): ?>
                                <hr>
                                <div class="mt-3">
                                    <h5>File Contents:</h5>
                                    <div class="border p-3 bg-light" style="max-height: 400px; overflow-y: auto;">
                                        <pre><?php echo htmlspecialchars($fileContent); ?></pre>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="alert alert-warning mt-3">
                                <strong>Tip:</strong> Try using <code>../</code> sequences to navigate to parent directories.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    public function handle(): array
    {
        $filename = $this->getInput('file', '');
        
        if (empty($filename)) {
            return ['error' => 'No filename provided'];
        }
        
        // INTENTIONAL VULNERABILITY: No sanitization of path input
        // This allows path traversal attacks
        $filePath = $this->baseDir . $filename;
        
        // Check if user successfully accessed a sensitive file
        if (strpos($filename, '../') !== false || strpos($filename, '..\\') !== false) {
            if (strpos($filename, 'etc/passwd') !== false || strpos($filename, 'etc/shadow') !== false) {
                $this->markCompleted();
            }
        }
        
        // Simulate file reading (in real scenario this would be file_get_contents)
        if (strpos($filename, '../') !== false || strpos($filename, '..\\') !== false) {
            if (strpos($filename, 'etc/passwd') !== false) {
                return [
                    'content' => "root:x:0:0:root:/root:/bin/bash\nwww-data:x:33:33:www-data:/var/www:/usr/sbin/nologin\nuser:x:1000:1000:User:/home/user:/bin/bash",
                    'success' => true
                ];
            } elseif (strpos($filename, 'config.php') !== false) {
                return [
                    'content' => "<?php\n// Database configuration\n\$db_host = 'localhost';\n\$db_user = 'admin';\n\$db_pass = 'supersecret_password_123';\n?>",
                    'success' => true
                ];
            }
        }
        
        // Default response for normal files
        return [
            'content' => "Contents of {$filename}\nThis is a sample file for the path traversal exercise.\n\nVULNERABILITY NOTE: In a real application, this would read the actual file from: {$filePath}",
            'success' => false
        ];
    }
    
    public function validate(): bool
    {
        return true;
    }
}