<?php
/**
 * Add/Edit Book
 *
 * This file provides a form for administrators to add a new book or edit an
 * existing one. It handles form submission, validation, and database operations.
 *
 * PHP version 8.x
 *
 * @category   Admin
 * @package    LibraryManagementSystem
 * @author     Your Name <your.email@example.com>
 * @license    MIT License
 * @link       https://github.com/yourusername/library-management-system
 */

require_once 'config.php';

// --- Admin Access Control ---
if (!is_admin()) {
    redirect('login.php');
}

$book_id = $_GET['id'] ?? 0;
$is_editing = $book_id > 0;
$book = ['title' => '', 'author' => '', 'genre' => '', 'availability' => 'Available', 'cover_image' => ''];
$errors = [];

// --- Fetch Book for Editing ---
if ($is_editing) {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch();
    if (!$book) {
        redirect('admin.php?action=manage_books'); // Book not found
    }
}

// --- Form Submission Handling ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = sanitize_input($_POST['title']);
    $author = sanitize_input($_POST['author']);
    $genre = sanitize_input($_POST['genre']);
    $availability = sanitize_input($_POST['availability']);

    // --- Validation ---
    if (empty($title) || empty($author) || empty($genre)) {
        $errors[] = "Title, Author, and Genre are required.";
    }

    // --- Cover Image Upload ---
    $cover_image = $book['cover_image'];
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png'];
        $max_size = 2 * 1024 * 1024; // 2MB

        if (in_array($_FILES['cover_image']['type'], $allowed_types) && $_FILES['cover_image']['size'] <= $max_size) {
            $upload_dir = 'images/';
            $new_image_name = uniqid() . '-' . basename($_FILES['cover_image']['name']);
            if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $upload_dir . $new_image_name)) {
                // Delete old image if it's not the default
                if ($is_editing && $cover_image && $cover_image !== 'default_cover.jpg') {
                    unlink($upload_dir . $cover_image);
                }
                $cover_image = $new_image_name;
            } else {
                $errors[] = "Failed to upload new cover image.";
            }
        } else {
            $errors[] = "Invalid file type or size for cover image (JPG/PNG, max 2MB).";
        }
    }

    // --- Database Operation ---
    if (empty($errors)) {
        if ($is_editing) {
            $sql = "UPDATE books SET title = ?, author = ?, genre = ?, availability = ?, cover_image = ? WHERE id = ?";
            $params = [$title, $author, $genre, $availability, $cover_image, $book_id];
        } else {
            $sql = "INSERT INTO books (title, author, genre, availability, cover_image) VALUES (?, ?, ?, ?, ?)";
            $params = [$title, $author, $genre, $availability, $cover_image ?: 'default_cover.jpg'];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        redirect('admin.php?action=manage_books');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_editing ? 'Edit' : 'Add'; ?> Book - Admin</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h1><?php echo $is_editing ? 'Edit Book' : 'Add New Book'; ?></h1>
    </header>
    <main>
        <?php if (!empty($errors)): ?>
            <div class="errors">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="edit_book.php<?php echo $is_editing ? '?id=' . $book_id : ''; ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="author">Author</label>
                <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($book['author']); ?>" required>
            </div>
            <div class="form-group">
                <label for="genre">Genre</label>
                <input type="text" id="genre" name="genre" value="<?php echo htmlspecialchars($book['genre']); ?>" required>
            </div>
            <div class="form-group">
                <label for="availability">Availability</label>
                <select id="availability" name="availability">
                    <option value="Available" <?php echo ($book['availability'] === 'Available') ? 'selected' : ''; ?>>Available</option>
                    <option value="Borrowed" <?php echo ($book['availability'] === 'Borrowed') ? 'selected' : ''; ?>>Borrowed</option>
                </select>
            </div>
            <div class="form-group">
                <label for="cover_image">Cover Image</label>
                <input type="file" id="cover_image" name="cover_image" accept="image/jpeg, image/png">
                <?php if ($is_editing && $book['cover_image']): ?>
                    <p>Current: <img src="images/<?php echo htmlspecialchars($book['cover_image']); ?>" alt="Cover" width="50"></p>
                <?php endif; ?>
            </div>
            <button type="submit"><?php echo $is_editing ? 'Update' : 'Add'; ?> Book</button>
        </form>
    </main>
</body>
</html>
