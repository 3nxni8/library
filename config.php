<?php
/**
 * Configuration and Database Connection
 *
 * This file contains the database connection settings and initializes the PDO object
 * for database interactions. It also starts the session for user authentication.
 *
 * PHP version 8.x
 *
 * @category   Configuration
 * @package    LibraryManagementSystem
 * @author     Your Name <your.email@example.com>
 * @license    MIT License
 * @link       https://github.com/yourusername/library-management-system
 */

// --- Database Configuration ---
/**
 * @var string DB_HOST The database host (e.g., "localhost" or "127.0.0.1").
 */
define('DB_HOST', 'localhost');

/**
 * @var string DB_NAME The name of the database.
 */
define('DB_NAME', 'library');

/**
 * @var string DB_USER The username for database access.
 */
define('DB_USER', 'root');

/**
 * @var string DB_PASS The password for the database user.
 */
define('DB_PASS', '');

/**
 * @var string DB_CHARSET The character set for the database connection.
 */
define('DB_CHARSET', 'utf8mb4');

// --- DSN (Data Source Name) ---
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

// --- PDO Options ---
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// --- Create PDO Instance ---
try {
    /**
     * @var PDO $pdo The PDO object for database interactions.
     */
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (\PDOException $e) {
    // In a real-world application, log this error and show a generic error message
    // For development, we can show the detailed error
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// --- Start Session ---
// Start the session if it's not already started. This is crucial for managing user
// login state across different pages.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * --- Helper Functions (Optional) ---
 */

/**
 * Sanitizes user input to prevent XSS attacks.
 *
 * @param string $data The input data to sanitize.
 * @return string The sanitized data.
 */
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

/**
 * Checks if a user is logged in.
 *
 * @return bool True if the user is logged in, false otherwise.
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Checks if the logged-in user is an admin.
 *
 * @return bool True if the user is an admin, false otherwise.
 */
function is_admin() {
    return is_logged_in() && isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';
}

/**
 * Redirects to a specified URL.
 *
 * @param string $url The URL to redirect to.
 * @return void
 */
function redirect($url) {
    header("Location: " . $url);
    exit;
}
?>
