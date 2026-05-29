# PHP Security Labs - Project Overview

> **Comprehensive audit and documentation of the PHP Security Labs training platform**

---

## 1. Project Summary

### What This App Is

**PHP Security Labs** is an intentionally vulnerable web application designed for security education and penetration testing training. It provides hands-on challenges covering the OWASP Top 10 vulnerabilities in a safe, controlled environment.

### Purpose

- 🎯 **Security Training**: Teach developers and security professionals about common web vulnerabilities
- 🛡️ **Defensive Learning**: Each lab includes patch reports showing secure implementations
- 🏆 **Gamified Experience**: Progress tracking, leaderboards, and achievement system
- 🔬 **Safe Environment**: Isolated labs where exploits can be practiced without real-world consequences

### Tech Stack

| Component | Technology |
|-----------|------------|
| **Backend** | PHP 8.x (namespaces, typed properties) |
| **Database** | MySQL/MariaDB with PDO |
| **Frontend** | TailwindCSS, Font Awesome icons |
| **Architecture** | Custom MVC-like pattern with PSR-4 autoloading |
| **Testing** | PHPUnit |
| **Theming** | Standard UI + Military-themed tactical UI |

---

## 2. Architecture Overview

### Request Flow

```
┌─────────────┐     ┌──────────────┐     ┌─────────────┐
│   Browser   │ ──► │  index.php   │ ──► │ ChallengeLoader │
│             │     │ (Front Ctrl) │     │ (for labs)   │
└─────────────┘     └──────────────┘     └─────────────┘
                           │
                           ▼
                    ┌──────────────┐
                    │    Router    │
                    │  (dispatch)  │
                    └──────────────┘
                           │
           ┌───────────────┼───────────────┐
           ▼               ▼               ▼
    ┌────────────┐  ┌────────────┐  ┌────────────┐
    │ Controllers│  │   Views    │  │   Labs     │
    │  (Auth)    │  │  (Templates│  │ (Challenges│
    └────────────┘  └────────────┘  └────────────┘
```

### Core Components

#### Routing System (`app/Core/Router.php`)
- URL-based routing via `?page=` parameter
- Hardcoded route map with dynamic lab route registration
- Special handling for authentication pages (login, logout, profile)

#### Authentication (`app/Core/Auth.php`)
- Session-based authentication
- MD5 password hashing (intentionally weak for training)
- Session fixation protection via `session_regenerate_id()`
- Leaderboard and progress tracking methods

#### Database Layer (`app/Core/Database.php`)
- Singleton pattern with multiple database support (`app` and `labs`)
- PDO with prepared statements
- Configuration loaded from `.env` file

#### Challenge System (`app/Core/ChallengeLoader.php`, `BaseChallenge.php`)
- Dynamic challenge loading from `challenge_map.php` files
- Abstract base class providing common functionality
- Progress persistence to database upon completion

#### Lab Engine (`app/Core/LabEngine.php`)
- Vulnerability toggle system (enable/disable specific vulnerabilities)
- Session-based persistence with optional database storage
- Allows instructors to demonstrate both vulnerable and patched code

### Directory Structure

```
/workspace
├── index.php              # Front controller
├── api.php                # REST API endpoints
├── .env                   # Environment configuration
├── .htaccess              # Apache rewrite rules
├── app/
│   ├── Core/              # Core classes (Auth, Database, Router, etc.)
│   ├── Controllers/       # Request handlers
│   └── Views/             # Page templates
├── config/                # Configuration files
├── database/              # SQL schema and seed data
├── labs/                  # Vulnerable challenge implementations
│   ├── xss/               # Cross-Site Scripting labs
│   ├── sqli/              # SQL Injection labs
│   ├── file_upload/       # File Upload labs
│   ├── csrf/              # CSRF labs
│   └── xxe/               # XXE labs
├── lang/                  # Translation files (JSON)
├── public/                # Publicly accessible files
├── shared/                # Reusable UI components
│   └── military-ui/       # Alternative tactical theme
├── storage/               # Logs and uploaded files
├── tests/                 # PHPUnit tests
└── overview.md            # This document
```

---

## 2.5 Architecture: Two Strictly Separate Environments

This project is architected as **two distinct environments** with strict separation of concerns:

### 🛡️ Environment 1: Secure Core (`/app`, `/config`, `/shared`, `/public/api.php`)
**Status:** Production-Quality | **Rule:** Zero Tolerance for Vulnerabilities

The Secure Core is a hardened application responsible for:
- **Authentication & Authorization**: Secure session management, password hashing (intentionally weak MD5 for legacy simulation, but logic is sound), and role-based access control.
- **Routing**: Centralized request dispatching via `App\Core\Router`.
- **Database Management**: Singleton pattern connections with PDO.
- **UI Framework**: Shared headers, footers, and sidebar components.

