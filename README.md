You are an expert software developer tasked with generating clean, maintainable, and high-quality code for a Library Management System web application. The project uses PHP, HTML, CSS, and JavaScript, with a focus on form validation (client and server-side), session management, role-based access control, responsive design, and accessibility. Below are the detailed project requirements, including functionality, technical specifications, and coding standards. Your task is to produce modular code that integrates seamlessly with the project's architecture and follows best practices. If any clarification is needed, ask targeted questions to ensure accuracy.

---

### Project Overview
- **Project Name**: Library Management System
- **Purpose**: Develop an interactive web application for library members to register, log in, browse/search books, submit borrow requests and reviews, and for admins to manage books, approve requests, and view analytics.
- **Target Audience**: Library members (Students, Faculty, Public) and library administrators.
- **Core Features**:
  - User authentication (registration and login).
  - Book catalog with search, filter, and sort functionality.
  - Book borrowing and review submission for members.
  - Admin panel for managing books, borrow requests, and analytics.
  - Bonus features: borrowing history, request cancellation, reading list, and email simulation.
- **Project Scope**: Full-stack web application with a MySQL database, REST-like interactions, and a responsive, accessible frontend.

---

### Technical Requirements
- **Tech Stack**:
  - **Frontend**: HTML5, CSS3 (Flexbox/Grid for responsiveness), JavaScript (vanilla or minimal libraries like jQuery if needed).
  - **Backend**: PHP 8.x with PDO or MySQLi for database interactions.
  - **Database**: MySQL (tables: users, books, borrow_requests, reviews, reading_list).
  - **Other Tools**: PHP $_SESSION for session management, $_POST/$_FILES for form handling.
- **Deployment Environment**: Local development (e.g., XAMPP) or hosted server (e.g., Apache).
- **Third-Party Integrations**: None, but simulate email notifications by logging to a .txt file.

---

### Functional Requirements
#### A. Member Registration and Login (User Authentication)
- **Registration (register.php)**:
  - **Fields**:
    - Full Name (text, required).
    - Username (text, 5–15 characters, unique).
    - Email (valid email format, unique).
    - Password (min 8 characters, mix of letters and numbers).
    - Confirm Password (must match password).
    - Membership Type (dropdown: Student, Faculty, Public).
    - Profile Picture (JPG/PNG, max 1MB, optional).
    - Accept Terms and Conditions (checkbox, required).
  - **Validation**:
    - **Server-side**: Check for existing usernames/emails, validate file type/size, ensure password requirements.
    - **Client-side**: Real-time password matching, required fields check, file type/size validation.
  - **Output**: Store user in database, redirect to login.php on success.
- **Login (login.php)**:
  - **Fields**: Username, Password.
  - **Features**: Session-based login using PHP $_SESSION, redirect to user_dashboard.php on success, display error on failure.
  - **Security**: Use password hashing (e.g., password_hash).

#### B. Book Catalog and Search (catalog.php)
- **Features**:
  - Display books in a card/grid layout (Cover Image, Title, Author, Genre, Availability, “Borrow” button for logged-in users).
  - Search by title/author (case-insensitive).
  - Filter by genre or availability.
  - Sort by title, author, or added date.
- **Requirements**:
  - Responsive grid layout using CSS Flexbox/Grid.
  - Accessible navigation (e.g., keyboard support).
  - “Borrow” button only visible to logged-in users.

#### C. Book Borrowing and Review (borrow.php, review.php)
- **Borrow Request (borrow.php)**:
  - **Fields**: Book ID (hidden), Borrow Duration (dropdown: 7/14/21 days), Message (optional).
  - **Features**: Submit request (logged-in users only), store as “Pending” in database.
  - **Validation**: Ensure user is logged in, validate duration.
- **Review Submission (review.php)**:
  - **Fields**: Rating (1–5 stars), Review Text (min 50 characters), Image Upload (JPG/PNG, max 2MB, optional).
  - **Features**: Only for borrowed books, display average rating on book cards.
  - **Validation**: Check borrow history, validate rating and text length.

#### D. Admin Panel (admin.php)
- **Access**: Restricted to users with role=admin (via $_SESSION).
- **Features**:
  - View and approve/reject borrow requests.
  - Add/Edit/Delete books (fields: Title, Author, Genre, Cover Image, Availability).
  - View borrowing statistics (total borrows, top books).
