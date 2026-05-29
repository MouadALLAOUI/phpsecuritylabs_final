<?php

namespace Labs\PathTraversal\Challenges;

use App\Core\BaseChallenge;
use App\Core\Session;

/**
 * Level 1: Basic Path Traversal Challenge
 * 
 * INTENTIONAL VULNERABILITY: This challenge demonstrates path traversal
 * by allowing unsanitized file path input.
 */
class Level1Basic extends BaseChallenge
{
    private $baseDir = '/var/www/html/uploads/';
    public $filename = '';
    public $fileContent = null;
    public $error = null;
    public $solved = false;

    public function __construct()
    {
        parent::__construct();
        if (Session::get('path_traversal_lvl1_solved') === true) {
            $this->completed = true;
            $this->solved = true;
        }
    }
    
    public function render(): void
    {
        $this->filename = $this->getInput('file', 'welcome.txt');

        include_once ROOT . '/shared/header.php';
        include_once ROOT . '/shared/sidebar.php';
        
        echo '<div class="lg:ml-64 p-6 min-h-[80vh] theme-transition">';
        include ROOT . '/labs/path_traversal/views/level1.php';
        echo '</div>';
        
        include_once ROOT . '/shared/footer.php';
    }
    
    public function handle(): void
    {
        if ($this->isPost()) {
            $this->filename = $this->getInput('file', '');
            
            if (empty($this->filename)) {
                $this->error = 'No filename provided';
                return;
            }
            
            // INTENTIONAL VULNERABILITY: No sanitization of path input
            // This allows path traversal attacks
            $filePath = $this->baseDir . $this->filename;
            
            // Check if user successfully accessed a sensitive file
            if (strpos($this->filename, '../') !== false || strpos($this->filename, '..\\') !== false) {
                if (strpos($this->filename, 'etc/passwd') !== false || strpos($this->filename, 'etc/shadow') !== false) {
                    $this->markCompleted('path_traversal', 'lvl1');
                    Session::set('path_traversal_lvl1_solved', true);
                    $this->completed = true;
                    $this->solved = true;
                }
            }
            
            // Simulate file reading (in real scenario this would be file_get_contents)
            if (strpos($this->filename, '../') !== false || strpos($this->filename, '..\\') !== false) {
                if (strpos($this->filename, 'etc/passwd') !== false) {
                    $this->fileContent = "root:x:0:0:root:/root:/bin/bash\nwww-data:x:33:33:www-data:/var/www:/usr/sbin/nologin\nuser:x:1000:1000:User:/home/user:/bin/bash";
                    return;
                } elseif (strpos($this->filename, 'config.php') !== false) {
                    $this->fileContent = "<?php\n// Database configuration\n\$db_host = 'localhost';\n\$db_user = 'admin';\n\$db_pass = 'supersecret_password_123';\n?>";
                    return;
                }
            }
            
            // Default response for normal files
            $this->fileContent = "Contents of {$this->filename}\nThis is a sample file for the path traversal exercise.\n\nVULNERABILITY NOTE: In a real application, this would read the actual file from: {$filePath}";
        }
    }
    
    public function validate(): bool
    {
        return Session::get('path_traversal_lvl1_solved') === true;
    }
}