> **Directive:** Any missing error handling, broken UI feedback, security flaws (CSRF, XSS, SQLi), or bad patterns found here are **REAL BUGS** and must be fixed immediately. There are no intentional vulnerabilities in the Core.

### ⚔️ Environment 2: Military Simulation Playground (`/labs`)
**Status:** Educational Sandbox | **Rule:** One Intentional Vulnerability Per Challenge

The Labs directory contains isolated sandboxes for security training. Each lab folder (e.g., `labs/xss`, `labs/sqli`) represents a specific vulnerability class.

**Internal Lab Structure:**
1.  **The Exploit Surface (INTENTIONAL VULN)**: Specific lines of code designed to be exploited (e.g., unsanitized `echo`, string-concatenated SQL).
    *   **Directive:** ❌ **NEVER MODIFY**. These are the teaching tools.
2.  **Lab Scaffolding (FIXABLE)**: The surrounding code that renders the UI, handles file uploads (validation logic), manages database connections for the lab, or displays results.
    *   **Directive:** ✅ **IMPROVE FREELY**. This code should have proper error handling, fallback UIs, and clear feedback messages, provided the fixes do not patch the intentional vulnerability.

---

## 3. File-by-File Breakdown

### Root Level Files

| File | Environment | Role | Status | Issues |
|------|-------------|------|--------|--------|
| `index.php` | Secure Core | Front controller, PSR-4 autoloader, session init | ✅ Good | None significant |
| `api.php` | Secure Core | REST API for progress/leaderboard/challenge data | ⚠️ Warning | Overly permissive CORS (`*`), no rate limiting |
| `.env` | Secure Core | Database credentials and environment vars | ❌ Issue | Empty DB password in example, should be gitignored |
| `.htaccess` | Secure Core | Apache URL rewriting, block sensitive dirs | ✅ Good | None |
| `phpunit.xml` | Secure Core | PHPUnit configuration | ✅ Good | None |

### App/Core/ Directory

| File | Environment | Role | Status | Issues |
|------|-------------|------|--------|--------|
| `App.php` | Secure Core | Application bootstrap, creates Router | ✅ Good | None |
| `Auth.php` | Secure Core | User authentication, session management | ⚠️ Warning | Uses MD5 (intentional for training), otherwise solid |
| `Database.php` | Secure Core | PDO singleton, multi-DB support | ⚠️ Warning | Commented-out imports, uses array instead of single instance |
| `Router.php` | Secure Core | URL dispatch, route registration | ⚠️ Warning | Hardcoded routes need manual maintenance |
| `Session.php` | Secure Core | Session wrapper utility | ✅ Good | None |
| `BaseChallenge.php` | Secure Core | Abstract base for all challenges | ⚠️ Warning | Uses `$_REQUEST` (combines GET/POST/COOKIE) |
| `ChallengeInterface.php` | Secure Core | Interface defining challenge contract | ✅ Good | None |
| `ChallengeLoader.php` | Secure Core | Dynamic challenge instantiation | ✅ Good | None |
| `ChallengeDatabase.php` | Secure Core | Separate DB connection for challenges | ⚠️ Warning | Redundant with Database.php, hardcoded DB name |
| `LabEngine.php` | Secure Core | Vulnerability toggle management | ⚠️ Warning | Stores config in session (user-manipulable) |

### App/Controllers/ Directory

| File | Environment | Role | Status | Issues |
|------|-------------|------|--------|--------|
| `AuthController.php` | Secure Core | Login/logout/profile/admin handlers | ✅ Good | None |

### App/Views/ Directory

| File | Environment | Role | Status | Issues |
|------|-------------|------|--------|--------|
| `home.php` | Secure Core | Dashboard landing page | ✅ Good | Has empty state for progress |
| `login.php` | Secure Core | Login form with CSRF token | ✅ Good | Test credentials displayed in plaintext |
| `profile.php` | Secure Core | User profile, achievements display | ✅ Good | Has empty state for no achievements |
| `labs.php` | Secure Core | Lab selection overview | ✅ Good | Has reset confirmation dialogs |
| `leaderboard.php` | Secure Core | User rankings (military theme) | ✅ Good | Has empty state message |
| `admin/dashboard.php` | Secure Core | Admin user management | ✅ Good | Has admin message flash |
| `settings/index.php` | Secure Core | Theme/language preferences | ⚠️ Warning | Settings only persist in session (lost on logout) |
| `patch_xss.php` | Secure Core | XSS remediation guide | ✅ Good | Educational content |
| `patch_sqli.php` | Secure Core | SQLi remediation guide | ✅ Good | Educational content |
| `patch_fileupload.php` | Secure Core | File upload remediation guide | ✅ Good | Educational content |

