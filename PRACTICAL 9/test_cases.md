# Practical 9 — Registration Test Report

| # | Test Case | Input | Expected Result | Actual Result |
|---|-----------|-------|-----------------|---------------|
| 1 | Valid registration | username `newuser1`, email `newuser1@test.com`, password `Test@1234`, confirm `Test@1234` | Success message, row in `users`, row in `audit_logs` with `user_registered` | |
| 2 | Invalid email | username `testuser`, email `bademail`, password `Test@1234`, confirm `Test@1234` | Error near email field, no DB insert | |
| 3 | Weak password | username `testuser`, email `test@test.com`, password `abc`, confirm `abc` | Password strength error, no DB insert | |
| 4 | Password mismatch | username `testuser`, email `test@test.com`, password `Test@1234`, confirm `Test@5678` | "Passwords do not match" error, no DB insert | |
| 5 | Duplicate email | email `student@studenthub.local` (seeded) with new username | "Email already registered" error, no DB insert | |
| 6 | Duplicate username | username `student` (seeded) with new email | "Username already exists" error, no DB insert | |
| 7 | Missing CSRF token | POST without `csrf_token` field | "Invalid request" error, no DB insert | |
