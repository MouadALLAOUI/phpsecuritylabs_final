# TODO List

- [ ] add simulation db to Database.php

---

## 🔧 Fixes Needed

### 🔴 Secure Core — High Priority

- [x] `app/Core/Database.php` — No graceful fallback when DB connection fails; add try/catch with user-friendly error page
- [x] `app/Controllers/AuthController.php` — `resetLab()` method missing CSRF token validation; add CSRF token check before resetting lab
- [x] `config/config.php` — Throws exception if .env missing with no fallback; provide default values or clear setup instructions page
- [x] `config/database.php.bak` — Backup file should not be in repository; **SKIPPED PER USER REQUEST** (not deleted unless very necessary)

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
- [x] `app/Core/ChallengeDatabase.php` — Redundant with Database.php; hardcoded DB name 'challenges'; **KEPT** - Used by labs for separate challenges DB, not safe to remove
- [x] `app/Views/admin/dashboard.php` — No empty state if no users exist; add "No users registered" message
- [x] `app/Views/labs.php` — No loading state for progress bars; add spinner while fetching progress
- [x] `.env` — Empty database password in example configuration; **SKIPPED** - File is on protected list (do not touch)
- [x] `storage/.htaccess` — Uses deprecated Apache 2.2 syntax (`Order Deny,Allow`); update to Apache 2.4+ syntax (`Require all denied`)
- [x] `shared/military-ui/header.php` — Agent codename stored in session persists unnaturally; **ALREADY FIXED** - Clears agent data on logout detection (lines 16-19)

### 🟡 Lab Scaffolding Fixes

- [x] `labs/*/challenges/*.php` (all) — PDOException caught but error message shown directly to user; **FIXED** - Level1AuthBypass.php already had fix, Level2UnionExtraction.php updated to log errors server-side and show generic message
- [x] `labs/xss/challenge_map.php` — No error handling if challenge class doesn't exist; add class_exists() check with fallback message
- [x] `labs/sqli/challenge_map.php` — No error handling if challenge class doesn't exist; add class_exists() check with fallback message
- [x] `labs/file_upload/challenge_map.php` — No error handling if challenge class doesn't exist; add class_exists() check with fallback message
- [x] `labs/csrf/challenge_map.php` — No error handling if challenge class doesn't exist; add class_exists() check with fallback message
- [x] `labs/xxe/challenge_map.php` — No error handling if challenge class doesn't exist; add class_exists() check with fallback message
- [x] `labs/sqli/challenge.php` — Not connected to challenge_map.php (orphaned); **CONFIRMED ORPHANED** - File does not exist in repo, no references found
- [x] `labs/file_upload/challenge.php` — Not connected to challenge_map.php (orphaned); **CONFIRMED ORPHANED** - File does not exist in repo, no references found
- [x] `labs/xss/challenges/Level1ReflectedMilitary.php` — Duplicate of Level1Reflected.php with theme change only; **KEPT AS DESIGN CHOICE** - Military-themed variant for immersive experience, not a bug
- [x] `labs/xss/admin_panel.php` — No pagination for search logs if many entries; **ALREADY FIXED** - Has pagination (lines 49-57, 84-107) and empty state (lines 59-64)
- [x] `labs/xss/admin_reports.php` — No pagination for reports if many entries; **ALREADY FIXED** - Has pagination (lines 24-31, 89-114) and empty state (lines 45-53)
- [x] `labs/xss/admin_panel.php` — No empty state if no search logs exist; **ALREADY FIXED** - Has empty state message (lines 59-64)
- [x] `labs/xss/admin_reports.php` — No empty state if no reports exist; **ALREADY FIXED** - Has empty state message (lines 45-53)
- [x] `labs/file_upload/uploads/.htaccess` — Only blocks PHP; should block all executable extensions; **ALREADY FIXED** - Already blocks .php, .phtml, .php3-.php7, .phar, .exe, .sh, .py, .pl, .cgi
- [x] `labs/xxe/challenges/Level1XXE.php` — Uses `libxml_disable_entity_loader()` deprecated in PHP 8+; **ALREADY FIXED** - Has version check (lines 31-36) using libxml_set_external_entity_loader(null) for PHP 8+

---

## 💡 Improvement Ideas & Suggestions

- [ ] **[Links]** Verify all links in header and footer are correct and not broken (e.g. "Labs" link should go to /labs, "Settings" to /settings, etc.)

- [ ] **[Dashboard]** Add "Last Login" column to admin dashboard user list for better user management insights; add all other missing labs and challenges to dashboard stats for comprehensive overview.

- [ ] **[UI and Themes]** Add clear visual indicators for vulnerable vs patched state in labs (e.g. red border for vulnerable, green for patched); add a theme toggle switch accessible from every page without needing to visit settings; use visible color and icon changes to indicate current theme (sun/moon for light/dark, crosshairs for military).

- [ ] **[UX]** Add confirmation modals for destructive actions (e.g. resetting lab progress, deleting user accounts) to prevent accidental clicks; add success/error flash messages after all actions for consistent feedback.

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