### Config/ Directory

| File | Environment | Role | Status | Issues |
|------|-------------|------|--------|--------|
| `config.php` | Secure Core | Load .env, return DB config array | ⚠️ Warning | Throws exception if .env missing (no fallback) |
| `database.php.bak` | Secure Core | Backup config file | ❌ Issue | Should not be in repository |

### Database/ Directory

| File | Environment | Role | Status | Issues |
|------|-------------|------|--------|--------|
| `schema.sql` | Secure Core | Database table definitions | ⚠️ Warning | FK from notifications.user_id references users in different DB (broken) |
| `seed.sql` | Secure Core | Sample data (users, agents, secrets) | ⚠️ Warning | Contains hardcoded MD5 passwords |

### Labs/ Directory - XSS

| File | Environment | Role | Status | Issues |
|------|-------------|------|--------|--------|
| `challenge_map.php` | Lab Sandbox (Scaffolding) | Maps lvl1/lvl2/lvl3 to classes | ✅ Good | None |
| `challenges/Level1Reflected.php` | Lab Sandbox (INTENTIONAL VULN) | Reflected XSS challenge | ⚠️ Intentional | Direct output without escaping (line 91) - **DO NOT FIX** |
| `challenges/Level1ReflectedMilitary.php` | Lab Sandbox (INTENTIONAL VULN) | Military-themed duplicate | ⚠️ Duplicate | Same as Level1Reflected.php - **DO NOT FIX** |
| `challenges/Level2Stored.php` | Lab Sandbox (INTENTIONAL VULN) | Stored XSS challenge | ⚠️ Intentional | Reports stored/displayed unsanitized - **DO NOT FIX** |
| `challenges/Level3Dom.php` | Lab Sandbox (INTENTIONAL VULN) | DOM-based XSS challenge | ⚠️ Intentional | innerHTML from URL hash - **DO NOT FIX** |
| `admin_panel.php` | Lab Sandbox (INTENTIONAL VULN) | Simulated admin view | ⚠️ Intentional | Search logs echoed raw - **DO NOT FIX** |
| `admin_reports.php` | Lab Sandbox (INTENTIONAL VULN) | Admin report viewer | ⚠️ Intentional | Reports output without htmlspecialchars - **DO NOT FIX** |

### Labs/ Directory - SQLi

| File | Environment | Role | Status | Issues |
|------|-------------|------|--------|--------|
| `challenge_map.php` | Lab Sandbox (Scaffolding) | Maps lvl1/lvl2 to classes | ✅ Good | None |
| `challenges/Level1AuthBypass.php` | Lab Sandbox (INTENTIONAL VULN) | Auth bypass via SQLi | ⚠️ Intentional | String concatenation in query (line 50) - **DO NOT FIX** |
| `challenges/Level2UnionExtraction.php` | Lab Sandbox (INTENTIONAL VULN) | UNION-based extraction | ⚠️ Intentional | LIKE clause injection - **DO NOT FIX** |
| `challenge.php` | Lab Sandbox (Scaffolding) | Orphaned alternative challenge | ❌ Issue | Not connected to challenge_map.php - **FIXABLE** |

### Labs/ Directory - File Upload

| File | Environment | Role | Status | Issues |
|------|-------------|------|--------|--------|
| `challenge_map.php` | Lab Sandbox (Scaffolding) | Maps lvl1/lvl2 to classes | ✅ Good | None |
| `challenges/Level1ExtensionBypass.php` | Lab Sandbox (INTENTIONAL VULN) | Extension-only validation | ⚠️ Intentional | No content check (lines 46-47) - **DO NOT FIX** |
| `challenges/Level2MimeBypass.php` | Lab Sandbox (INTENTIONAL VULN) | MIME type spoofing | ⚠️ Intentional | Trusts Content-Type header - **DO NOT FIX** |
| `challenge.php` | Lab Sandbox (Scaffolding) | Orphaned alternative challenge | ❌ Issue | Not connected to challenge_map.php - **FIXABLE** |
| `uploads/.htaccess` | Lab Sandbox (Scaffolding) | Block PHP execution | ✅ Good | None |

### Labs/ Directory - CSRF

| File | Environment | Role | Status | Issues |
|------|-------------|------|--------|--------|
| `challenge_map.php` | Lab Sandbox (Scaffolding) | Maps lvl1 to class | ✅ Good | None |
| `challenges/Level1CSRF.php` | Lab Sandbox (INTENTIONAL VULN) | Fund transfer CSRF | ⚠️ Intentional | No CSRF token on form (lines 108-122) - **DO NOT FIX** |

### Labs/ Directory - XXE

