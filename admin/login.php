<?php
/**
 * Admin login page.
 */

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

// Already logged in? Go straight to the dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = cleanText($_POST['username'] ?? '', 50);
    $password = (string) ($_POST['password'] ?? '');

    $stmt = getDb()->prepare('SELECT id, username, password_hash FROM admins WHERE username = ?');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        // Create a new session ID after login to prevent session fixation
        session_regenerate_id(true);
        $_SESSION['admin_id']   = (int) $admin['id'];
        $_SESSION['admin_name'] = $admin['username'];
        header('Location: index.php');
        exit;
    }

    // Same message for wrong username or password
    $error = 'Incorrect username or password.';
    sleep(1); // Slows down repeated guessing
}

renderHeader('Login');
?>
<div class="card narrow">
    <h1>Admin Login</h1>

    <?php if ($error !== ''): ?>
        <p class="notice error"><?= e($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Username
            <input type="text" name="username" required autofocus>
        </label>
        <label>Password
            <input type="password" name="password" required>
        </label>
        <button type="submit" class="btn">Log In</button>
    </form>
</div>
<?php renderFooter(); ?>
