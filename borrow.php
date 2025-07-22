<?php
/**
 * Book Borrowing Request
 *
 * This file handles the process for a logged-in user to request to borrow a book.
 * It validates the request and inserts a "Pending" borrow request into the database.
 *
 * PHP version 8.x
 *
 * @category   Borrowing
 * @package    LibraryManagementSystem
 * @author     Your Name <your.email@example.com>
 * @license    MIT License
 * @link       https://github.com/yourusername/library-management-system
 */

require_once 'config.php';

// --- Authentication Check ---
if (!is_logged_in()) {
    redirect('login.php');
}

$book_id = isset($_GET['book_id']) ? (int)$_GET['book_id'] : 0;
$errors = [];
$success_message = '';

// --- Fetch Book Details ---
if ($book_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ? AND availability = 'Available'");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch();
    if (!$book) {
        $errors[] = "This book is not available for borrowing or does not exist.";
    }
} else {
    $errors[] = "Invalid book ID.";
}

// --- Form Submission Handling ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($errors)) {
    $borrow_duration = sanitize_input($_POST['borrow_duration']);
    $message = sanitize_input($_POST['message']);
    $user_id = $_SESSION['user_id'];

    // --- Insert Borrow Request ---
    $sql = "INSERT INTO borrow_requests (user_id, book_id, borrow_duration, message) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([$user_id, $book_id, $borrow_duration, $message]);

        // --- Update Book Availability ---
        $update_stmt = $pdo->prepare("UPDATE books SET availability = 'Borrowed' WHERE id = ?");
        $update_stmt->execute([$book_id]);

        $success_message = "Your borrow request has been submitted and is pending approval.";
    } catch (PDOException $e) {
        $errors[] = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrow Book - Library Management System</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h1>Borrow a Book</h1>
    </header>
    <main>
        <?php if (!empty($errors)): ?>
            <div class="errors">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
                <p><a href="catalog.php">Return to Catalog</a></p>
            </div>
        <?php elseif ($success_message): ?>
            <div class="success">
                <p><?php echo $success_message; ?></p>
                <p><a href="catalog.php">Return to Catalog</a> or <a href="user_dashboard.php">view your requests</a>.</p>
            </div>
        <?php else: ?>
            <h2>You are requesting to borrow: <?php echo htmlspecialchars($book['title']); ?></h2>
            <p>by <?php echo htmlspecialchars($book['author']); ?></p>
            <form action="borrow.php?book_id=<?php echo $book_id; ?>" method="post">
                <div class="form-group">
                    <label for="borrow_duration">Borrow Duration</label>
                    <select id="borrow_duration" name="borrow_duration" required>
                        <option value="7">7 Days</option>
                        <option value="14">14 Days</option>
                        <option value="21">21 Days</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="message">Message (Optional)</label>
                    <textarea id="message" name="message" rows="4"></textarea>
                </div>
                <button type="submit">Submit Request</button>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>