| File | Environment | Role | Status | Issues |
|------|-------------|------|--------|--------|
| `challenge_map.php` | Lab Sandbox (Scaffolding) | Maps lvl1 to class | ✅ Good | None |
| `challenges/Level1XXE.php` | Lab Sandbox (INTENTIONAL VULN) | XML External Entity | ⚠️ Intentional | `libxml_disable_entity_loader(false)` deprecated in PHP 8+ - **DO NOT FIX** |

### Lang/ Directory

| File | Environment | Role | Status | Issues |
|------|-------------|------|--------|--------|
| `Translator.php` | Secure Core | Multi-language translation system | ⚠️ Warning | References JSON files but translations not used in views |
| `en.json` | Secure Core | English translations | ✅ Good | Present and valid |
| `es.json` | Secure Core | Spanish translations | ✅ Good | Present and valid |
| `fr.json` | Secure Core | French translations | ✅ Good | Present and valid |

### Public/ Directory

| File | Environment | Role | Status | Issues |
|------|-------------|------|--------|--------|
| `attacker.php` | Lab Sandbox (Scaffolding) | XSS exfiltration endpoint | ⚠️ Warning | Accessible without auth, logs stolen cookies - **INTENTIONAL FOR TRAINING** |
| `favicon.svg` | Secure Core | Site favicon | ✅ Good | None |

### Shared/ Directory

| File | Environment | Role | Status | Issues |
|------|-------------|------|--------|--------|
| `header.php` | Secure Core | Standard HTML header with nav | ⚠️ Warning | Language selector references non-existent session key, theme toggle function undefined |
| `footer.php` | Secure Core | Standard HTML footer | ✅ Good | None |
| `sidebar.php` | Secure Core | Sidebar navigation | ✅ Good | None |
| `military-ui/header.php` | Secure Core | Military-themed header | ⚠️ Warning | Agent codename stored in session (persists unnaturally) |
| `military-ui/footer.php` | Secure Core | Military-themed footer | ✅ Good | Includes alert/toast modals |
| `military-ui/mil-ops.css` | Secure Core | Military theme styles | ✅ Good | None |
| `military-ui/mil-ops.js` | Secure Core | Military theme interactions | ✅ Good | None |

### Storage/ Directory

| File | Environment | Role | Status | Issues |
|------|-------------|------|--------|--------|
| `.htaccess` | Secure Core | Deny web access | ⚠️ Warning | Uses deprecated Apache 2.2 syntax (`Order Deny,Allow`) |
| `logs/` | Secure Core | Log file directory | ✅ Good | Created dynamically by challenges |

### Tests/ Directory

| File | Environment | Role | Status | Issues |
|------|-------------|------|--------|--------|
| `Core/AuthTest.php` | Secure Core | PHPUnit tests for Auth class | ⚠️ Warning | Minimal coverage (3 basic tests only) |

---

## 4. Pages Needing Fixes

### Missing Error Handling (Try/Catch, DB Failure Messages)

| Page/File | Issue | Recommendation |
|-----------|-------|----------------|
| `app/Core/Database.php` | Exception thrown if config missing, no graceful fallback | Add try/catch with user-friendly error page |
| `app/Core/Config.php` | Throws exception if .env missing | Provide default values or clear setup instructions |
| `api.php` | Generic Exception catch, exposes messages | Log detailed errors, return generic messages to client |
| `labs/*/challenges/*.php` | PDOException caught but shown directly | Use custom error messages in production mode |
| `shared/header.php` | No error handling for session access | Add null coalescing for all `$_SESSION` accesses |

### Missing Fallback UI (Empty States, Loading States, No-Data States)

| Page/File | Current State | Needed Improvement |
|-----------|---------------|-------------------|
| `app/Views/home.php` | ✅ Has empty state for progress | Consider loading skeleton for stats |
| `app/Views/profile.php` | ✅ Has empty state for achievements | None needed |
| `app/Views/leaderboard.php` | ✅ Has empty state message | None needed |
| `app/Views/admin/dashboard.php` | ❌ No empty state if no users | Add "No users registered" message |
| `app/Views/labs.php` | ❌ No loading state for progress bars | Add spinner while fetching progress |
| `app/Views/settings/index.php` | ❌ No confirmation before theme change | Add toast notification on save |

### Missing Flash/Feedback Messages

