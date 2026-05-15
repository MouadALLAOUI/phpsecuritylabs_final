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
    public function render(): string
    {
        $serializedData = $this->getInput('data', '');
        $result = null;
        $error = null;
        
        if ($this->isPost()) {
            $response = $this->handle();
            if (isset($response['error'])) {
                $error = $response['error'];
            } elseif (isset($response['result'])) {
                $result = $response['result'];
            }
        }
        
        // Default serialized object for demonstration
        if (empty($serializedData)) {
            $serializedData = 'Tzo0OiJVc2VyIjoyOntzOjg6InVzZXJuYW1lIjtzOjU6IkFsaWNlIjtzOjU6InJvbGUiO3M6NDoidXNlciI7fQ==';
        }
        
        ob_start();
        ?>
        <div class="container mt-4">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h3><i class="fas fa-cube"></i> Level 1: Basic Deserialization</h3>
                        </div>
                        <div class="card-body">
                            <p class="lead">You have discovered a functionality that accepts serialized PHP objects.</p>
                            
                            <div class="alert alert-info">
                                <strong>Goal:</strong> Exploit the PHP deserialization vulnerability to manipulate object properties or trigger unexpected behavior.
                                <br>Try to create an object with <code>role: admin</code>.
                            </div>
                            
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                            
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="data" class="form-label">Serialized data (base64 encoded):</label>
                                    <textarea class="form-control font-monospace" id="data" name="data" rows="4" placeholder="Enter base64-encoded serialized PHP object"><?php echo htmlspecialchars($serializedData); ?></textarea>
                                    <small class="form-text text-muted">Hint: Decode, modify the serialized object, then re-encode in base64</small>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-magic"></i> Deserialize
                                </button>
                            </form>
                            
                            <?php if ($result !== null): ?>
                                <hr>
                                <div class="mt-3">
                                    <h5>Deserialization Result:</h5>
                                    <div class="border p-3 bg-light">
                                        <pre><?php echo htmlspecialchars($result); ?></pre>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="alert alert-warning mt-3">
                                <strong>Tip:</strong> Look for classes with magic methods like <code>__destruct()</code>, <code>__wakeup()</code>, etc.
                                <br>Try changing the role from "user" to "admin" in the serialized object.
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
        $serializedData = $this->getInput('data', '');
        
        if (empty($serializedData)) {
            return ['error' => 'No data provided'];
        }
        
        // Decode base64
        $decoded = base64_decode($serializedData);
        
        if (!$decoded) {
            return ['error' => 'Invalid base64 encoding'];
        }
        
        // Check if user successfully crafted an admin object
        if (strpos($decoded, 's:4:"role";s:5:"admin"') !== false || 
            strpos($decoded, 's:4:"role";s:4:"admin"') !== false) {
            $this->markCompleted();
            return [
                'result' => "SUCCESS! Admin object detected!\n\nDecoded payload:\n{$decoded}\n\nVULNERABILITY EXPLOITED: You successfully injected a malicious serialized object with admin privileges!",
                'success' => true
            ];
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
        
        return [
            'result' => $userInfo,
            'success' => false
        ];
    }
    
    public function validate(): bool
    {
        return true;
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