<?php
/**
 * User Registration
 *
 * This file handles new user registration, including form validation (server-side),
 * processing user input, and storing new user data in the database.
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

// --- Form Submission Handling ---
$errors = [];
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // --- Sanitize and Validate Inputs ---
    $full_name = sanitize_input($_POST['full_name']);
    $username = sanitize_input($_POST['username']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password']; // Do not trim passwords
    $confirm_password = $_POST['confirm_password'];
    $membership_type = sanitize_input($_POST['membership_type']);
    $terms = isset($_POST['terms']);

    // --- Validation Rules ---
    if (empty($full_name)) {
        $errors[] = "Full Name is required.";
    }
    if (empty($username)) {
        $errors[] = "Username is required.";
    } elseif (strlen($username) < 5 || strlen($username) > 15) {
        $errors[] = "Username must be between 5 and 15 characters.";
    }
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must be at least 8 characters long and contain both letters and numbers.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    if (empty($membership_type)) {
        $errors[] = "Membership Type is required.";
    }
    if (!$terms) {
        $errors[] = "You must accept the Terms and Conditions.";
    }

    // --- Profile Picture Upload ---
    $profile_picture = 'default_profile.jpg'; // Default value
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png'];
        $max_size = 1 * 1024 * 1024; // 1MB

        if (in_array($_FILES['profile_picture']['type'], $allowed_types) && $_FILES['profile_picture']['size'] <= $max_size) {
            $upload_dir = 'images/';
            $profile_picture = uniqid() . '-' . basename($_FILES['profile_picture']['name']);
            if (!move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_dir . $profile_picture)) {
                $errors[] = "Failed to upload profile picture.";
                $profile_picture = 'default_profile.jpg'; // Reset to default on failure
            }
        } else {
            $errors[] = "Invalid file type or size for profile picture (JPG/PNG, max 1MB).";
        }
    }

    // --- Check for Existing User ---
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = "Username or email already exists.";
        }
    }

    // --- Insert User into Database ---
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (full_name, username, email, password, membership_type, profile_picture) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute([$full_name, $username, $email, $hashed_password, $membership_type, $profile_picture]);
            $success_message = "Registration successful! You can now <a href='login.php'>log in</a>.";
            // Optionally, redirect to login page
            // redirect('login.php');
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Library Management System</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/scripts.js" defer></script>
</head>
<body>
    <header>
        <h1>Register for a Library Account</h1>
    </header>
    <main>
        <form action="register.php" method="post" enctype="multipart/form-data" id="register-form">
            <?php if (!empty($errors)): ?>
                <div class="errors">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <?php if ($success_message): ?>
                <div class="success">
                    <p><?php echo $success_message; ?></p>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required minlength="5" maxlength="15">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="8">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <span id="password-match-error" class="error-message"></span>
            </div>
            <div class="form-group">
                <label for="membership_type">Membership Type</label>
                <select id="membership_type" name="membership_type" required>
                    <option value="">--Select--</option>
                    <option value="Student">Student</option>
                    <option value="Faculty">Faculty</option>
                    <option value="Public">Public</option>
                </select>
            </div>
            <div class="form-group">
                <label for="profile_picture">Profile Picture (Optional)</label>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/jpeg, image/png">
            </div>
            <div class="form-group">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">I accept the <a href="#">Terms and Conditions</a></label>
            </div>
            <button type="submit">Register</button>
        </form>
    </main>
</body>
</html>