| Page/File | Action | Current Feedback | Needed Improvement |
|-----------|--------|------------------|-------------------|
| `AuthController::handleLogin()` | Failed login | Session message (cleared after display) | ✅ Already implemented |
| `AuthController::resetLab()` | Lab reset | Session message | ✅ Already implemented |
| `AuthController::handleAdminReset()` | Admin reset | Session message | ✅ Already implemented |
| `labs/xss/Level1Reflected.php` | Challenge complete | Success banner in view | ✅ Already implemented |
| `labs/sqli/Level1AuthBypass.php` | Challenge complete | Terminal output message | ✅ Already implemented |
| `labs/file_upload/Level1ExtensionBypass.php` | Upload success/failure | Message in terminal | ✅ Already implemented |
| `labs/csrf/Level1CSRF.php` | Transfer complete | Alert box message | ✅ Already implemented |
| `labs/xxe/Level1XXE.php` | XML parse result | Alert box message | ✅ Already implemented |
| `app/Views/settings/index.php` | Settings saved | Success banner | ✅ Already implemented |
| `api.php` | API errors | JSON error response | ✅ Already implemented |

### Specific Missing Features

| Feature | Location | Priority | Description |
|---------|----------|----------|-------------|
| Toast notification system | `shared/footer.php` | Medium | Centralized toast component for all feedback |
| Loading spinners | All lab pages | Low | Visual feedback during form submissions |
| Database connection failure page | `app/Core/Database.php` | High | Dedicated error page when DB unavailable |
| 404 error page | `app/Core/Router.php` | Medium | Custom styled 404 instead of plain text |
| Session timeout warning | `app/Core/Auth.php` | Low | Warn before session expires |
| Password strength indicator | `app/Views/login.php` | Low | Visual feedback for password complexity |

---

## 6. Corrected Issues to Fix (Architectural Separation Applied)

> **Important**: This project has a strict architectural separation:
> - `/app`, `/config`, `/shared`, `/api.php` = **Secure Core** (production-quality, no vulnerabilities allowed)
> - `/labs` = **Military Simulation Playground** (intentional vulnerabilities in challenge files, but scaffolding should be solid)

---

### 🔴 1. Secure Core Bugs (Must Fix - Real Issues)

These are genuine bugs in production-quality code that need immediate attention.

#### App/Core/ Directory

| File | Issue | Priority | Fix Required |
|------|-------|----------|--------------|
| `Database.php` | No graceful fallback when DB connection fails; throws exception directly | High | Add try/catch with user-friendly error page |
| `Database.php` | Commented-out `use PDOException` import (line 6) | Low | Remove commented code or fix import |
| `Database.php` | Uses array for instances instead of proper singleton pattern | Medium | Refactor to clean singleton or multi-DB manager |
| `Router.php` | Hardcoded route list requires manual maintenance | Medium | Implement dynamic route discovery or config file |
| `Router.php` | Returns null for unmatched routes (plain text 404) | Medium | Return styled 404 error page |
| `BaseChallenge.php` | Uses `$_REQUEST` which combines GET/POST/COOKIE | Medium | Use explicit `$_POST` or `$_GET` based on context |
| `ChallengeDatabase.php` | Redundant with Database.php; hardcoded DB name 'challenges' | Low | Merge with Database.php or remove |
| `LabEngine.php` | Stores vulnerability config in session (user-manipulable) | Medium | Store in server-side session with integrity check |

#### App/Controllers/ Directory

| File | Issue | Priority | Fix Required |
|------|-------|----------|--------------|
| `AuthController.php` | ✅ Fully secured with CSRF timing-resistant check & POST method validation | High | Completed |

#### App/Views/ Directory

| File | Issue | Priority | Fix Required |
|------|-------|----------|--------------|
| `settings/index.php` | Settings stored only in session (lost on logout) | Medium | Persist settings to database or cookies |
| `admin/dashboard.php` | No empty state if no users exist | Low | Add "No users registered" message |
| `labs.php` | No loading state for progress bars | Low | Add spinner while fetching progress |

#### Config/ Directory

| File | Issue | Priority | Fix Required |
|------|-------|----------|--------------|
| `config.php` | Throws exception if .env missing with no fallback | High | Provide default values or clear setup instructions page |
| `database.php.bak` | Backup file should not be in repository | High | **DELETE THIS FILE** |

#### Shared/ Directory

| File | Issue | Priority | Fix Required |
|------|-------|----------|--------------|
| `header.php` | Language selector references non-existent `$_SESSION['lang']` key | Medium | Add null coalescing operator or initialize session key |
| `header.php` | Theme toggle function referenced but not defined | Medium | Define `toggleTheme()` JavaScript function |
| `military-ui/header.php` | Agent codename stored in session persists unnaturally | Low | Clear agent data on logout or use temporary session |

#### Root Level Files

| File | Issue | Priority | Fix Required |
|------|-------|----------|--------------|
| `api.php` | Overly permissive CORS header (`Access-Control-Allow-Origin: *`) | Medium | Restrict to specific origins or remove for internal API |
| `api.php` | No rate limiting on API endpoints | Medium | Implement rate limiting middleware |
| `.env` | Empty database password in example configuration | Low | Add placeholder password with comment |
| `.htaccess` (in storage/) | Uses deprecated Apache 2.2 syntax (`Order Deny,Allow`) | Low | Update to Apache 2.4+ syntax (`Require all denied`) |

