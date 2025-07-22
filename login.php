<?php
/**
 * User Login
 *
 * This file handles user login, authenticates credentials against the database,
 * and manages the user session.
 *
 * PHP version 8.x
 *
 * @category   Authentication
 * @package    LibraryManagementSystem
 * @author     Your Name <your.email@example.com>
 * @license    MIT License
 * @link       https://github.com/yourusername/library-management-system
 */

require_once 'config.php';

// --- Redirect if already logged in ---
if (is_logged_in()) {
    if (is_admin()) {
        redirect('admin.php');
    } else {
        redirect('user_dashboard.php');
    }
}

// --- Form Submission Handling ---
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // --- Sanitize and Validate Inputs ---
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password']; // Do not trim passwords

    if (empty($username) || empty($password)) {
        $errors[] = "Username and password are required.";
    }

    // --- Authenticate User ---
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // --- Set Session Variables ---
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // --- Redirect based on role ---
            if ($user['role'] === 'Admin') {
                redirect('admin.php');
            } else {
                redirect('user_dashboard.php');
            }
        } else {
            $errors[] = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Library Management System</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h1>Login to Your Library Account</h1>
    </header>
    <main>
        <form action="login.php" method="post" id="login-form">
            <?php if (!empty($errors)): ?>
                <div class="errors">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
    </main>
</body>
</html>
