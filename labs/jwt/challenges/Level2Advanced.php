<?php

namespace Labs\Jwt\Challenges;

use App\Core\BaseChallenge;
use App\Core\Session;

/**
 * Level 2: JWT Weak Secret Key Challenge
 * 
 * INTENTIONAL VULNERABILITY: This challenge demonstrates brute-forcing
 * weak JWT secret keys.
 */
class Level2Advanced extends BaseChallenge
{
    private $weakSecret = 'password123';
    public $token = '';
    public $result = null;
    public $error = null;
    public $solved = false;

    public function __construct()
    {
        parent::__construct();
        if (Session::get('jwt_lvl2_solved') === true) {
            $this->completed = true;
            $this->solved = true;
        }
    }
    
    public function render(): void
    {
        $this->token = $this->getInput('token', '');
        
        // Generate a default token if none provided
        if (empty($this->token)) {
            $this->token = $this->createToken('user', 'standard_user');
        }

        include_once ROOT . '/shared/header.php';
        include_once ROOT . '/shared/sidebar.php';
        
        echo '<div class="lg:ml-64 p-6 min-h-[80vh] theme-transition">';
        include ROOT . '/labs/jwt/views/level2.php';
        echo '</div>';
        
        include_once ROOT . '/shared/footer.php';
    }
    
    public function handle(): void
    {
        if ($this->isPost()) {
            $this->token = $this->getInput('token', '');
            
            if (empty($this->token)) {
                $this->error = 'No token provided';
                return;
            }
            
            $parts = explode('.', $this->token);
            
            if (count($parts) !== 3) {
                $this->error = 'Invalid JWT format';
                return;
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
                $this->result = "Token signature does not match any known weak secret.\n\nTry forging a new token with one of the common secrets listed below.";
                return;
            }
            
            // Decode payload to check role
            $payloadJson = base64_decode(str_replace(['-', '_'], ['+', '/'], $payloadB64));
            $payload = json_decode($payloadJson, true);
            
            if (!$payload) {
                $this->error = 'Invalid payload';
                return;
            }
            
            $result = "SUCCESS! Cracked secret key: '{$foundSecret}'\n\n";
            $result .= "Payload: " . json_encode($payload, JSON_PRETTY_PRINT);
            
            if (isset($payload['role']) && $payload['role'] === 'admin') {
                $this->markCompleted('jwt', 'lvl2');
                Session::set('jwt_lvl2_solved', true);
                $this->completed = true;
                $this->solved = true;
                $result .= "\n\nVULNERABILITY EXPLOITED: You successfully cracked the weak secret and forged an admin token!\n\nFLAG{JWT_WEAK_SECRET_KEY_CRACKED}";
                $this->result = $result;
                return;
            }
            
            $result .= "\n\nNote: You found the secret, but the token doesn't have admin role. Forge a new token with role=admin.";
            $this->result = $result;
        }
    }
    
    public function validate(): bool
    {
        return Session::get('jwt_lvl2_solved') === true;
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