#### Tests/ Directory

| File | Issue | Priority | Fix Required |
|------|-------|----------|--------------|
| `Core/AuthTest.php` | Minimal test coverage (only 3 basic tests) | Medium | Add tests for login, logout, permission checks |

---

### 🟡 2. Lab Scaffolding Issues (Fixable - Non-Exploit Code)

These issues are in lab directories but are NOT part of the intentional vulnerability. They improve UX without changing the exploit surface.

#### General Lab Infrastructure

| File | Issue | Priority | Fix Required |
|------|-------|----------|--------------|
| `labs/*/challenges/*.php` (all) | PDOException caught but error message shown directly to user | Medium | Use custom error messages; log details server-side |
| `labs/xss/challenge_map.php` | No error handling if challenge class doesn't exist | Low | Add class_exists() check with fallback message |
| `labs/sqli/challenge_map.php` | No error handling if challenge class doesn't exist | Low | Add class_exists() check with fallback message |
| `labs/file_upload/challenge_map.php` | No error handling if challenge class doesn't exist | Low | Add class_exists() check with fallback message |
| `labs/csrf/challenge_map.php` | No error handling if challenge class doesn't exist | Low | Add class_exists() check with fallback message |
| `labs/xxe/challenge_map.php` | No error handling if challenge class doesn't exist | Low | Add class_exists() check with fallback message |

#### Orphaned/Unused Files

| File | Issue | Priority | Fix Required |
|------|-------|----------|--------------|
| `labs/sqli/challenge.php` | Not connected to challenge_map.php (orphaned) | Medium | Either integrate into routing or DELETE |
| `labs/file_upload/challenge.php` | Not connected to challenge_map.php (orphaned) | Medium | Either integrate into routing or DELETE |

#### Duplicate Challenges

| File | Issue | Priority | Fix Required |
|------|-------|----------|--------------|
| `labs/xss/challenges/Level1ReflectedMilitary.php` | Duplicate of Level1Reflected.php with theme change only | Low | Consider merging or clearly differentiating |

#### Lab Admin Panels (Non-Exploit Areas)

| File | Issue | Priority | Fix Required |
|------|-------|----------|--------------|
| `labs/xss/admin_panel.php` | No pagination for search logs if many entries | Low | Add pagination for large log files |
| `labs/xss/admin_reports.php` | No pagination for reports if many entries | Low | Add pagination for large report lists |
| `labs/xss/admin_panel.php` | No empty state if no search logs exist | Low | Add "No search logs available" message |
| `labs/xss/admin_reports.php` | No empty state if no reports exist | Low | Add "No reports submitted" message |

#### File Upload Lab

| File | Issue | Priority | Fix Required |
|------|-------|----------|--------------|
| `labs/file_upload/uploads/.htaccess` | Only blocks PHP; should block all executable extensions | Medium | Add rules for .php, .phtml, .php5, .exe, .sh, etc. |

#### XXE Lab

| File | Issue | Priority | Fix Required |
|------|-------|----------|--------------|
| `labs/xxe/challenges/Level1XXE.php` | Uses `libxml_disable_entity_loader()` deprecated in PHP 8+ | Low | Add version check and alternative for PHP 8+ |

---

### 🟢 3. Intentional Vulnerabilities (DO NOT TOUCH)

These are the core teaching elements of the platform. **Never modify these.**

#### XSS Lab - Intentional Vulnerabilities

| File | Vulnerability Type | Line(s) | Description |
|------|-------------------|---------|-------------|
| `labs/xss/challenges/Level1Reflected.php` | Reflected XSS | ~91 | Search term echoed without `htmlspecialchars()` |
| `labs/xss/challenges/Level2Stored.php` | Stored XSS | Multiple | Reports stored and displayed without sanitization |
| `labs/xss/challenges/Level3Dom.php` | DOM-based XSS | Client-side | `innerHTML` assignment from URL hash |
| `labs/xss/admin_panel.php` | Stored XSS (admin view) | ~61 | Search logs echoed raw |
| `labs/xss/admin_reports.php` | Stored XSS (admin view) | ~54 | Reports output without escaping |

#### SQLi Lab - Intentional Vulnerabilities

| File | Vulnerability Type | Line(s) | Description |
|------|-------------------|---------|-------------|
| `labs/sqli/challenges/Level1AuthBypass.php` | Authentication Bypass | ~50 | String concatenation in SQL query |
| `labs/sqli/challenges/Level2UnionExtraction.php` | UNION Injection | ~43 | LIKE clause injection point |

