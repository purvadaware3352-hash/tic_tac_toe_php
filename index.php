<?php
/**
 * index.php — Landing page
 * Redirects logged-in users to the game, others to login.
 */

session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: game.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tic-Tac-Toe</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container landing">
        <h1>Tic-Tac-Toe</h1>
        <p class="tagline">Challenge the AI — can you beat Minimax?</p>
        <div class="btn-group">
            <a href="login.php" class="btn btn-primary">Login</a>
            <a href="register.php" class="btn btn-secondary">Register</a>
        </div>
    </div>
</body>
</html>
