<?php
/**
 * User Dashboard
 *
 * This file provides a personalized dashboard for logged-in users. It displays
 * their borrowing history, pending requests, and reading list. Users can also
 * cancel pending requests from this page.
 *
 * PHP version 8.x
 *
 * @category   User
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

$user_id = $_SESSION['user_id'];
$success_message = '';

// --- Action Handling (Cancel Request / Remove from Reading List) ---
if (isset($_POST['action'])) {
    $item_id = (int)$_POST['item_id'];

    if ($_POST['action'] === 'cancel_request' && $item_id > 0) {
        // First, verify the request belongs to the user and is 'Pending'
        $stmt = $pdo->prepare("SELECT book_id FROM borrow_requests WHERE id = ? AND user_id = ? AND status = 'Pending'");
        $stmt->execute([$item_id, $user_id]);
        $request = $stmt->fetch();

        if ($request) {
            // Delete the request
            $pdo->prepare("DELETE FROM borrow_requests WHERE id = ?")->execute([$item_id]);
            // Make the book available again
            $pdo->prepare("UPDATE books SET availability = 'Available' WHERE id = ?")->execute([$request['book_id']]);
            $success_message = "Your request has been cancelled.";
        }
    } elseif ($_POST['action'] === 'remove_from_list' && $item_id > 0) {
        $stmt = $pdo->prepare("DELETE FROM reading_list WHERE id = ? AND user_id = ?");
        $stmt->execute([$item_id, $user_id]);
        $success_message = "Book removed from your reading list.";
    }
}


// --- Fetch User Data ---
// Borrowing History
$stmt_history = $pdo->prepare("SELECT br.status, br.created_at, b.title, b.author FROM borrow_requests br JOIN books b ON br.book_id = b.id WHERE br.user_id = ? ORDER BY br.created_at DESC");
$stmt_history->execute([$user_id]);
$borrow_history = $stmt_history->fetchAll();

// Pending Requests
$stmt_pending = $pdo->prepare("SELECT br.id, br.created_at, b.title FROM borrow_requests br JOIN books b ON br.book_id = b.id WHERE br.user_id = ? AND br.status = 'Pending' ORDER BY br.created_at DESC");
$stmt_pending->execute([$user_id]);
$pending_requests = $stmt_pending->fetchAll();

// Reading List
$stmt_list = $pdo->prepare("SELECT rl.id, b.title, b.author FROM reading_list rl JOIN books b ON rl.book_id = b.id WHERE rl.user_id = ? ORDER BY b.title");
$stmt_list->execute([$user_id]);
$reading_list = $stmt_list->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - Library Management System</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <nav>
            <a href="catalog.php">Browse Catalog</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <?php if ($success_message): ?>
            <div class="success"><p><?php echo $success_message; ?></p></div>
        <?php endif; ?>

        <section id="pending-requests">
            <h2>Pending Borrow Requests</h2>
            <?php if (count($pending_requests) > 0): ?>
                <ul>
                    <?php foreach ($pending_requests as $request): ?>
                        <li>
                            "<?php echo htmlspecialchars($request['title']); ?>" (Requested on <?php echo date('Y-m-d', strtotime($request['created_at'])); ?>)
                            <form method="post" style="display:inline; margin-left: 10px;">
                                <input type="hidden" name="action" value="cancel_request">
                                <input type="hidden" name="item_id" value="<?php echo $request['id']; ?>">
                                <button type="submit" class="btn-cancel">Cancel</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>You have no pending requests.</p>
            <?php endif; ?>
        </section>

        <section id="borrow-history">
            <h2>Borrowing History</h2>
            <table>
                <thead>
                    <tr>
                        <th>Book Title</th>
                        <th>Author</th>
                        <th>Request Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($borrow_history as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['title']); ?></td>
                        <td><?php echo htmlspecialchars($item['author']); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($item['created_at'])); ?></td>
                        <td><?php echo htmlspecialchars($item['status']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section id="reading-list">
            <h2>My Reading List</h2>
            <?php if (count($reading_list) > 0): ?>
                <ul>
                    <?php foreach ($reading_list as $item): ?>
                        <li>
                            <?php echo htmlspecialchars($item['title']); ?> by <?php echo htmlspecialchars($item['author']); ?>
                            <form method="post" style="display:inline; margin-left: 10px;">
                                <input type="hidden" name="action" value="remove_from_list">
                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" class="btn-remove">Remove</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Your reading list is empty. Browse the <a href="catalog.php">catalog</a> to add books.</p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
