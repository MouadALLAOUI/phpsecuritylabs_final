<?php

namespace Labs\Jwt\Challenges;

use App\Core\BaseChallenge;
use App\Core\Session;

/**
 * Level 1: JWT Algorithm Confusion Challenge
 * 
 * INTENTIONAL VULNERABILITY: This challenge demonstrates JWT algorithm confusion
 * by accepting tokens with weak or "none" algorithm.
 */
class Level1Basic extends BaseChallenge
{
    private $secretKey = 'weak_secret';
    public $token = '';
    public $result = null;
    public $error = null;
    public $solved = false;

    public function __construct()
    {
        parent::__construct();
        if (Session::get('jwt_lvl1_solved') === true) {
            $this->completed = true;
            $this->solved = true;
        }
    }
    
    public function render(): void
    {
        $this->token = $this->getInput('token', '');
        
        // Generate a default token if none provided
        if (empty($this->token)) {
            $this->token = $this->createToken('user', 'basic_user');
        }

        include_once ROOT . '/shared/header.php';
        include_once ROOT . '/shared/sidebar.php';
        
        echo '<div class="lg:ml-64 p-6 min-h-[80vh] theme-transition">';
        include ROOT . '/labs/jwt/views/level1.php';
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
                $this->error = 'Invalid JWT format. Expected: header.payload.signature';
                return;
            }
            
            list($headerB64, $payloadB64, $signatureB64) = $parts;
            
            // Decode header
            $headerJson = base64_decode(str_replace(['-', '_'], ['+', '/'], $headerB64));
            $header = json_decode($headerJson, true);
            
            if (!$header) {
                $this->error = 'Invalid JWT header';
                return;
            }
            
            // Decode payload
            $payloadJson = base64_decode(str_replace(['-', '_'], ['+', '/'], $payloadB64));
            $payload = json_decode($payloadJson, true);
            
            if (!$payload) {
                $this->error = 'Invalid JWT payload';
                return;
            }
            
            // INTENTIONAL VULNERABILITY: Accept "none" algorithm
            if (isset($header['alg']) && strtoupper($header['alg']) === 'NONE') {
                if (isset($payload['role']) && $payload['role'] === 'admin') {
                    $this->markCompleted('jwt', 'lvl1');
                    Session::set('jwt_lvl1_solved', true);
                    $this->completed = true;
                    $this->solved = true;
                    $this->result = "SUCCESS! Token accepted with 'none' algorithm!\n\nHeader: " . json_encode($header, JSON_PRETTY_PRINT) . "\n\nPayload: " . json_encode($payload, JSON_PRETTY_PRINT) . "\n\nVULNERABILITY EXPLOITED: Server accepted unsigned token with admin role!\n\nFLAG{JWT_NONE_ALGORITHM_BREACHED}";
                    return;
                }
                
                $this->result = "Token accepted with 'none' algorithm (insecure!)\n\nHeader: " . json_encode($header, JSON_PRETTY_PRINT) . "\n\nPayload: " . json_encode($payload, JSON_PRETTY_PRINT) . "\n\nNote: The server is accepting tokens without signature verification, but you need to forge 'role' => 'admin' to breach the console.";
                return;
            }
            
            // Check for HS256/RS256 confusion (simplified)
            $result = "Token verified successfully!\n\n";
            $result .= "Header: " . json_encode($header, JSON_PRETTY_PRINT) . "\n\n";
            $result .= "Payload: " . json_encode($payload, JSON_PRETTY_PRINT);
            
            if (isset($payload['role']) && $payload['role'] === 'admin') {
                $this->markCompleted('jwt', 'lvl1');
                Session::set('jwt_lvl1_solved', true);
                $this->completed = true;
                $this->solved = true;
                $result .= "\n\nVULNERABILITY EXPLOITED: Admin token detected!\n\nFLAG{JWT_NONE_ALGORITHM_BREACHED}";
            }
            
            $this->result = $result;
        }
    }
    
    public function validate(): bool
    {
        return Session::get('jwt_lvl1_solved') === true;
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