<?php
/**
 * Admin Panel
 *
 * This file provides a dashboard for administrators to manage books (add, edit, delete)
 * and handle borrow requests (approve, reject). Access is restricted to users with
 * the 'Admin' role.
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

// --- Action Handling ---
$action = $_GET['action'] ?? 'view_requests';
$item_id = $_GET['id'] ?? 0;
$errors = [];
$success_message = '';

// Handle Borrow Request Actions
if (isset($_POST['request_action'])) {
    $request_id = (int)$_POST['request_id'];
    $new_status = $_POST['request_action'] === 'approve' ? 'Approved' : 'Rejected';

    $stmt = $pdo->prepare("UPDATE borrow_requests SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $request_id]);

    // Log Email Simulation
    $log_message = sprintf("[%s] Borrow request ID %d was %s.\n", date('Y-m-d H:i:s'), $request_id, strtolower($new_status));
    file_put_contents('logs/email_logs.txt', $log_message, FILE_APPEND);

    $success_message = "Request has been $new_status.";
}

// Handle Book Deletion
if ($action === 'delete_book' && $item_id > 0) {
    $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
    $stmt->execute([$item_id]);
    $success_message = "Book deleted successfully.";
    redirect('admin.php?action=manage_books');
}


// --- Data Fetching for Views ---
$borrow_requests = [];
$books = [];
$stats = [];

if ($action === 'view_requests') {
    $stmt = $pdo->query("SELECT br.*, u.username, b.title FROM borrow_requests br JOIN users u ON br.user_id = u.id JOIN books b ON br.book_id = b.id ORDER BY br.created_at DESC");
    $borrow_requests = $stmt->fetchAll();
} elseif ($action === 'manage_books') {
    $stmt = $pdo->query("SELECT * FROM books ORDER BY title");
    $books = $stmt->fetchAll();
} elseif ($action === 'view_stats') {
    $stats['total_borrows'] = $pdo->query("SELECT COUNT(*) FROM borrow_requests WHERE status = 'Approved'")->fetchColumn();
    $stats['top_books'] = $pdo->query("SELECT b.title, COUNT(br.id) as borrow_count FROM borrow_requests br JOIN books b ON br.book_id = b.id WHERE br.status = 'Approved' GROUP BY b.id ORDER BY borrow_count DESC LIMIT 5")->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Library Management System</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h1>Admin Panel</h1>
        <nav>
            <a href="admin.php?action=view_requests">Borrow Requests</a>
            <a href="admin.php?action=manage_books">Manage Books</a>
            <a href="admin.php?action=view_stats">View Stats</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <?php if ($success_message): ?>
            <div class="success"><p><?php echo $success_message; ?></p></div>
        <?php endif; ?>

        <?php if ($action === 'view_requests'): ?>
            <h2>Pending Borrow Requests</h2>
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Book</th>
                        <th>Duration</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($borrow_requests as $request): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($request['username']); ?></td>
                        <td><?php echo htmlspecialchars($request['title']); ?></td>
                        <td><?php echo htmlspecialchars($request['borrow_duration']); ?> days</td>
                        <td><?php echo date('Y-m-d', strtotime($request['created_at'])); ?></td>
                        <td><?php echo htmlspecialchars($request['status']); ?></td>
                        <td>
                            <?php if ($request['status'] === 'Pending'): ?>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                <button type="submit" name="request_action" value="approve">Approve</button>
                                <button type="submit" name="request_action" value="reject">Reject</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php elseif ($action === 'manage_books'): ?>
            <h2>Manage Books <a href="edit_book.php" class="btn-add">Add New Book</a></h2>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Genre</th>
                        <th>Availability</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $book): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($book['title']); ?></td>
                        <td><?php echo htmlspecialchars($book['author']); ?></td>
                        <td><?php echo htmlspecialchars($book['genre']); ?></td>
                        <td><?php echo htmlspecialchars($book['availability']); ?></td>
                        <td>
                            <a href="edit_book.php?id=<?php echo $book['id']; ?>">Edit</a> |
                            <a href="admin.php?action=delete_book&id=<?php echo $book['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php elseif ($action === 'view_stats'): ?>
            <h2>Borrowing Statistics</h2>
            <p>Total Approved Borrows: <strong><?php echo $stats['total_borrows']; ?></strong></p>
            <h3>Top 5 Borrowed Books</h3>
            <ul>
                <?php foreach ($stats['top_books'] as $book): ?>
                    <li><?php echo htmlspecialchars($book['title']); ?> (<?php echo $book['borrow_count']; ?> borrows)</li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </main>
</body>
</html>
