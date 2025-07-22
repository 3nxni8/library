Description:

You are tasked with developing an interactive Library Management System using PHP, HTML, CSS, and JavaScript. This web application must allow users to:

1. Register and log in as library members.

2. Browse and search for books in the online catalog.

3. Submit book reviews and borrow requests.

4. For admin users: manage books, approve borrow requests, and view analytics.

This project emphasizes form validation (client and server-side), session management, role-based access control, responsive design, and accessibility.

 

Instructions

A. Member Registration and Login (User Authentication)

• Registration Form (register.php)
 Fields:
 Full Name
 Username (unique, 5–15 characters)
 Email (valid format)
 Password (min 8 characters, mix of letters and numbers)
 Confirm Password (must match)
 Membership Type (Student, Faculty, Public – dropdown)
 Profile Picture (JPG/PNG, max 1MB)
 Accept Terms and Conditions (checkbox)
 Validation
 Server-side
 Check for existing usernames/emails in the database.
 Client-side
 Real-time password matching required fields check.
 File type/size check for profile image.
 Login Form (login.php)
 Session-based login using PHP $_SESSION.
 Redirect to user dashboard after successful login.
 Error message on failed attempts.
B. Book Catalog and Search (catalog.php)

 Display all books using card/grid layout.
 Each book should show
 Cover Image, Title, Author, Genre, Availability
 “Borrow” button (for logged-in users)
 Include
 Search by title/author
 Filter by genre or availability
 Sort by title, author, or added date
C. Book Borrowing and Review (borrow.php, review.php)

 Borrow Request Form
 Submit a borrow request (logged-in users only).
 Fields: Book ID (hidden), Borrow Duration (dropdown: 7/14/21 days), Message (optional)
 Store status as "Pending" in the database.
• Review Submission
 Fields
 Rating (1–5 stars)
 Review Text (min 50 characters)
 Optional Image Upload (max 2MB)
 Logged-in users can review books they have borrowed.
 Display average rating on each book card.
D. Admin Panel (admin.php)

 Restricted access (role-based).
 Features
 View and approve/reject borrow requests.
 Add/Edit/Delete books.
 View borrowing statistics (total borrows, top books).
 Validation and secure CRUD operations (use prepared statements in PHP).
E. Bonus Feature

 Allow members to
 See borrowing history (user_dashboard.php)
 Cancel pending requests
 Save books to a “Reading List”
 Email simulation (write logs to .txt file) when
 Borrow request is approved/rejected.
 New review is posted.
 

Visual and Technical Requirements

 Theming with book/library aesthetic: fonts, icons, colors.
 Mobile responsiveness using Flexbox/Grid.
 Accessibility
 Semantic HTML
 Screen-reader-friendly form inputs
 Tab navigation support
 Use PHP $_SESSION, $_POST, $_FILES, and mysqli or PDO.
