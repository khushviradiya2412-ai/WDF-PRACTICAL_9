# Practical 9 — Secure User Registration with Database Insert, Duplicate Email Check, and Password Hashing

## Purpose
StudentHub module (ITUE203, CHARUSAT) that implements secure registration using PHP MySQLi:
frontend + backend validation, duplicate username/email check, `password_hash()` storage,
prepared statements, CSRF token, and an audit log entry per registration.

## Files
- `index.php` — landing page linking to registration.
- `register.php` — registration form + PHP processing (self-contained).
- `db.php` — MySQLi connection to `studenthub_p9` (utf8mb4, generic user errors).
- `database.sql` — schema + seed data (users, students, events, registrations, audit_logs).
- `assets/style.css` — shared look copied from Practical 7 (navbar `#2c3e50`).
- `assets/validate.js` — JS regex validation with real-time keyup checks.
- `test_cases.md` — valid / invalid / duplicate / CSRF test report.

## Setup
1. Start Apache + MySQL in XAMPP.
2. Open phpMyAdmin, create database `studenthub_p9` (or just import — the SQL creates it).
3. Import `PRACTICAL 9/database.sql`.
4. Copy the `PRACTICAL 9` folder into `htdocs` (if not already there).
5. Open in browser:
   - Landing: `http://localhost/PRACTICAL 9/index.php`
   - Register: `http://localhost/PRACTICAL 9/register.php`

## Test Credentials (seeded)
- Admin: `admin` / `Admin@123`
- Student: `student` / `Student@123`

## Viva Q&A
1. **Why is password_hash() used?**
   It stores a one-way salted hash instead of the plain password, so leaked database rows
   do not reveal passwords; login checks with `password_verify()`.
2. **Why are prepared statements used?**
   They send SQL and data separately, so user input cannot break the query (SQL injection);
   every query with user input in this practical uses `prepare()` + `bind_param()`.
3. **What does the CSRF token do?**
   A random per-session token in a hidden field proves the POST came from our own form;
   the server rejects requests with a missing/wrong token using `hash_equals()`.
