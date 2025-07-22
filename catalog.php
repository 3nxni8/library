<?php
/**
 * Book Catalog
 *
 * This file displays the library's book collection. It allows users to view,
 * search, filter, and sort books. Logged-in users will see options to borrow books.
 *
 * PHP version 8.x
 *
 * @category   Catalog
 * @package    LibraryManagementSystem
 * @author     Your Name <your.email@example.com>
 * @license    MIT License
 * @link       https://github.com/yourusername/library-management-system
 */

require_once 'config.php';

// --- Fetch Books with Filtering, Sorting, and Searching ---
$sql = "SELECT * FROM books WHERE 1=1";
$params = [];

// Search
if (!empty($_GET['search'])) {
    $search_term = '%' . $_GET['search'] . '%';
    $sql .= " AND (title LIKE ? OR author LIKE ?)";
    $params[] = $search_term;
    $params[] = $search_term;
}

// Filter by genre
if (!empty($_GET['genre'])) {
    $sql .= " AND genre = ?";
    $params[] = $_GET['genre'];
}

// Filter by availability
if (!empty($_GET['availability'])) {
    $sql .= " AND availability = ?";
    $params[] = $_GET['availability'];
}

// Sort
$sort_order = 'added_date DESC'; // Default sort
if (!empty($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'title_asc':
            $sort_order = 'title ASC';
            break;
        case 'author_asc':
            $sort_order = 'author ASC';
            break;
        case 'added_date_desc':
            $sort_order = 'added_date DESC';
            break;
    }
}
$sql .= " ORDER BY $sort_order";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll();

// Fetch all genres for the filter dropdown
$genres = $pdo->query("SELECT DISTINCT genre FROM books ORDER BY genre")->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Catalog - Library Management System</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h1>Book Catalog</h1>
        <nav>
            <?php if (is_logged_in()): ?>
                <a href="user_dashboard.php">My Dashboard</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
        <form action="catalog.php" method="get" class="filter-sort-form">
            <input type="text" name="search" placeholder="Search by title or author..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            <select name="genre">
                <option value="">All Genres</option>
                <?php foreach ($genres as $genre): ?>
                    <option value="<?php echo htmlspecialchars($genre); ?>" <?php echo (($_GET['genre'] ?? '') == $genre) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($genre); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="availability">
                <option value="">All</option>
                <option value="Available" <?php echo (($_GET['availability'] ?? '') == 'Available') ? 'selected' : ''; ?>>Available</option>
                <option value="Borrowed" <?php echo (($_GET['availability'] ?? '') == 'Borrowed') ? 'selected' : ''; ?>>Borrowed</option>
            </select>
            <select name="sort">
                <option value="added_date_desc" <?php echo (($_GET['sort'] ?? '') == 'added_date_desc') ? 'selected' : ''; ?>>Newest First</option>
                <option value="title_asc" <?php echo (($_GET['sort'] ?? '') == 'title_asc') ? 'selected' : ''; ?>>Title (A-Z)</option>
                <option value="author_asc" <?php echo (($_GET['sort'] ?? '') == 'author_asc') ? 'selected' : ''; ?>>Author (A-Z)</option>
            </select>
            <button type="submit">Apply</button>
        </form>

        <section class="book-grid">
            <?php if (count($books) > 0): ?>
                <?php foreach ($books as $book): ?>
                    <article class="book-card">
                        <img src="images/<?php echo htmlspecialchars($book['cover_image']); ?>" alt="Cover of <?php echo htmlspecialchars($book['title']); ?>">
                        <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                        <p>by <?php echo htmlspecialchars($book['author']); ?></p>
                        <p>Genre: <?php echo htmlspecialchars($book['genre']); ?></p>
                        <p>Status: <span class="<?php echo strtolower($book['availability']); ?>"><?php echo htmlspecialchars($book['availability']); ?></span></p>
                        <?php if (is_logged_in() && $book['availability'] == 'Available'): ?>
                            <a href="borrow.php?book_id=<?php echo $book['id']; ?>" class="btn">Borrow</a>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No books found matching your criteria.</p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
