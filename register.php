<?php
/**
 * register.php — User registration
 * Validates input server-side, hashes password, stores in MySQL.
 */

session_start();
require_once 'config.php';

$message = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    // --- Server-side validation ---
    if ($username === '' || $password === '' || $confirm === '') {
        $error = "All fields are required.";
    } elseif (strlen($username) < 3 || strlen($username) > 50) {
        $error = "Username must be 3-50 characters.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error = "Username can only contain letters, numbers, and underscores.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $pdo = getDB();

        // Check if username already exists — prepared statement
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Username already taken. Choose another.";
        } else {
            // Hash password and insert — prepared statement
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $hash]);
            $newUserId = $pdo->lastInsertId();

            // Create empty stats row for this user
            $stmt = $pdo->prepare("INSERT INTO game_stats (user_id) VALUES (?)");
            $stmt->execute([$newUserId]);

            // Auto-login after registration
            $_SESSION['user_id']   = $newUserId;
            $_SESSION['username']  = $username;
            header("Location: game.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Tic-Tac-Toe</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container auth-card">
        <h1>Register</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <label for="username">Username</label>
            <input type="text" id="username" name="username"
                   value="<?php echo htmlspecialchars($username ?? ''); ?>"
                   placeholder="3-50 chars, letters/numbers/_"
                   required maxlength="50">

            <label for="password">Password</label>
            <input type="password" id="password" name="password"
                   placeholder="Min 6 characters" required>

            <label for="confirm">Confirm Password</label>
            <input type="password" id="confirm" name="confirm"
                   placeholder="Re-enter password" required>

            <button type="submit" class="btn btn-primary full-width">Create Account</button>
        </form>

        <p class="switch-link">Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>
</html>
