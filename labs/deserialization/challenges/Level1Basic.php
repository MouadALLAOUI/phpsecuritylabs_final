<?php

namespace Labs\Deserialization\Challenges;

use App\Core\BaseChallenge;

/**
 * Level 1: Basic PHP Object Injection Challenge
 * 
 * INTENTIONAL VULNERABILITY: This challenge demonstrates PHP object injection
 * by allowing unserialized user input.
 */
class Level1Basic extends BaseChallenge
{
    public $serializedData = '';
    public $result = null;
    public $error = null;
    public $solved = false;

    public function __construct()
    {
        parent::__construct();
        if (Session::get('deserialization_lvl1_solved') === true) {
            $this->completed = true;
            $this->solved = true;
        }
    }

    public function render(): void
    {
        $this->serializedData = $this->getInput('data', '');
        
        // Default serialized object for demonstration
        if (empty($this->serializedData)) {
            $this->serializedData = 'Tzo0OiJVc2VyIjoyOntzOjg6InVzZXJuYW1lIjtzOjU6IkFsaWNlIjtzOjU6InJvbGUiO3M6NDoidXNlciI7fQ==';
        }

        include_once ROOT . '/shared/header.php';
        include_once ROOT . '/shared/sidebar.php';
        
        echo '<div class="lg:ml-64 p-6 min-h-[80vh] theme-transition">';
        include ROOT . '/labs/deserialization/views/level1.php';
        echo '</div>';
        
        include_once ROOT . '/shared/footer.php';
    }
    
    public function handle(): void
    {
        if ($this->isPost()) {
            $this->serializedData = $this->getInput('data', '');
            
            if (empty($this->serializedData)) {
                $this->error = 'No data provided';
                return;
            }
            
            // Decode base64
            $decoded = base64_decode($this->serializedData);
            
            if (!$decoded) {
                $this->error = 'Invalid base64 encoding';
                return;
            }
            
            // Check if user successfully crafted an admin object
            if (strpos($decoded, 's:4:"role";s:5:"admin"') !== false || 
                strpos($decoded, 's:4:"role";s:4:"admin"') !== false) {
                $this->markCompleted('deserialization', 'lvl1');
                Session::set('deserialization_lvl1_solved', true);
                $this->completed = true;
                $this->solved = true;
                $this->result = "SUCCESS! Admin object detected!\n\nDecoded payload:\n{$decoded}\n\nVULNERABILITY EXPLOITED: You successfully injected a malicious serialized object with admin privileges!\n\nFLAG{DESERIALIZATION_OBJECT_INJECTION_SUCCESS}";
                return;
            }
            
            // Parse and display the object info (simulated unserialize)
            $userInfo = "Object deserialized successfully.\n\n";
            if (preg_match('/s:8:"username";s:(\d+):"([^"]+)"/', $decoded, $matches)) {
                $userInfo .= "Username: {$matches[2]}\n";
            }
            if (preg_match('/s:4:"role";s:(\d+):"([^"]+)"/', $decoded, $matches)) {
                $userInfo .= "Role: {$matches[2]}\n";
            }
            
            $userInfo .= "\nRaw payload:\n{$decoded}";
            
            $this->result = $userInfo;
        }
    }
    
    public function validate(): bool
    {
        return Session::get('deserialization_lvl1_solved') === true;
    }
}

/**
 * Example vulnerable class for educational purposes
 * In a real application, this would be defined elsewhere
 */
class User {
    public $username;
    public $role;
    
    public function __construct($username, $role) {
        $this->username = $username;
        $this->role = $role;
    }
    
    public function __toString() {
        return "User({$this->username}, {$this->role})";
    }
}