<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - Practical 9</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg" style="background-color:#2c3e50;">
        <div class="container-fluid nav-container">
            <a class="navbar-brand" href="index.php">StudentHub</a>
            <div class="nav-menu">
                <a class="nav-item active" href="index.php">Home</a>
                <a class="nav-item" href="register.php">Register</a>
            </div>
        </div>
    </nav>
</header>

<main>
    <section class="hero">
        <h1>Practical 9: Secure User Registration</h1>
        <p>Database insert, duplicate email check, and password hashing with MySQLi.</p>
        <a class="btn btn-primary" href="register.php">Go to Registration</a>
    </section>

    <section class="container">
        <h2 class="section-title">What this module does</h2>
        <div class="card-grid">
            <article class="card">
                <h3>Validated Input</h3>
                <p>Same rules on frontend JS and backend PHP for username, email and password.</p>
            </article>
            <article class="card">
                <h3>Duplicate Check</h3>
                <p>Prepared SELECT blocks duplicate username or email before insert.</p>
            </article>
            <article class="card">
                <h3>Secure Storage</h3>
                <p>Passwords stored with password_hash() plus CSRF token and audit log.</p>
            </article>
        </div>
    </section>
</main>

<footer>
    <div class="footer-bottom">StudentHub &copy; 2026 | Practical 9 | ITUE203 CHARUSAT</div>
</footer>
</body>
</html>
