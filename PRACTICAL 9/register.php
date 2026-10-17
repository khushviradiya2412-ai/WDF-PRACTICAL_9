<?php
// register.php - Secure User Registration (Practical 9).
// Frontend JS validation + backend PHP validation, duplicate check,
// password_hash(), prepared statements, CSRF token, audit log.

// Security: start session so we can store the CSRF token.
session_start();
require_once __DIR__ . '/db.php';

// Security: create a CSRF token once per session using random bytes.
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$success = '';
// Keep typed values to refill the form after an error.
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Security: check CSRF token to block fake form submits from other sites.
    $sentToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $sentToken)) {
        $errors[] = 'Invalid request. Please reload the form and try again.';
    } else {

        // Security: trim and clean input before checking it.
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        // Security: role is fixed to student, never taken from user choice.
        $role = 'student';

        // Security: server validates even when JS validates (JS can be bypassed).
        if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
            $errors['username'] = 'Username must be 3-20 letters, numbers or underscore.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email address.';
        }
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9]).{8,}$/', $password)) {
            $errors['password'] = 'Password needs min 8 chars with upper, lower, digit and special character.';
        }
        if ($confirm === '' || $confirm !== $password) {
            $errors['confirm'] = 'Passwords do not match.';
        }

        if (empty($errors)) {
            // Security: use prepared SELECT to check duplicate username safely.
            $stmt = $conn->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $errors['username'] = 'Username already exists. Please choose another.';
            }
            $stmt->close();

            // Security: use prepared SELECT to check duplicate email safely.
            if (empty($errors)) {
                $stmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $errors['email'] = 'Email already registered. Please login instead.';
                }
                $stmt->close();
            }

            if (empty($errors)) {
                // Security: hash password so plain password is never stored.
                $hash = password_hash($password, PASSWORD_DEFAULT);

                // Security: use prepared INSERT so user input cannot break the query.
                $stmt = $conn->prepare('INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)');
                $stmt->bind_param('ssss', $username, $email, $hash, $role);
                if ($stmt->execute()) {
                    $newUserId = $stmt->insert_id;
                    $stmt->close();

                    // Security: write audit log via prepared INSERT for traceability.
                    $action = 'user_registered';
                    $details = 'New student registered: ' . $username;
                    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
                    $log = $conn->prepare('INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)');
                    $log->bind_param('isss', $newUserId, $action, $details, $ip);
                    $log->execute();
                    $log->close();

                    $success = 'Registration successful! You can now login.';
                    $username = '';
                    $email = '';
                } else {
                    // Security: log real error, show generic message to user.
                    error_log('P9 register insert failed: ' . $stmt->error);
                    $stmt->close();
                    $errors[] = 'Registration failed. Please try again later.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - StudentHub Practical 9</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg" style="background-color:#2c3e50;">
        <div class="container-fluid nav-container">
            <a class="navbar-brand" href="index.php">StudentHub</a>
            <div class="nav-menu">
                <a class="nav-item" href="index.php">Home</a>
                <a class="nav-item active" href="register.php">Register</a>
            </div>
        </div>
    </nav>
</header>

<main class="auth-wrapper">
    <section class="auth-box" aria-label="Registration form">
        <h2>Student Registration</h2>
        <p>Secure registration with hashing and duplicate check</p>

        <?php if ($success !== ''): ?>
            <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php foreach ($errors as $key => $msg): ?>
            <?php if (is_int($key)): ?>
                <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($msg); ?></div>
            <?php endif; ?>
        <?php endforeach; ?>

        <form id="registerForm" method="POST" action="register.php" novalidate>
            <!-- Security: hidden CSRF token checked on the server. -->
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <!-- Security: role is fixed to student only. -->
            <input type="hidden" name="role" value="student">

            <div class="form-group mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" maxlength="20" required
                       value="<?php echo htmlspecialchars($username); ?>">
                <small class="text-danger" id="usernameError"><?php echo htmlspecialchars($errors['username'] ?? ''); ?></small>
            </div>

            <div class="form-group mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" maxlength="100" required
                       value="<?php echo htmlspecialchars($email); ?>">
                <small class="text-danger" id="emailError"><?php echo htmlspecialchars($errors['email'] ?? ''); ?></small>
            </div>

            <div class="form-group mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <small class="text-danger" id="passwordError"><?php echo htmlspecialchars($errors['password'] ?? ''); ?></small>
            </div>

            <div class="form-group mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                <small class="text-danger" id="confirmError"><?php echo htmlspecialchars($errors['confirm'] ?? ''); ?></small>
            </div>

            <button type="submit" class="btn btn-primary">Register</button>
        </form>

        <div class="auth-footer">
            <a href="index.php">Back to Home</a>
        </div>
    </section>
</main>

<footer>
    <div class="footer-bottom">StudentHub &copy; 2026 | Practical 9 Secure Registration | ITUE203</div>
</footer>

<script src="assets/validate.js"></script>
</body>
</html>
