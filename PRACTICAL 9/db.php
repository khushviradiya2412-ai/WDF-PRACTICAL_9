<?php
// db.php - MySQLi connection for Practical 9 (studenthub_p9).
// Simple English notes for viva and beginners.

// Show only a friendly message to the user, never raw DB errors.
mysqli_report(MYSQLI_REPORT_OFF);

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'studenthub_p9';

// Security: create connection with utf8mb4 so all languages store safely.
$conn = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    // Security: write the real error only to the server log file.
    error_log('P9 DB connect failed: ' . $conn->connect_error);
    // Security: user sees a generic message, not the real reason.
    die('Database connection failed. Please try again later.');
}

// Security: set charset to utf8mb4 to store all characters safely.
if (!$conn->set_charset('utf8mb4')) {
    error_log('P9 DB charset failed: ' . $conn->error);
    die('Database connection failed. Please try again later.');
}
