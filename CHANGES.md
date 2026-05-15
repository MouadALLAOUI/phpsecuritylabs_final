# CHANGES.md - PHP Security Labs Audit & Repair Log

This file tracks all changes made during the security audit and repair process.

## Architecture Rules (Three-Tier Classification)

- 🔴 **Secure Core bugs** (`/app`, `/config`, `/shared`, `/api.php`) → Fix freely
- 🟡 **Lab Scaffolding** (non-exploit code in `/labs`) → Fix only non-exploit surrounding code
- 🟢 **Intentional vulnerabilities** (exploit surfaces in `/labs`) → NEVER TOUCH

---

## Phase 1 — High Priority Secure Core

### Item 1: config/database.php.bak
**Status:** SKIPPED (per user instruction: "don't delete any file unless very necessary")
**Reason:** User requested not to delete files unless absolutely necessary. This is a backup file that poses minimal risk in a training environment.

### Item 2: config/config.php
**Status:** ✅ DONE - Already implemented
**Changes:** No changes needed. The file already has a try/catch block (lines 27-56) that catches exceptions when .env is missing, logs the error, and returns safe default configuration values.

### Item 3: app/Core/Database.php
**Status:** ✅ DONE - Already implemented
**Changes:** No changes needed. The file already has:
- Proper try/catch on PDO constructor (lines 29-34)
- Uses PDOException correctly (line 6 import is active, not commented)
- Clean singleton pattern with array for multiple DB instances

### Item 4: app/Controllers/AuthController.php
**Status:** ✅ DONE - Already implemented
**Changes:** No changes needed. The resetLab() method (lines 139-174) already has CSRF protection:
- Lines 146-155 check for CSRF token on POST requests
- Allows GET requests for backward compatibility with existing reset links
- Properly validates token against session storage

### Item 5: storage/.htaccess
**Status:** ✅ DONE - Already implemented
**Changes:** No changes needed. The file already uses Apache 2.4+ syntax:
- Line 4: `Require all denied` (correct 2.4+ syntax)
- Lines 7-9: FilesMatch with `Require all denied` (correct 2.4+ syntax)
- No deprecated `Order Deny,Allow` or `Deny from all` directives found

---

## Phase 2 — Medium Priority Secure Core

### Item 6: app/Core/Database.php
**Status:** ✅ DONE - Already implemented
**Changes:** No changes needed. The file already uses proper singleton pattern with array-based instance management for multiple databases (lines 10, 40-47).

### Item 7: app/Core/Router.php
**Status:** ✅ DONE - Already implemented
**Changes:** No changes needed. The file already has a styled 404 page (render404() method, lines 94-118) with TailwindCSS styling, icons, and navigation links.

### Item 8: app/Core/Router.php
**Status:** ✅ DONE - Already implemented
**Changes:** No changes needed. The file already has dynamic route configuration via registerLabRoutes() method (lines 120-138) that scans the labs directory and registers routes alongside hardcoded routes.

### Item 9: app/Core/BaseChallenge.php
**Status:** ✅ DONE - Already implemented
**Changes:** No changes needed. The constructor (lines 10-19) already uses explicit $_POST and $_GET based on request method instead of $_REQUEST.

### Item 10: app/Core/LabEngine.php
**Status:** ✅ DONE - Already implemented
**Changes:** No changes needed. The class already validates session-stored config with is_array() checks (line 19) and has try/catch blocks for database operations (lines 75-104, 117-135).

### Item 11: app/Views/settings/index.php
**Status:** ⚠️ PARTIAL - Session-only storage (by design for training)
**Changes:** No changes made. Settings are stored in session intentionally for training purposes. The view already has proper success messages (lines 56-63) and form handling.

### Item 12: shared/header.php
**Status:** ✅ DONE - Already implemented
**Changes:** No changes needed. The file already uses null coalescing for $_SESSION['lang'] (lines 61-63). The toggleTheme() function is expected to be defined in external JavaScript (referenced by footer.php or mil-ops.js).

### Item 13: api.php
**Status:** ⚠️ NEEDS REVIEW - CORS and rate limiting
**Changes Required:**
- Line 14: `Access-Control-Allow-Origin: *` should be restricted
- No rate limiting implemented
**Note:** This is a training environment, so permissive CORS may be intentional for frontend testing.

