<?php
/**
 * stats.php — Statistics and match history page
 * Shows aggregate stats and a searchable/filterable match history table.
 * All user input goes through prepared statements.
 * All database values are escaped with htmlspecialchars() on output.
 */

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'config.php';
$pdo    = getDB();
$userId = $_SESSION['user_id'];

// --- Fetch aggregate stats ---
$stmt = $pdo->prepare(
    "SELECT games_played, player_wins, ai_wins, draws
     FROM game_stats WHERE user_id = ?"
);
$stmt->execute([$userId]);
$stats = $stmt->fetch();

$played = (int)$stats['games_played'];
$wins   = (int)$stats['player_wins'];
$losses = (int)$stats['ai_wins'];
$draws  = (int)$stats['draws'];
$winRate = $played > 0 ? round(($wins / $played) * 100, 1) : 0;

// --- Build match history query with optional filter ---
$filter  = $_GET['filter'] ?? 'all';
$search  = trim($_GET['search'] ?? '');
$params  = ["user_id" => $userId];

$query = "SELECT id, result, played_at FROM match_history WHERE user_id = ?";

if ($filter === 'win') {
    $query .= " AND result = 'win'";
} elseif ($filter === 'loss') {
    $query .= " AND result = 'loss'";
} elseif ($filter === 'draw') {
    $query .= " AND result = 'draw'";
}

if ($search !== '') {
    // Search by date (YYYY-MM-DD) — prepared statement
    $query .= " AND played_at LIKE ?";
    $params[] = "%" . $search . "%";
}

$query .= " ORDER BY played_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute(array_values($params));
$matches = $stmt->fetchAll();

$username = htmlspecialchars($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stats — Tic-Tac-Toe</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <span class="nav-brand">Tic-Tac-Toe</span>
        <div class="nav-links">
            <a href="game.php">Play</a>
            <a href="stats.php" class="active">Stats</a>
            <span class="nav-user"><?php echo $username; ?></span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>

    <div class="container stats-page">
        <h1>Your Statistics</h1>

        <!-- Aggregate stats cards -->
        <div class="stats-cards">
            <div class="card">
                <div class="card-number"><?php echo $played; ?></div>
                <div class="card-label">Games Played</div>
            </div>
            <div class="card card-win">
                <div class="card-number"><?php echo $wins; ?></div>
                <div class="card-label">Wins</div>
            </div>
            <div class="card card-loss">
                <div class="card-number"><?php echo $losses; ?></div>
                <div class="card-label">AI Wins</div>
            </div>
            <div class="card card-draw">
                <div class="card-number"><?php echo $draws; ?></div>
                <div class="card-label">Draws</div>
            </div>
            <div class="card">
                <div class="card-number"><?php echo $winRate; ?>%</div>
                <div class="card-label">Win Rate</div>
            </div>
        </div>

        <!-- Match history section -->
        <h2>Match History</h2>

        <form method="GET" action="stats.php" class="filter-form">
            <input type="text" name="search"
                   placeholder="Search by date (YYYY-MM-DD)"
                   value="<?php echo htmlspecialchars($search); ?>">
            <select name="filter">
                <option value="all"  <?php echo $filter === 'all'  ? 'selected' : ''; ?>>All</option>
                <option value="win"  <?php echo $filter === 'win'  ? 'selected' : ''; ?>>Wins</option>
                <option value="loss" <?php echo $filter === 'loss' ? 'selected' : ''; ?>>Losses</option>
                <option value="draw" <?php echo $filter === 'draw' ? 'selected' : ''; ?>>Draws</option>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="stats.php" class="btn btn-secondary">Clear</a>
        </form>

        <table class="history-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Result</th>
                    <th>Date &amp; Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($matches)): ?>
                    <tr><td colspan="3" class="empty-msg">No matches found.</td></tr>
                <?php else: ?>
                    <?php foreach ($matches as $i => $m): ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td>
                                <span class="badge badge-<?php echo htmlspecialchars($m['result']); ?>">
                                    <?php
                                    // Display result with proper capitalisation
                                    $resultLabels = ['win' => 'Player Win', 'loss' => 'AI Win', 'draw' => 'Draw'];
                                    echo htmlspecialchars($resultLabels[$m['result']] ?? $m['result']);
                                    ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($m['played_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <p class="total-showing">Showing <?php echo count($matches); ?> match(es)</p>
    </div>
</body>
</html>
