<?php
/**
 * REST API Endpoints for PHP Security Labs
 * 
 * Usage:
 *   GET  /api.php?action=progress&lab=xss    - Get user progress for a lab
 *   GET  /api.php?action=leaderboard         - Get top 10 leaderboard
 *   GET  /api.php?action=challenge&lab=xss&lvl=lvl1 - Get challenge info
 *   
 * Returns JSON responses. Requires authentication for user-specific data.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/index.php';

use App\Core\Auth;
use App\Core\Database;

$response = ['success' => false, 'data' => null, 'error' => null];

try {
    $auth = new Auth();
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'progress':
            if (!$auth->isAuthenticated()) {
                throw new Exception('Authentication required', 401);
            }
            $lab = $_GET['lab'] ?? null;
            $db = Database::getInstance('app');
            
            if ($lab) {
                $sql = "SELECT lab_name, challenge, completed_at FROM lab_progress 
                        WHERE user_id = :user_id AND lab_name = :lab_name AND completed = 1";
                $stmt = $db->query($sql, ['user_id' => $auth->getUserId(), 'lab_name' => $lab]);
                $response['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $response['data'] = $auth->getCompletedChallenges();
            }
            break;
            
        case 'leaderboard':
            $limit = min((int)($_GET['limit'] ?? 10), 100);
            $response['data'] = Auth::getLeaderboard($limit);
            break;
            
        case 'challenge':
            $lab = $_GET['lab'] ?? '';
            $lvl = $_GET['lvl'] ?? '';
            if (!$lab || !$lvl) {
                throw new Exception('Lab and level parameters required', 400);
            }
            $mapFile = __DIR__ . "/labs/{$lab}/challenge_map.php";
            if (!file_exists($mapFile)) {
                throw new Exception('Lab not found', 404);
            }
            $map = require $mapFile;
            if (!isset($map[$lvl])) {
                throw new Exception('Level not found', 404);
            }
            $class = $map[$lvl];
            if (!class_exists($class)) {
                throw new Exception('Challenge class not found', 500);
            }
            $challenge = new $class();
            $response['data'] = [
                'title' => $challenge->getTitle(),
                'description' => $challenge->getDescription(),
                'hint' => $challenge->getHint()
            ];
            break;
            
        case 'user':
            if (!$auth->isAuthenticated()) {
                throw new Exception('Authentication required', 401);
            }
            $response['data'] = $auth->getUser();
            break;
            
        default:
            throw new Exception('Invalid action. Available: progress, leaderboard, challenge, user', 400);
    }
    
    $response['success'] = true;
    
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    http_response_code($e->getCode() ?: 500);
}

echo json_encode($response);
