/**
 * Client-Side JavaScript
 *
 * This file contains JavaScript for enhancing user experience, primarily through
 * client-side form validation and real-time feedback.
 *
 * @category   JavaScript
 * @package    LibraryManagementSystem
 * @author     Abdullahi Nur <nurthed27@gmail.com>
 */

document.addEventListener('DOMContentLoaded', function() {

    // --- Registration Form: Password Confirmation ---
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const passwordMatchError = document.getElementById('password-match-error');

        const validatePasswordMatch = () => {
            if (password.value !== confirmPassword.value) {
                passwordMatchError.textContent = "Passwords do not match.";
                confirmPassword.setCustomValidity("Passwords do not match.");
            } else {
                passwordMatchError.textContent = "";
                confirmPassword.setCustomValidity("");
            }
        };

        password.addEventListener('input', validatePasswordMatch);
        confirmPassword.addEventListener('input', validatePasswordMatch);
    }

    // --- General Form Validation Feedback ---
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                // Optionally, add a class to show custom validation styles
                form.classList.add('was-validated');
            }
        }, false);
    });

    // --- File Input Validation (e.g., Profile Picture) ---
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', event => {
            const file = event.target.files[0];
            const maxSizeMB = input.id === 'profile_picture' ? 1 : 2; // 1MB for profile, 2MB for review

            if (file) {
                if (file.size > maxSizeMB * 1024 * 1024) {
                    alert(`File size exceeds the ${maxSizeMB}MB limit.`);
                    input.value = ''; // Clear the invalid selection
                }
                if (!['image/jpeg', 'image/png'].includes(file.type)) {
                    alert('Invalid file type. Please upload a JPG or PNG image.');
                    input.value = '';
                }
            }
        });
    });

});
