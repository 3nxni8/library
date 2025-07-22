# library
library
Assignment: Library Management Website
Type of Assignment: Group
Description:
You are tasked with developing an interactive Library Management System using PHP,
HTML, CSS, and JavaScript. This web application must allow users to:
1. Register and log in as library members.
2. Browse and search for books in the online catalog.
3. Submit book reviews and borrow requests.
4. For admin users: manage books, approve borrow requests, and view analytics.
This project emphasizes form validation (client and server-side), session management,
role-based access control, responsive design, and accessibility.
Instructions
A. Member Registration and Login (User Authentication)
• Registration Form (register.php)
o Fields:
▪ Full Name
▪ Username (unique, 5–15 characters)
▪ Email (valid format)
▪ Password (min 8 characters, mix of letters and numbers)
▪ Confirm Password (must match)
▪ Membership Type (Student, Faculty, Public – dropdown)
▪ Profile Picture (JPG/PNG, max 1MB)
▪ Accept Terms and Conditions (checkbox)
o Validation
▪ Server-side
▪ Check for existing usernames/emails in the database.
▪ Client-side
▪ Real-time password matching required fields check.
▪ File type/size check for profile image.
• Login Form (login.php)
o Session-based login using PHP $_SESSION.
o Redirect to user dashboard after successful login.
o Error message on failed attempts.
B. Book Catalog and Search (catalog.php)
• Display all books using card/grid layout.
• Each book should show
o Cover Image, Title, Author, Genre, Availability
o “Borrow” button (for logged-in users)
• Include
o Search by title/author
ITNB2123 INTERNET PROGRAMMING
o Filter by genre or availability
o Sort by title, author, or added date
C. Book Borrowing and Review (borrow.php, review.php)
• Borrow Request Form
o Submit a borrow request (logged-in users only).
o Fields: Book ID (hidden), Borrow Duration (dropdown: 7/14/21 days),
Message (optional)
o Store status as "Pending" in the database.
• Review Submission
o Fields
▪ Rating (1–5 stars)
▪ Review Text (min 50 characters)
▪ Optional Image Upload (max 2MB)
o Logged-in users can review books they have borrowed.
o Display average rating on each book card.
D. Admin Panel (admin.php)
• Restricted access (role-based).
• Features
o View and approve/reject borrow requests.
o Add/Edit/Delete books.
o View borrowing statistics (total borrows, top books).
• Validation and secure CRUD operations (use prepared statements in PHP).
E. Bonus Feature
• Allow members to
o See borrowing history (user_dashboard.php)
o Cancel pending requests
o Save books to a “Reading List”
• Email simulation (write logs to .txt file) when
o Borrow request is approved/rejected.
o New review is posted.
Visual and Technical Requirements
• Theming with book/library aesthetic: fonts, icons, colors.
• Mobile responsiveness using Flexbox/Grid.
• Accessibility
o Semantic HTML
o Screen-reader-friendly form inputs
o Tab navigation support
• Use PHP $_SESSION, $_POST, $_FILES, and mysqli or PDO.
Deliverables
A. Fully Functional Web Application
• Organized into folders
o /php, /css, /js, /images, /uploads
ITNB2123 INTERNET PROGRAMMING
• Uses PHP with a local MySQL/MariaDB database.
• All forms and interactions validated client-side (JavaScript) and server-side
(PHP).
B. Technical Report (PDF, 15+ Pages)
• Sections
o Introduction and Purpose
o Database Schema Design
o Authentication and Session Handling
o Book Management Logic (CRUD with SQL)
o Validation Logic with Code Samples
o Role-Based Access Control
o Security Measures (SQL injection prevention, file validation)
o Testing Procedures and Screenshots
o Challenges and Improvements
o References
C. Presentation Slides (PDF, 10–12 slides)
• Overview of system
• Key features
o Registration
o Book Borrowing,
o Admin Panel
• Validation Flow (Client and Server)
• Code Highlights
• Database Structure (ERD or simplified diagram)
• Final Thoughts
Submission Guidelines
• Submit a .ZIP file:
o Project source code
o Report (PDF)
o Slides (PDF)
• Format: LibraryProject_MatricNo.zip
• Submit via CN only
• Due Date: 0830, Thursday, 31 July 2025
