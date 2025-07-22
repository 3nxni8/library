<?php
/**
 * Book Review Submission
 *
 * This file allows a logged-in user to submit a review for a book they have
 * previously borrowed. It validates the review and stores it in the database.
 *
 * PHP version 8.x
 *
 * @category   Reviews
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
$user_id = $_SESSION['user_id'];
$errors = [];
$success_message = '';

// --- Verify User has Borrowed the Book ---
$stmt = $pdo->prepare("SELECT 1 FROM borrow_requests WHERE user_id = ? AND book_id = ? AND status = 'Approved'");
$stmt->execute([$user_id, $book_id]);
if ($stmt->rowCount() == 0) {
    // For simplicity, we only check for approved requests. A real system might check for returned books.
    $errors[] = "You can only review books you have borrowed.";
}

// --- Fetch Book Details ---
if ($book_id > 0 && empty($errors)) {
    $stmt = $pdo->prepare("SELECT title FROM books WHERE id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch();
    if (!$book) {
        $errors[] = "Book not found.";
    }
} else {
    $errors[] = "Invalid book ID.";
}

// --- Form Submission Handling ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($errors)) {
    $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT, ["options" => ["min_range" => 1, "max_range" => 5]]);
    $review_text = sanitize_input($_POST['review_text']);

    // --- Validation ---
    if (!$rating) {
        $errors[] = "Rating must be between 1 and 5.";
    }
    if (strlen($review_text) < 50) {
        $errors[] = "Review text must be at least 50 characters long.";
    }

    // --- Image Upload ---
    $review_image = null;
    if (isset($_FILES['review_image']) && $_FILES['review_image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png'];
        $max_size = 2 * 1024 * 1024; // 2MB

        if (in_array($_FILES['review_image']['type'], $allowed_types) && $_FILES['review_image']['size'] <= $max_size) {
            $upload_dir = 'images/reviews/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $review_image = uniqid() . '-' . basename($_FILES['review_image']['name']);
            if (!move_uploaded_file($_FILES['review_image']['tmp_name'], $upload_dir . $review_image)) {
                $errors[] = "Failed to upload review image.";
                $review_image = null;
            }
        } else {
            $errors[] = "Invalid file type or size for review image (JPG/PNG, max 2MB).";
        }
    }

    // --- Insert Review into Database ---
    if (empty($errors)) {
        $sql = "INSERT INTO reviews (user_id, book_id, rating, review_text, image) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute([$user_id, $book_id, $rating, $review_text, $review_image]);

            // --- Log Email Simulation ---
            $log_message = sprintf("[%s] New review posted for book ID %d by user ID %d.\n", date('Y-m-d H:i:s'), $book_id, $user_id);
            file_put_contents('logs/email_logs.txt', $log_message, FILE_APPEND);

            $success_message = "Your review has been submitted successfully.";
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
    <title>Submit a Review - Library Management System</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h1>Submit a Review</h1>
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
                <p><a href="catalog.php">Return to Catalog</a>.</p>
            </div>
        <?php else: ?>
            <h2>You are reviewing: <?php echo htmlspecialchars($book['title']); ?></h2>
            <form action="review.php?book_id=<?php echo $book_id; ?>" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="rating">Rating (1-5)</label>
                    <input type="number" id="rating" name="rating" min="1" max="5" required>
                </div>
                <div class="form-group">
                    <label for="review_text">Review (min 50 characters)</label>
                    <textarea id="review_text" name="review_text" rows="6" minlength="50" required></textarea>
                </div>
                <div class="form-group">
                    <label for="review_image">Upload Image (Optional)</label>
                    <input type="file" id="review_image" name="review_image" accept="image/jpeg, image/png">
                </div>
                <button type="submit">Submit Review</button>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>
