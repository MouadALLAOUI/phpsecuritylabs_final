# TODO List

- [ ] add simulation db to Database.php

---

## 🔧 Fixes Needed

### 🔴 Secure Core — High Priority

- [x] `app/Core/Database.php` — No graceful fallback when DB connection fails; add try/catch with user-friendly error page
- [x] `app/Controllers/AuthController.php` — `resetLab()` method missing CSRF token validation; add CSRF token check before resetting lab
- [x] `config/config.php` — Throws exception if .env missing with no fallback; provide default values or clear setup instructions page
- [ ] `config/database.php.bak` — Backup file should not be in repository; **DELETE THIS FILE**

### 🔴 Secure Core — Medium Priority

- [x] `app/Core/Database.php` — Uses array for instances instead of proper singleton pattern; refactor to clean singleton or multi-DB manager
- [x] `app/Core/Router.php` — Hardcoded route list requires manual maintenance; implement dynamic route discovery or config file
- [x] `app/Core/Router.php` — Returns null for unmatched routes (plain text 404); return styled 404 error page
- [x] `app/Core/BaseChallenge.php` — Uses `$_REQUEST` which combines GET/POST/COOKIE; use explicit `$_POST` or `$_GET` based on context
- [x] `app/Core/LabEngine.php` — Stores vulnerability config in session (user-manipulable); store in server-side session with integrity check
- [x] `app/Views/settings/index.php` — Settings stored only in session (lost on logout); persist settings to database or cookies
- [x] `shared/header.php` — Language selector references non-existent `$_SESSION['lang']` key; add null coalescing operator or initialize session key
- [x] `shared/header.php` — Theme toggle function referenced but not defined; define `toggleTheme()` JavaScript function
- [x] `api.php` — Overly permissive CORS header (`Access-Control-Allow-Origin: *`); restrict to specific origins or remove for internal API
- [x] `api.php` — No rate limiting on API endpoints; implement rate limiting middleware
- [x] `tests/Core/AuthTest.php` — Minimal test coverage (only 3 basic tests); add tests for login, logout, permission checks

### 🔴 Secure Core — Low Priority

- [x] `app/Core/Database.php` — Commented-out `use PDOException` import (line 6); remove commented code or fix import
- [ ] `app/Core/ChallengeDatabase.php` — Redundant with Database.php; hardcoded DB name 'challenges'; merge with Database.php or remove
- [x] `app/Views/admin/dashboard.php` — No empty state if no users exist; add "No users registered" message
- [x] `app/Views/labs.php` — No loading state for progress bars; add spinner while fetching progress
- [ ] `.env` — Empty database password in example configuration; add placeholder password with comment
- [x] `storage/.htaccess` — Uses deprecated Apache 2.2 syntax (`Order Deny,Allow`); update to Apache 2.4+ syntax (`Require all denied`)
- [ ] `shared/military-ui/header.php` — Agent codename stored in session persists unnaturally; clear agent data on logout or use temporary session

### 🟡 Lab Scaffolding Fixes

- [ ] `labs/*/challenges/*.php` (all) — PDOException caught but error message shown directly to user; use custom error messages; log details server-side
- [x] `labs/xss/challenge_map.php` — No error handling if challenge class doesn't exist; add class_exists() check with fallback message
- [x] `labs/sqli/challenge_map.php` — No error handling if challenge class doesn't exist; add class_exists() check with fallback message
- [x] `labs/file_upload/challenge_map.php` — No error handling if challenge class doesn't exist; add class_exists() check with fallback message
- [x] `labs/csrf/challenge_map.php` — No error handling if challenge class doesn't exist; add class_exists() check with fallback message
- [x] `labs/xxe/challenge_map.php` — No error handling if challenge class doesn't exist; add class_exists() check with fallback message
- [ ] `labs/sqli/challenge.php` — Not connected to challenge_map.php (orphaned); either integrate into routing or DELETE
- [ ] `labs/file_upload/challenge.php` — Not connected to challenge_map.php (orphaned); either integrate into routing or DELETE
- [ ] `labs/xss/challenges/Level1ReflectedMilitary.php` — Duplicate of Level1Reflected.php with theme change only; consider merging or clearly differentiating
- [ ] `labs/xss/admin_panel.php` — No pagination for search logs if many entries; add pagination for large log files
- [ ] `labs/xss/admin_reports.php` — No pagination for reports if many entries; add pagination for large report lists
- [ ] `labs/xss/admin_panel.php` — No empty state if no search logs exist; add "No search logs available" message
- [ ] `labs/xss/admin_reports.php` — No empty state if no reports exist; add "No reports submitted" message
- [ ] `labs/file_upload/uploads/.htaccess` — Only blocks PHP; should block all executable extensions; add rules for .php, .phtml, .php5, .exe, .sh, etc.
- [ ] `labs/xxe/challenges/Level1XXE.php` — Uses `libxml_disable_entity_loader()` deprecated in PHP 8+; add version check and alternative for PHP 8+

---

## 💡 Improvement Ideas & Suggestions

- [ ] **[New Lab Modules]** Add SSRF (Server-Side Request Forgery), IDOR (Insecure Direct Object Reference), Path Traversal, Deserialization, and JWT attack labs to expand OWASP Top 10 coverage
- [ ] **[Instructor/Demo Mode]** Implement live toggle between vulnerable and patched state within each lab, allowing instructors to demonstrate both the exploit and the fix in real-time
- [ ] **[Student Experience]** Add hint reveal timer (penalty for using hints), lab completion timer, and score multiplier for speed to increase engagement
- [ ] **[Progress & Reporting]** Build instructor dashboard with class-wide analytics and add student progress export to PDF/CSV for certification tracking
- [ ] **[Deployment]** Create Docker Compose setup with pre-configured MySQL and PHP, plus a one-command install script for quick environment provisioning
- [ ] **[API Hardening]** Implement rate limiting middleware, lockdown CORS to specific origins, and add API key authentication for REST endpoints
- [ ] **[Auth Upgrade]** Replace MD5 with `password_hash()`/bcrypt in Auth.php and update seed.sql to use secure password hashing
- [ ] **[i18n]** Wire existing Translator.php + en/es/fr.json to all views, enabling full multi-language support throughout the platform
- [ ] **[Testing]** Expand PHPUnit suite to cover Database, Router, ChallengeLoader, and LabEngine classes with integration tests
- [ ] **[Accessibility]** Add ARIA labels and keyboard navigation support for military UI terminal inputs and all interactive elements
- [ ] **[Theme Persistence]** Save user theme preference (dark/light/military) to database instead of just session for persistent experience across sessions
- [ ] **[Notification System]** Wire the notifications table to a real in-app bell icon with real-time updates for challenge completions and achievements
