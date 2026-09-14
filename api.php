<?php
/**
 * api.php — AJAX endpoint
 * POST /api.php?action=save_result  → saves match result to DB
 * Returns JSON for the frontend to consume.
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Not logged in"]);
    exit;
}

require_once 'config.php';
$pdo    = getDB();
$userId = $_SESSION['user_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// -----------------------------------------------------------
// Save match result
// -----------------------------------------------------------
if ($action === 'save_result') {
    $result = $_POST['result'] ?? '';

    // Validate result server-side
    if (!in_array($result, ['win', 'loss', 'draw'])) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid result value"]);
        exit;
    }

    $pdo->beginTransaction();
    try {
        // Update aggregate stats — prepared statement
        $fieldMap = [
            'win'  => 'player_wins',
            'loss' => 'ai_wins',
            'draw' => 'draws',
        ];
        $field = $fieldMap[$result];

        $stmt = $pdo->prepare(
            "UPDATE game_stats
             SET games_played = games_played + 1,
                 {$field} = {$field} + 1,
                 last_played_at = NOW()
             WHERE user_id = ?"
        );
        $stmt->execute([$userId]);

        // Insert into match history — prepared statement
        $stmt = $pdo->prepare(
            "INSERT INTO match_history (user_id, result, played_at)
             VALUES (?, ?, NOW())"
        );
        $stmt->execute([$userId, $result]);

        $pdo->commit();
        echo json_encode(["success" => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(["error" => "Failed to save result"]);
    }
    exit;
}

// -----------------------------------------------------------
// Any other action
// -----------------------------------------------------------
http_response_code(400);
echo json_encode(["error" => "Unknown action"]);
