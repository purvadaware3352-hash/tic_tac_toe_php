<?php
/**
 * profile.php — Update account (edit username / change password)
 * Additional feature: UPDATE — allows a logged-in user to edit their
 * profile details. All input is validated server-side and all queries
 * use prepared statements.
 */

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'config.php';
$pdo    = getDB();
$userId = $_SESSION['user_id'];

$message = '';
$error   = '';
$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = trim($_POST['username'] ?? '');
    $newPassword = $_POST['password'] ?? '';
    $confirm     = $_POST['confirm'] ?? '';

    // --- Server-side validation ---
    $errors = [];
    if ($newUsername === '') {
        $errors[] = "Username cannot be empty.";
    } elseif (strlen($newUsername) < 3 || strlen($newUsername) > 50) {
        $errors[] = "Username must be 3-50 characters.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $newUsername)) {
        $errors[] = "Username can only contain letters, numbers, and underscores.";
    }

    if ($newPassword !== '') {
        if (strlen($newPassword) < 6) {
            $errors[] = "New password must be at least 6 characters.";
        } elseif ($newPassword !== $confirm) {
            $errors[] = "Passwords do not match.";
        }
    }

    if (empty($errors)) {
        try {
            // Check that the new username is not taken by another user
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
            $stmt->execute([$newUsername, $userId]);
            if ($stmt->fetch()) {
                $error = "Username already taken. Choose another.";
            } else {
                $pdo->beginTransaction();

                if ($newUsername !== $_SESSION['username']) {
                    $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
                    $stmt->execute([$newUsername, $userId]);
                }

                if ($newPassword !== '') {
                    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->execute([$hash, $userId]);
                }

                $pdo->commit();

                $_SESSION['username'] = $newUsername;
                $username = $newUsername;
                $message = "Profile updated successfully.";
            }
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = "Failed to update profile. Please try again.";
        }
    } else {
        $error = implode(" ", $errors);
    }
}

$usernameEsc = htmlspecialchars($username);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile — Tic-Tac-Toe</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <span class="nav-brand">Tic-Tac-Toe</span>
        <div class="nav-links">
            <a href="game.php">Play</a>
            <a href="stats.php">Stats</a>
            <a href="profile.php" class="active">Profile</a>
            <span class="nav-user"><?php echo $usernameEsc; ?></span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>

    <div class="container auth-card">
        <h1>Update Profile</h1>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="profile.php">
            <label for="username">Username</label>
            <input type="text" id="username" name="username"
                   value="<?php echo $usernameEsc; ?>"
                   placeholder="3-50 chars, letters/numbers/_"
                   required maxlength="50">

            <label for="password">New Password <span class="opt-note">(leave blank to keep current)</span></label>
            <input type="password" id="password" name="password"
                   placeholder="Min 6 characters">

            <label for="confirm">Confirm New Password</label>
            <input type="password" id="confirm" name="confirm"
                   placeholder="Re-enter new password">

            <button type="submit" class="btn btn-primary full-width">Update Profile</button>
        </form>
    </div>
</body>
</html>