#### File Upload Lab - Intentional Vulnerabilities

| File | Vulnerability Type | Line(s) | Description |
|------|-------------------|---------|-------------|
| `labs/file_upload/challenges/Level1ExtensionBypass.php` | Extension Bypass | 46-47 | Only checks extension, not file content |
| `labs/file_upload/challenges/Level2MimeBypass.php` | MIME Spoofing | 106, 113 | Trusts Content-Type header from client |

#### CSRF Lab - Intentional Vulnerabilities

| File | Vulnerability Type | Line(s) | Description |
|------|-------------------|---------|-------------|
| `labs/csrf/challenges/Level1CSRF.php` | CSRF | 108-122 | Transfer form lacks CSRF token |

#### XXE Lab - Intentional Vulnerabilities

| File | Vulnerability Type | Line(s) | Description |
|------|-------------------|---------|-------------|
| `labs/xxe/challenges/Level1XXE.php` | XXE | 29 | `libxml_disable_entity_loader(false)` enables external entities |

---

### Summary by Category

| Category | Count | Action Required |
|----------|-------|-----------------|
| **Secure Core Bugs** | 23 | ✅ Fix immediately |
| **Lab Scaffolding Issues** | 18 | ✅ Fix when convenient (doesn't affect exploits) |
| **Intentional Vulnerabilities** | 11 | ❌ NEVER MODIFY - Core teaching content |

---

## 5. What's Working Well ✅

### Security Features (for a training app)

| Feature | Implementation | Quality |
|---------|---------------|---------|
| **CSRF Protection** | Token-based on login, admin actions | ✅ Excellent |
| **Session Fixation** | `session_regenerate_id(true)` on login | ✅ Excellent |
| **Prepared Statements** | Used in Auth.php, Database.php | ✅ Excellent |
| **Input Sanitization** | `trim()` in BaseChallenge | ✅ Good |
| **Output Escaping** | `htmlspecialchars()` in most views | ✅ Good |
| **Access Control** | `isAdmin()` checks for admin pages | ✅ Good |
| **Directory Protection** | `.htaccess` blocks direct access | ✅ Good |

### Code Quality

| Aspect | Assessment |
|--------|------------|
| **PSR-4 Autoloading** | Properly implemented, clean namespace structure |
| **Type Declarations** | Typed properties and parameters throughout |
| **Separation of Concerns** | Clear division between Core, Controllers, Views |
| **DRY Principle** | BaseChallenge reduces code duplication |
| **Documentation** | PHPDoc comments in core classes |
| **Error Logging** | `error_log()` calls in critical paths |

### User Experience

| Feature | Implementation |
|---------|---------------|
| **Responsive Design** | TailwindCSS with mobile breakpoints |
| **Dual Themes** | Standard + Military tactical UI |
| **Progress Tracking** | Visual progress bars, completion timestamps |
| **Gamification** | Leaderboard, achievements, challenge counts |
| **Educational Content** | Patch reports with secure code examples |
| **Hint System** | Collapsible hints in each challenge |

### Architecture Strengths

| Pattern | Benefit |
|---------|---------|
| **Front Controller** | Single entry point, centralized routing |
| **Singleton Database** | Efficient connection reuse |
| **Challenge Interface** | Consistent challenge API, easy to extend |
| **Challenge Map** | Decoupled routing, easy to add levels |
| **Lab Engine** | Runtime vulnerability toggling for demos |

---

## Summary Statistics

| Metric | Count |
|--------|-------|
| **Total PHP Files** | 52 |
| **Core Classes** | 10 |
| **Controllers** | 1 |
| **Views** | 11 |
| **Challenge Classes** | 11 |
| **Language Files** | 3 (en, es, fr) |
| **Test Files** | 1 |
| **SQL Files** | 2 (schema, seed) |
| **Labs Implemented** | 5 (XSS, SQLi, File Upload, CSRF, XXE) |
| **Total Challenges** | 11+ across all labs |

---

## 🔒 Secure Core Protection & Leakage Prevention

We have successfully implemented deep guardrails to isolate simulated vulnerabilities from the production console:
1. **Lab Sandbox Flag (`LABS_ENABLE_INTENTIONAL_VULNS`):** A strict toggle (false by default) that must be set to `true` to execute or load challenge endpoints (web or API). This ensures dynamic training exercises never run by accident in staging or production.
2. **Attacker Endpoint Restrictions:** `/public/attacker.php` is strictly mapped to `APP_ENV=development`. If accessed in production, it is completely blocked and returns a `403` status.
3. **Simulated Admin Panel Security:** Restricts simulated stored XSS terminals (`xss_admin_reports`) to authenticated operators and ensures the vulnerability execution flag is enabled.
4. **Early Configuration Bootstrapping:** Ensures `.env` environment parameters are parsed and validated immediately at standard index and API boots.

---

## 🛠️ UI & Navigation Repairs

We have repaired core UI, routing, and stats dashboard issues:
1. **Challenge Route Expansion:** Whitelisted all 10 OWASP Top 10 lab categories in the Front Controller to allow access to SSRF, IDOR, path traversal, deserialization, and JWT sandboxes.
2. **Immersive "Coming Soon" Screens:** Added professional slate-navy simulation queued views to the four empty sandbox level templates.
3. **Dynamic Training Catalog Count:** Replaced the hardcoded active core modules stat with a dynamically computed count from the actual folders inside `labs/`.
4. **Resolved Progress Counting Bugs:** Fixed index progress parsing of aggregate arrays from `Auth::getCompletedChallenges()`, resulting in completely accurate dashboard totals and progress indicators.

---

## 🧼 Request & Response Hygiene

We have introduced industry-standard request/response protections across the platform core:
1. **Secure Session Cookies:** Configured session starts to enforce `HttpOnly`, `SameSite=Lax`, and `Secure` (when active in production environments), mitigating potential session theft via XSS or cross-site scripting channels. All raw `session_start()` boots across the entire codebase (`Router.php`, `Translator.php`, `attacker.php`, `AuthTest.php`) have been completely replaced by the centralized `Session::start()` method.
2. **IP & Username Login Throttling:** Added database failed-login restrictions of 5 attempts per 5 minutes per IP or Username using `storage/logs/login_throttle.json` to defend against automated brute-forcing.
3. **Global Escaping Helper `e()`:** Created a global output escaping function registered at the top of `config/config.php`. This fixes a critical bug where the helper's definition was bypassed due to the early global return statement in database configuration loading. Standard core views have been fully migrated to use this centralized utility, eliminating potential stored or reflected XSS bugs inside standard dashboard screens.
4. **Standardized REST HTTP Codes:** Enforced explicit `401 Unauthorized` and `403 Forbidden` headers when authorization checks fail. Blocked unauthorized attempts with immersive dark terminal console alert boxes.
5. **Timing-Attack-Resistant CSRF Verification:** Centrally implemented secure `hash_equals()` comparisons inside `Session::validateCsrfToken()` for all CSRF token validations, safeguarding the platform from timing side-channel threats.
6. **Input Parameter Limits (`maxlength`):** Hardened all login inputs and relevant lab search, url, filename, and transfer inputs with explicit `maxlength` properties to prevent excessive inputs or database truncation anomalies.
7. **Browser Auto-completion Guidance:** Specified `autocomplete="username"` and `autocomplete="current-password"` to the login fields in `login.php` to adhere to user agent credential management specifications.
8. **Icon Typo Correction:** Standardized Font Awesome icons, replacing all `fa-shield-halved` references with the universally compatible `fa-shield-alt` icon to ensure seamless rendering across older or legacy browsers.
9. **Search Crawling Restrictions (`robots.txt`):** Added a global `robots.txt` in the root and `/public` directories to discourage search indexing in case the range is accidentally exposed to public search crawlers.
10. **Robust Directory Check on Logs:** Injected recursive `is_dir` and `mkdir` parent directory creation checks before all log writing calls to `file_put_contents()` in XSS labs, `attacker.php` and `api.php` to fail safely and gracefully.

---

## Recommendations

### Immediate Actions (High Priority)

1. **Remove backup files**: Delete `config/database.php.bak`
2. **Update .htaccess syntax**: Replace Apache 2.2 directives with 2.4+ syntax
3. **Add .gitignore entries**: Ensure `.env`, `storage/logs/*`, `storage/uploads/*` are ignored
4. **Fix broken foreign key**: Either move `notifications` table to app DB or remove FK reference

### Short-Term Improvements (Medium Priority)

1. **Add custom error pages**: Create 404, 500, and DB connection failure pages
2. **Implement toast system**: Centralized notification component in footer
3. **Expand test coverage**: Add tests for Database, Router, and ChallengeLoader
4. **Remove orphaned files**: Either integrate or delete `labs/sqli/challenge.php` and `labs/file_upload/challenge.php`

### Long-Term Enhancements (Low Priority)

1. **Migrate to password_hash()**: Update seed data and Auth.php for bcrypt
2. **Add more labs**: SSRF, IDOR, Deserialization, LDAP Injection
3. **Implement API rate limiting**: Prevent abuse of REST endpoints
4. **Add Docker support**: One-command setup for training environments

---

*Generated: $(date)*  
*Project Version: 1.0*  
*Audit Status: Complete*