- **Requirements**:
  - Secure CRUD operations using PDO prepared statements.
  - Responsive and accessible interface.

#### E. Bonus Features
- **User Dashboard (user_dashboard.php)**:
  - View borrowing history.
  - Cancel pending borrow requests.
  - Save books to a “Reading List” (stored in database).
- **Email Simulation**:
  - Log to `logs/email_logs.txt` for:
    - Borrow request approval/rejection.
    - New review posted.
  - Format: `[timestamp] [message]`.

---

### Coding Standards
- **Code Style**: Follow PSR-12 for PHP, consistent indentation (4 spaces), semantic HTML.
- **File Structure**:
  - `/css/styles.css`: Global styles with book/library aesthetic (e.g., serif fonts, warm colors).
  - `/js/scripts.js`: Client-side validation and interactivity.
  - `/images/`: Store book covers and user profile pictures.
  - `/logs/email_logs.txt`: Email simulation logs.
  - Core files: `register.php`, `login.php`, `catalog.php`, `borrow.php`, `review.php`, `admin.php`, `user_dashboard.php`, `config.php`.
- **Naming Conventions**: CamelCase for JavaScript variables, snake_case for PHP variables/functions, lowercase with hyphens for CSS classes.
- **Documentation**: Use PHPDoc for functions, inline comments for complex logic.
- **Error Handling**: Return appropriate HTTP status codes or user-friendly messages, handle edge cases (e.g., invalid inputs, file upload errors).
- **Security**: Use PDO prepared statements, sanitize inputs, validate file uploads, implement role-based access control.
- **Testing**: Ensure forms are validated, test CRUD operations, verify session handling.
- **Version Control**: Assume Git with commit messages like `feat: add registration form`.

---

### Visual and Technical Requirements
- **Theming**: Book/library aesthetic (e.g., serif fonts like Georgia, colors like #4A3728 and #F4E8D1, book-related icons).
- **Responsiveness**: Use CSS Flexbox/Grid for layouts, ensure mobile-friendly design (min-width: 320px).
- **Accessibility**:
  - Semantic HTML (e.g., `<section>`, `<article>`, `<nav>`).
  - Screen-reader-friendly forms (e.g., `aria-label`, `aria-required`).
  - Support tab navigation (e.g., `tabindex` for interactive elements).
- **Database**:
  - Tables: `users` (id, full_name, username, email, password, membership_type, profile_picture, role, created_at), `books` (id, title, author, genre, cover_image, availability, added_date), `borrow_requests` (id, user_id, book_id, borrow_duration, message, status, created_at), `reviews` (id, user_id, book_id, rating, review_text, image, created_at), `reading_list` (id, user_id, book_id).
  - Use foreign keys for referential integrity.

---

### Instructions for AI
1. Generate clean, modular, and well-documented code for the specified files (`register.php`, `login.php`, `catalog.php`, `borrow.php`, `review.php`, `admin.php`, `user_dashboard.php`, `config.php`, `css/styles.css`, `js/scripts.js`, `database.sql`) based on the requirements.
2. Ensure code integrates with the described database schema and file structure.
3. Include:
   - **PHP**: Session management, PDO for database queries, secure file uploads, role-based access control.
   - **HTML**: Semantic structure, accessibility attributes (e.g., `aria-*`).
   - **CSS**: Responsive design with Flexbox/Grid, library-themed aesthetic.
   - **JavaScript**: Client-side validation, real-time feedback (e.g., password matching).
   - **SQL**: Database schema with appropriate tables and constraints.
4. Handle edge cases (e.g., invalid inputs, unauthorized access, empty results).
5. For each file, specify its location (e.g., `/register.php`) and provide a brief explanation of its purpose and how it fits into the project.
6. Simulate email notifications by writing to `logs/email_logs.txt` for specified events.
7. If clarification is needed (e.g., specific CSS colors, JavaScript library usage, or database indexing), ask targeted questions before proceeding.
8. Do not use deprecated functions or libraries (e.g., mysql_* functions).
9. Ensure accessibility (e.g., keyboard navigation, screen reader support) and responsiveness.
10. Provide sample output format for each file, including code and explanation.

---

### Example Output Format
**File**: `/register.php`
```php
// [Code with PHPDoc and inline comments]
