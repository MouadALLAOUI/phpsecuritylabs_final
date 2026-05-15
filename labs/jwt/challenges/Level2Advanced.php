<?php

namespace Labs\Jwt\Challenges;

use App\Core\BaseChallenge;

/**
 * Level 2: JWT Weak Secret Key Challenge
 * 
 * INTENTIONAL VULNERABILITY: This challenge demonstrates brute-forcing
 * weak JWT secret keys.
 */
class Level2Advanced extends BaseChallenge
{
    private $weakSecret = 'password123';
    
    public function render(): string
    {
        $token = $this->getInput('token', '');
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
        
        // Generate a default token if none provided
        if (empty($token)) {
            $token = $this->createToken('user', 'standard_user');
        }
        
        ob_start();
        ?>
        <div class="container mt-4">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h3><i class="fas fa-user-secret"></i> Level 2: Weak Secret Key</h3>
                        </div>
                        <div class="card-body">
                            <p class="lead">You have discovered a JWT-based authentication system using a weak secret key.</p>
                            
                            <div class="alert alert-info">
                                <strong>Goal:</strong> Crack the weak JWT secret key and forge a token with admin privileges.
                                <br>Hint: The secret might be a common password like "password", "secret", "admin", etc.
                            </div>
                            
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                            
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="token" class="form-label">Forged JWT Token:</label>
                                    <textarea class="form-control font-monospace" id="token" name="token" rows="3" placeholder="Enter your forged JWT token"><?php echo htmlspecialchars($token); ?></textarea>
                                    <small class="form-text text-muted">Create a token signed with the correct weak secret and role=admin</small>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-key"></i> Submit Token
                                </button>
                            </form>
                            
                            <?php if ($result !== null): ?>
                                <hr>
                                <div class="mt-3">
                                    <h5>Result:</h5>
                                    <div class="border p-3 bg-light">
                                        <pre><?php echo htmlspecialchars($result); ?></pre>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="alert alert-info mt-3">
                                <strong>Common weak secrets to try:</strong><br>
                                <code>password</code>, <code>123456</code>, <code>secret</code>, <code>admin</code>, 
                                <code>root</code>, <code>qwerty</code>, <code>password123</code>
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
        $token = $this->getInput('token', '');
        
        if (empty($token)) {
            return ['error' => 'No token provided'];
        }
        
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            return ['error' => 'Invalid JWT format'];
        }
        
        list($headerB64, $payloadB64, $signatureB64) = $parts;
        
        // Common weak secrets to try
        $weakSecrets = ['password', '123456', 'secret', 'admin', 'root', 'qwerty', 'password123', 'letmein', 'welcome', 'monkey'];
        
        $foundSecret = null;
        foreach ($weakSecrets as $secret) {
            $expectedSig = hash_hmac('sha256', "{$headerB64}.{$payloadB64}", $secret, true);
            $expectedSigB64 = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($expectedSig));
            
            if (hash_equals($expectedSigB64, $signatureB64)) {
                $foundSecret = $secret;
                break;
            }
        }
        
        if (!$foundSecret) {
            return [
                'result' => "Token signature does not match any known weak secret.\n\nTry forging a new token with one of the common secrets listed below.",
                'success' => false
            ];
        }
        
        // Decode payload to check role
        $payloadJson = base64_decode(str_replace(['-', '_'], ['+', '/'], $payloadB64));
        $payload = json_decode($payloadJson, true);
        
        if (!$payload) {
            return ['error' => 'Invalid payload'];
        }
        
        $result = "SUCCESS! Cracked secret key: '{$foundSecret}'\n\n";
        $result .= "Payload: " . json_encode($payload, JSON_PRETTY_PRINT);
        
        if (isset($payload['role']) && $payload['role'] === 'admin') {
            $this->markCompleted();
            $result .= "\n\nVULNERABILITY EXPLOITED: You successfully cracked the weak secret and forged an admin token!";
            return ['result' => $result, 'success' => true];
        }
        
        $result .= "\n\nNote: You found the secret, but the token doesn't have admin role. Forge a new token with role=admin.";
        return ['result' => $result, 'success' => false];
    }
    
    public function validate(): bool
    {
        return true;
    }
    
    private function createToken($type, $user, $role = 'user'): string
    {
        $header = ['typ' => 'JWT', 'alg' => 'HS256'];
        $payload = [
            'sub' => $user,
            'role' => $role,
            'iat' => time(),
            'exp' => time() + 3600
        ];
        
        $headerB64 = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($header)));
        $payloadB64 = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));
        
        $signature = hash_hmac('sha256', "{$headerB64}.{$payloadB64}", $this->weakSecret, true);
        $signatureB64 = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        return "{$headerB64}.{$payloadB64}.{$signatureB64}";
    }
}