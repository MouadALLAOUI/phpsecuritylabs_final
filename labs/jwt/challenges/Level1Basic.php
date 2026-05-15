<?php

namespace Labs\Jwt\Challenges;

use App\Core\BaseChallenge;

/**
 * Level 1: JWT Algorithm Confusion Challenge
 * 
 * INTENTIONAL VULNERABILITY: This challenge demonstrates JWT algorithm confusion
 * by accepting tokens with weak or "none" algorithm.
 */
class Level1Basic extends BaseChallenge
{
    private $secretKey = 'weak_secret';
    
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
            $token = $this->createToken('user', 'basic_user');
        }
        
        ob_start();
        ?>
        <div class="container mt-4">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h3><i class="fas fa-key"></i> Level 1: JWT Algorithm Confusion</h3>
                        </div>
                        <div class="card-body">
                            <p class="lead">You have discovered a JWT-based authentication system with potential algorithm confusion vulnerability.</p>
                            
                            <div class="alert alert-info">
                                <strong>Goal:</strong> Exploit the JWT algorithm confusion vulnerability to gain admin access.
                                <br>Try changing the algorithm to "none" or using a different signing algorithm.
                            </div>
                            
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                            <?php endif; ?>
                            
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="token" class="form-label">JWT Token:</label>
                                    <textarea class="form-control font-monospace" id="token" name="token" rows="3" placeholder="Enter your JWT token"><?php echo htmlspecialchars($token); ?></textarea>
                                    <small class="form-text text-muted">Format: header.payload.signature</small>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-check-circle"></i> Verify Token
                                </button>
                            </form>
                            
                            <?php if ($result !== null): ?>
                                <hr>
                                <div class="mt-3">
                                    <h5>Verification Result:</h5>
                                    <div class="border p-3 bg-light">
                                        <pre><?php echo htmlspecialchars($result); ?></pre>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="alert alert-warning mt-3">
                                <strong>Tip:</strong> Some servers incorrectly trust tokens with the "none" algorithm.
                                <br>Try decoding the token, changing alg to "none", removing the signature, and submitting.
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
            return ['error' => 'Invalid JWT format. Expected: header.payload.signature'];
        }
        
        list($headerB64, $payloadB64, $signatureB64) = $parts;
        
        // Decode header
        $headerJson = base64_decode(str_replace(['-', '_'], ['+', '/'], $headerB64));
        $header = json_decode($headerJson, true);
        
        if (!$header) {
            return ['error' => 'Invalid JWT header'];
        }
        
        // Decode payload
        $payloadJson = base64_decode(str_replace(['-', '_'], ['+', '/'], $payloadB64));
        $payload = json_decode($payloadJson, true);
        
        if (!$payload) {
            return ['error' => 'Invalid JWT payload'];
        }
        
        // INTENTIONAL VULNERABILITY: Accept "none" algorithm
        if (isset($header['alg']) && strtoupper($header['alg']) === 'NONE') {
            if (isset($payload['role']) && $payload['role'] === 'admin') {
                $this->markCompleted();
                return [
                    'result' => "SUCCESS! Token accepted with 'none' algorithm!\n\nHeader: " . json_encode($header, JSON_PRETTY_PRINT) . "\n\nPayload: " . json_encode($payload, JSON_PRETTY_PRINT) . "\n\nVULNERABILITY EXPLOITED: Server accepted unsigned token with admin role!",
                    'success' => true
                ];
            }
            
            return [
                'result' => "Token accepted with 'none' algorithm (insecure!)\n\nHeader: " . json_encode($header, JSON_PRETTY_PRINT) . "\n\nPayload: " . json_encode($payload, JSON_PRETTY_PRINT) . "\n\nNote: The server is accepting tokens without signature verification!",
                'success' => false
            ];
        }
        
        // Check for HS256/RS256 confusion (simplified)
        $result = "Token verified successfully!\n\n";
        $result .= "Header: " . json_encode($header, JSON_PRETTY_PRINT) . "\n\n";
        $result .= "Payload: " . json_encode($payload, JSON_PRETTY_PRINT);
        
        if (isset($payload['role']) && $payload['role'] === 'admin') {
            $this->markCompleted();
            $result .= "\n\nVULNERABILITY EXPLOITED: Admin token detected!";
        }
        
        return [
            'result' => $result,
            'success' => isset($payload['role']) && $payload['role'] === 'admin'
        ];
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
        
        $signature = hash_hmac('sha256', "{$headerB64}.{$payloadB64}", $this->secretKey, true);
        $signatureB64 = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        return "{$headerB64}.{$payloadB64}.{$signatureB64}";
    }
}