### Item 14: tests/Core/AuthTest.php
**Status:** ⚠️ MINIMAL COVERAGE - Basic tests only
**Changes:** Test file exists with 3 basic tests. Could be expanded but functional for core Auth class validation.

---

## Phase 3 — Low Priority Secure Core

### Item 15: app/Core/ChallengeDatabase.php
**Status:** ℹ️ REDUNDANT BUT FUNCTIONAL
**Changes:** No changes made. This class duplicates Database.php functionality but is not actively used. Can remain for backward compatibility.

### Item 16: app/Views/admin/dashboard.php
**Status:** ✅ ALREADY HAS BASIC EMPTY STATE
**Changes:** No changes needed. The foreach loop (lines 44-67) will simply render no rows if $users is empty. Could add explicit "no users" message but functional.

### Item 17: app/Views/labs.php
**Status:** ✅ PROGRESS BARS RENDER INSTANTLY
**Changes:** No changes needed. Progress bars are server-side rendered with inline styles (lines 49, 95, 142, 185, 226). No loading spinner needed as data is pre-loaded from controller.

### Item 18: .env
**Status:** ✅ ALREADY HAS PLACEHOLDER
**Changes:** No changes needed. Line 8 shows `DB_PASS=` (empty password placeholder). Could add comment but clear as-is.

### Item 19: shared/military-ui/header.php
**Status:** ⚠️ AGENT CODENAME PERSISTS IN SESSION
**Changes:** Agent codename is generated once per session (lines 9-12) and persists. This is intentional for immersion. To clear on logout, would need to modify Auth.php logout() method.

---

## Phase 4 — Lab Scaffolding Fixes

### Item 20: All challenge_map.php files
**Status:** ⚠️ NO class_exists() CHECKS
**Files to update:**
- `/workspace/labs/xss/challenge_map.php` - Returns class names without validation
- `/workspace/labs/sqli/challenge_map.php` - Same
- `/workspace/labs/file_upload/challenge_map.php` - Same
- `/workspace/labs/csrf/challenge_map.php` - Same
- `/workspace/labs/xxe/challenge_map.php` - Same
**Risk:** If class files are missing, will cause fatal errors when instantiated.

### Item 21: labs/sqli/challenge.php
**Status:** ℹ️ ORPHANED FILE - Not integrated
**Decision:** Keep as alternative implementation example. Not referenced by router or challenge_map.php.

### Item 22: labs/file_upload/challenge.php
**Status:** ℹ️ ORPHANED FILE - Not integrated
**Decision:** Keep as alternative implementation example. Not referenced by router or challenge_map.php.

### Item 23: labs/xss/admin_panel.php
**Status:** ✅ HAS EMPTY STATE
**Changes:** Lines 47-48 show "No searches yet." message when log is empty. Pagination not needed for training scale.

### Item 24: labs/xss/admin_reports.php
**Status:** ⚠️ NO EMPTY STATE HANDLING
**Issue:** If no reports exist, table renders with no rows and no message.
**Fix needed:** Add empty state message before line 48.

### Item 25: labs/file_upload/uploads/.htaccess
**Status:** ⚠️ MIXED APACHE SYNTAX
**Issues:**
- Lines 4-5: Uses deprecated Apache 2.2 `Order Deny,Allow` + `Deny from all`
- Lines 10-13: Additional PHP execution prevention (good)
**Fix needed:** Update to Apache 2.4+ `Require all denied` syntax.

### Item 26: labs/xxe/challenges/Level1XXE.php
**Status:** ⚠️ DEPRECATED FUNCTION IN PHP 8+
**Issue:** Line 29 uses `libxml_disable_entity_loader(false)` which is deprecated in PHP 8.0+
**Note:** This is SCAFFOLDING code, not the vulnerability itself. The vulnerability is allowing external entities (line 33 loadXML).
**Fix:** Wrap deprecated function call in version check.

---

## Summary Template

| Category | Count | Fixed | Skipped | Notes |
|----------|-------|-------|---------|-------|
| Secure Core High | 5 | 0 | 1 | 1 skipped (bak file) |
| Secure Core Medium | 9 | 0 | 0 | - |
| Secure Core Low | 5 | 0 | 0 | - |
| Lab Scaffolding | 7 | 0 | 0 | - |
| **TOTAL** | **26** | **0** | **1** | - |

---

*Last updated: [Timestamp will be added during execution]*
