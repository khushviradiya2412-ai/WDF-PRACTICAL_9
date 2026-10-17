// validate.js - Frontend validation for Practical 9 register form.
// Same rules as PHP server side: username, email, password, confirm password.
// Shows inline errors near fields and validates in real time on keyup.

var USERNAME_PATTERN = /^[a-zA-Z0-9_]{3,20}$/;
var EMAIL_PATTERN = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
// Min 8 chars + upper + lower + digit + special character.
var PASSWORD_PATTERN = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9]).{8,}$/;

function showError(id, message) {
    var el = document.getElementById(id);
    if (el) {
        el.textContent = message;
    }
}

function validateUsername() {
    var value = document.getElementById('username').value.trim();
    if (!USERNAME_PATTERN.test(value)) {
        showError('usernameError', 'Username must be 3-20 letters, numbers or underscore.');
        return false;
    }
    showError('usernameError', '');
    return true;
}

function validateEmail() {
    var value = document.getElementById('email').value.trim();
    if (!EMAIL_PATTERN.test(value)) {
        showError('emailError', 'Enter a valid email address.');
        return false;
    }
    showError('emailError', '');
    return true;
}

function validatePassword() {
    var value = document.getElementById('password').value;
    if (!PASSWORD_PATTERN.test(value)) {
        showError('passwordError', 'Min 8 chars with upper, lower, digit and special character.');
        return false;
    }
    showError('passwordError', '');
    return true;
}

function validateConfirm() {
    var password = document.getElementById('password').value;
    var confirm = document.getElementById('confirm_password').value;
    if (confirm === '' || confirm !== password) {
        showError('confirmError', 'Passwords do not match.');
        return false;
    }
    showError('confirmError', '');
    return true;
}

function validateRegisterForm() {
    var okUser = validateUsername();
    var okEmail = validateEmail();
    var okPass = validatePassword();
    var okConfirm = validateConfirm();
    return okUser && okEmail && okPass && okConfirm;
}

document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('registerForm');
    if (!form) {
        return;
    }

    // Real-time validation on keyup for quick feedback.
    document.getElementById('username').addEventListener('keyup', validateUsername);
    document.getElementById('email').addEventListener('keyup', validateEmail);
    document.getElementById('password').addEventListener('keyup', function () {
        validatePassword();
        validateConfirm();
    });
    document.getElementById('confirm_password').addEventListener('keyup', validateConfirm);

    // Block submit when frontend validation fails.
    form.addEventListener('submit', function (event) {
        if (!validateRegisterForm()) {
            event.preventDefault();
        }
    });
});
