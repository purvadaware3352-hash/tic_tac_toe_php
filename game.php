<?php
/**
 * game.php — Main game page
 * Requires login. Renders the board and loads game.js for Minimax AI.
 */

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'config.php';
$pdo    = getDB();
$userId = $_SESSION['user_id'];

// Fetch current stats for display
$stmt = $pdo->prepare(
    "SELECT games_played, player_wins, ai_wins, draws
     FROM game_stats WHERE user_id = ?"
);
$stmt->execute([$userId]);
$stats = $stmt->fetch();

$username = htmlspecialchars($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play — Tic-Tac-Toe</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Top navigation bar -->
    <nav class="navbar">
        <span class="nav-brand">Tic-Tac-Toe</span>
        <div class="nav-links">
            <a href="game.php" class="active">Play</a>
            <a href="stats.php">Stats</a>
            <span class="nav-user"><?php echo $username; ?></span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>

    <div class="container game-page">

        <!-- Quick stats strip -->
        <div class="stats-strip">
            <div class="stat-pill">Played: <strong><?php echo (int)$stats['games_played']; ?></strong></div>
            <div class="stat-pill win">Wins: <strong><?php echo (int)$stats['player_wins']; ?></strong></div>
            <div class="stat-pill loss">Losses: <strong><?php echo (int)$stats['ai_wins']; ?></strong></div>
            <div class="stat-pill draw">Draws: <strong><?php echo (int)$stats['draws']; ?></strong></div>
        </div>

        <!-- Status banner -->
        <div id="status" class="status-banner">Your turn — place X</div>

        <!-- 3×3 game board -->
        <div class="board" id="board">
            <div class="cell" data-index="0"></div>
            <div class="cell" data-index="1"></div>
            <div class="cell" data-index="2"></div>
            <div class="cell" data-index="3"></div>
            <div class="cell" data-index="4"></div>
            <div class="cell" data-index="5"></div>
            <div class="cell" data-index="6"></div>
            <div class="cell" data-index="7"></div>
            <div class="cell" data-index="8"></div>
        </div>

        <button id="newGameBtn" class="btn btn-primary" style="display:none;">New Game</button>
    </div>

    <script>
        // Pass PHP session data to JavaScript
        var CURRENT_USER_ID = <?php echo $userId; ?>;
    </script>
    <script src="game.js"></script>
</body>
</html>
