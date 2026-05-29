> **Auto-generated improvement roadmap.** This roadmap was generated from a read-through of the current PHP Security Labs codebase and should be reviewed by maintainers before implementation, especially because some intentionally vulnerable lab code must remain exploitable for teaching purposes.

# PHP Security Labs Improvement Roadmap

## 🔴 NECESSARY (Critical Fixes & Foundation)

- [ ] Replace MD5 password verification in `app/Core/Auth.php` with `password_hash()` / `password_verify()` and add a migration path for existing seeded users.
- [ ] Replace MD5 password hashes in `database/seed.sql` with modern bcrypt/Argon2 hashes so fresh installs are not trained around weak password storage.
- [ ] Remove tracked `.env` secrets from version control, rotate any reused local credentials, and commit only a safe `.env.example`.
- [ ] Fix `.gitignore` formatting by removing the surrounding Markdown code fences so patterns like `.env` and `*.log` are actually interpreted by Git.
- [ ] Disable `APP_DEBUG=true` by default and fail closed in production when required environment variables are missing.
- [ ] Stop falling back to root/empty database credentials in `config/config.php`; require explicit credentials outside local development.
- [ ] Read `DB_PORT` from the environment instead of hardcoding port `3306` in `config/config.php`.
- [ ] Remove or replace `config/database.php.bak`, because backup config files often expose stale credentials and confuse deployment.
- [ ] Add startup validation for required PHP extensions (`pdo_mysql`, `json`, `libxml`, `fileinfo`, `mbstring`, `session`) with clear setup guidance.
- [ ] Fix `api.php` so it does not `require_once index.php`, which currently starts normal page routing before API dispatch and can emit HTML or exit unexpectedly.
- [ ] Add a dedicated API bootstrap that loads constants, autoloading, config, and session state without executing the web UI router.
- [ ] Restrict `api.php?action=challenge` to a whitelist of known lab slugs before building `labs/{$lab}/challenge_map.php` paths.
- [ ] Normalize all challenge maps to one contract; older maps return class strings while newer maps return metadata arrays, which breaks `ChallengeLoader` and `api.php` for SSRF, IDOR, JWT, path traversal, and deserialization.
- [ ] Update `ChallengeLoader::load()` to handle the canonical challenge map shape and report structured errors when maps are invalid.
- [x] Add missing route support in `index.php` for SSRF, IDOR, path traversal, deserialization, and JWT labs that already have directories.
- [ ] Add missing level files/classes referenced by maps, such as `Level2AdvancedSSRF` and `Level2AdvancedIDOR`, or remove those map entries until implemented.
- [x] Fix the blank view templates in `labs/deserialization/views/level1.php`, `labs/idor/views/level1.php`, `labs/jwt/views/level1.php`, and `labs/path_traversal/views/level1.php`.
- [ ] Update the labs overview to list every available lab instead of only the original XSS, SQLi, file upload, CSRF, and XXE modules.
- [x] Fix stale home dashboard copy that says `3 Modules Available` even though the repository contains more lab categories.
- [x] Fix `app/Views/home.php` progress logic: `getCompletedChallenges()` returns aggregate rows, but the page treats them like detailed rows containing `lab_name`.
- [x] Remove the unused `$userId` parameter passed to `Auth::getCompletedChallenges()` in `app/Views/home.php` or add a typed optional parameter intentionally.
- [x] Fix the progress total calculation on the home page so it sums aggregate challenge counts instead of counting lab groups.
- [x] Generate CSRF tokens centrally; several forms output `$_SESSION['csrf_token'] ?? bin2hex(random_bytes(32))`, which can render a token that is never stored in session.
- [x] Remove GET-based lab reset support in `AuthController::resetLab()` and require POST + CSRF for all destructive reset actions.
- [x] Validate `labName` in user and admin reset flows against a whitelist to prevent deleting unexpected `lab_progress` rows.
- [x] Add authorization checks to XSS admin pages so `xss_admin_reports` and XSS admin panels cannot be browsed by non-admin users unless intentionally part of a lab scenario.
- [ ] Convert `labs/xss/admin_reports.php?mark_reviewed=...` from GET to POST with CSRF protection because it mutates database state.
- [x] Separate intentionally vulnerable lab endpoints from secure core endpoints with explicit comments, routes, and guardrails to prevent accidental “fixes” of teaching vulnerabilities.
- [x] Add an environment flag such as `LABS_ENABLE_INTENTIONAL_VULNS=true` to prevent accidental public deployment of deliberately vulnerable exercises.
- [x] Protect `/public/attacker.php` behind local/demo-only configuration; it records stolen cookies to logs and should never be active in production.
- [ ] Ensure `storage/logs` exists and is writable before `api.php`, XSS labs, or attacker endpoint attempt to write log files.
- [ ] Move API rate-limit storage out of a JSON file that can grow indefinitely and race under concurrency; use database, Redis, or a locked rotating file abstraction.
- [ ] Prevent direct browser access to sensitive lab directories on non-Apache deployments by adding Nginx/Caddy guidance and application-level route guards.
- [x] Add secure session cookie parameters (`HttpOnly`, `Secure`, `SameSite=Lax/Strict`) before `session_start()` in the central bootstrap.
- [ ] Add session idle timeout and absolute session lifetime checks to reduce risk from leaked training sessions.
- [ ] Regenerate CSRF tokens after successful login and logout to avoid token fixation across identity changes.
- [x] Add login throttling or lockout to `AuthController::handleLogin()` to slow credential stuffing against seeded accounts.
- [ ] Stop displaying reusable test credentials directly on the login form outside a clearly marked local/demo mode.
- [ ] Add a proper registration or user provisioning flow, or document that accounts are seed-only.
- [ ] Fix double document markup in military-themed pages where `shared/military-ui/header.php` already emits full HTML but is included inside another `<head>`/`<body>` wrapper.
- [ ] Standardize page layout includes so pages do not mix `shared/header.php`, `shared/sidebar.php`, and `shared/military-ui/header.php` in incompatible combinations.
- [x] Add consistent HTTP status codes for unauthorized access (`401` unauthenticated, `403` forbidden) instead of redirecting every failure to home.
- [ ] Replace raw `Exception` codes in `api.php` with a safe custom exception type so arbitrary exception codes do not become invalid HTTP status codes.
- [ ] Return generic API errors in production and log details server-side.
- [ ] Add input length limits for usernames, passwords, lab names, challenge IDs, uploaded filenames, XML input, SSRF URLs, and search fields.
- [x] Add a central output escaping helper for HTML, attributes, JavaScript strings, URLs, and JSON contexts.
- [ ] Audit every intentional raw output in lab views and mark it with an explicit `INTENTIONAL_VULNERABILITY` comment plus a patched counterpart.
- [ ] Add a production-safe patched mode for each lab so instructors can demonstrate the fix without editing exploit code.
- [ ] Update `.htaccess` to block direct access to `config`, `.env`, `*.bak`, Markdown planning files, test files, and SQL seed/schema files where appropriate.
- [ ] Add an application health check route that validates DB connectivity, migrations, writable storage, and required extensions.
- [ ] Add database migration tooling instead of relying on manual execution of `database/schema.sql` and `database/seed.sql`.
- [ ] Add unique indexes that match `ON DUPLICATE KEY UPDATE` usage in `lab_progress` so repeated completions update as intended.
- [ ] Make admin dashboard counts data-driven from available challenge maps rather than hardcoding XSS, SQLi, and upload totals.
- [ ] Fix leaderboard codename display; `Auth::getLeaderboard()` does not select a `codename`, so the view falls back to `Unknown`.
- [ ] Record `last_login_at` and failed-login timestamps for account security and admin visibility.
- [ ] Add robust 404 rendering for missing challenges instead of plain text `Challenge not found`.
- [ ] Add an install-time warning banner if the app is reachable from a non-localhost host while intentional vulnerabilities are enabled.

## 🟠 GREAT IDEAS (High-Value Enhancements)

- [ ] Build a dynamic lab catalog by scanning `labs/*/challenge_map.php` and showing title, description, implemented levels, and locked/unlocked state.
- [ ] Add per-lab landing pages that explain objectives, prerequisites, expected exploit outcome, and patched remediation.
- [ ] Add “vulnerable mode” and “patched mode” toggles for XSS, SQLi, file upload, CSRF, XXE, SSRF, IDOR, path traversal, deserialization, and JWT labs.
- [ ] Add guided hints with progressive reveal and optional score penalties.
- [ ] Add solution walkthrough pages that are hidden until a learner completes or explicitly unlocks a challenge.
- [ ] Add a student dashboard with current streak, completion percentage, recent activity, and next recommended lab.
- [ ] Add instructor dashboard filters by user, lab, role, completion date, and challenge difficulty.
- [ ] Add CSV export for progress, leaderboard, and admin roster data.
- [ ] Add printable completion certificates for learners who finish all required modules.
- [ ] Add achievements/badges such as “First XSS”, “Union Operator”, “Cookie Collector”, “JWT Forger”, and “Defense Engineer”.
- [ ] Add a scoreboard mode that weights challenges by difficulty instead of counting each challenge equally.
- [ ] Add time-to-complete tracking per challenge for leaderboard tie-breakers and instructor analytics.
- [ ] Add search and filtering on the labs page by OWASP category, difficulty, estimated time, and implemented status.
- [ ] Add a “resume last challenge” card on the home dashboard.
- [ ] Add a notifications bell backed by the existing `notifications` table for completions, new labs, and instructor announcements.
- [ ] Add profile settings for display name, preferred language, theme, notification preferences, and password change.
- [ ] Persist theme/language preferences in `lab_settings` or the `users` table instead of only session/cookie/localStorage.
- [ ] Add a true dark mode to the Tailwind-based core pages so they match the military UI theme quality.
- [ ] Add a PWA manifest and offline shell for reading lab instructions while offline.
- [ ] Add RSS/Atom or JSON feed for newly added labs and changelog updates.
- [ ] Add admin-configurable lab ordering and prerequisite chains.
- [ ] Add “classroom mode” where instructors can create cohorts and assign lab bundles.
- [ ] Add seeded demo personas for student, instructor, and admin roles with role-specific UI.
- [ ] Add a “reset all my progress” account action with confirmation, CSRF, and audit logging.
- [ ] Add per-challenge notes so students can keep private remediation notes.
- [ ] Add markdown-rendered remediation content with code examples for each vulnerability.
- [ ] Add side-by-side vulnerable vs patched code explanations for each level.
- [ ] Add mini quizzes after each lab to reinforce secure coding concepts.
- [ ] Add tags for OWASP Top 10, CWE IDs, affected components, and defense techniques.
- [ ] Add shareable, privacy-safe completion cards that exclude sensitive usernames or emails.
- [ ] Add an “import/export lab pack” convention so new labs can be dropped in with a manifest.
- [ ] Add a lab authoring guide plus scaffolding command for new challenge maps, classes, and views.
- [ ] Add a route inspector/debug page in local mode to show registered pages and lab challenges.
- [ ] Add localized content coverage reports showing which translation keys are unused or missing in English/French.
- [ ] Add an instructor-only “simulate completion” action for demos and workshops.
- [ ] Add Docker Compose with PHP, Apache/Nginx, MySQL, and optional phpMyAdmin/Adminer for one-command setup.
- [ ] Add a seeded reset script that recreates databases from schema and seed files for workshops.
- [ ] Add Makefile or task runner commands for linting, tests, DB reset, and local server startup.
- [ ] Add structured audit logs for login, logout, challenge completion, admin reset, settings changes, and API access.
- [ ] Add an admin audit log viewer with pagination and filtering.
- [ ] Add API endpoints for lab catalog and user progress after the API bootstrap is separated from `index.php`.
- [ ] Add OpenAPI documentation for public/internal API endpoints.
- [ ] Add a consistent flash-message component for success, warning, error, and info states.
- [ ] Add a global error page for uncaught exceptions with a request ID for support.
- [ ] Add database-backed feature flags for enabling experimental labs.
- [ ] Add support for HTTPS-aware base URLs and reverse proxy headers in deployment config.
- [ ] Add canonical URLs and structured metadata for public landing/documentation pages.

## 🟡 MEDIUM IDEAS (Useful UX Improvements)

- [ ] Add keyboard-accessible mobile navigation to the global header; current nav is hidden on small screens without a visible menu replacement.
- [ ] Add `aria-label` text to icon-only buttons such as the theme toggle and military UI controls.
- [x] Fix the Font Awesome icon typo `fa-shield-haltered` to a valid shield icon everywhere it appears.
- [ ] Add skip-to-content links to both standard and military layouts.
- [ ] Add focus outlines that are visible in military green/amber themes.
- [ ] Add breadcrumb navigation for `Labs > XSS > Level 1` style pages.
- [ ] Add a table of contents to long challenge pages, especially XXE and multi-step exploit walkthroughs.
- [ ] Add “Back to labs” and “Next challenge” buttons consistently on every challenge render.
- [ ] Add empty states for progress, logs, leaderboard, admin tables, and notifications using a shared component.
- [ ] Add loading states for API-driven widgets and avoid showing static “live” labels before data is loaded.
- [ ] Add copy-to-clipboard buttons for payload examples, curl commands, and JWT tokens.
- [ ] Add syntax highlighting for SQL, XML, HTML, JavaScript, PHP, and HTTP request snippets.
- [ ] Add collapsible “payload lab notebook” sections so challenge pages do not become overwhelming.
- [ ] Add validation messages near fields instead of only top-level alerts.
- [ ] Preserve form input after failed login, failed lab submission, or validation errors where safe.
- [ ] Add consistent date formatting and timezone labeling for completion timestamps.
- [ ] Add relative timestamps such as “completed 3 minutes ago” alongside exact timestamps.
- [ ] Add pagination to admin user progress if the roster grows beyond a classroom size.
- [ ] Add pagination or virtual scrolling to the attacker cookie log page.
- [ ] Add sorting controls to the leaderboard and admin dashboard.
- [ ] Add lab difficulty badges and estimated completion times.
- [ ] Add “implemented / coming soon” badges so links do not lead to empty templates or 404s.
- [ ] Add disabled states for buttons while forms are submitting to avoid duplicate actions.
- [ ] Add accessible confirmation modals instead of native `confirm()` for reset actions.
- [ ] Add toast notifications for settings updates, reset completion, and challenge completion.
- [ ] Add an announcement banner for workshop instructions or maintenance notices.
- [ ] Add a command-palette style quick switcher for labs and pages.
- [ ] Add responsive table alternatives/cards for admin and leaderboard pages on mobile.
- [ ] Add consistent page titles and `<meta name="description">` values for all pages.
- [ ] Add favicon variants and web app icons beyond the current SVG favicon.
- [ ] Add visual distinction between secure core pages and intentionally vulnerable lab pages.
- [ ] Add a “danger zone” section on settings/profile for destructive actions.
- [ ] Add a language selector label for screen readers and non-visual navigation.
- [ ] Add a reduced-motion toggle for CRT effects, blinking text, and animated logs.
- [ ] Add a high-contrast theme for accessibility beyond the military aesthetic.
- [ ] Add localized strings to replace hardcoded English copy in all views, not just selected labels.
- [ ] Add consistent capitalization for SQLi/SQL Injection, XSS, File Upload, CSRF, XXE, SSRF, IDOR, JWT, and Path Traversal.
- [ ] Add inline help text for why certain lab behavior is intentionally insecure.
- [ ] Add a “report issue with this lab” link that opens a prefilled GitHub issue template or local contact guidance.
- [ ] Add a small setup status card showing whether databases and seed data are installed.
- [ ] Add recent completions feed to the home dashboard.
- [ ] Add progress bars that compute totals from challenge maps instead of hardcoded divisors.
- [ ] Add icons and descriptions for newer labs in `shared/sidebar.php` and the military sidebar.
- [ ] Add route-specific active states for all lab levels and admin pages.
- [ ] Add a “view source” link per challenge to show relevant vulnerable and patched snippets safely.
- [ ] Add better 404 suggestions based on closest known lab/page slug.
- [ ] Add a maintenance mode page for database migrations or classroom resets.

## 🟢 TIPS IDEAS (Polish & Nice-to-Have)

- [ ] Add rotating cybersecurity quotes to the home dashboard.
- [ ] Add subtle terminal boot animation on first visit, with an option to skip permanently.
- [ ] Add celebratory confetti or terminal “mission complete” animation when a challenge is solved.
- [ ] Add collectible challenge badges with military-style patch artwork.
- [ ] Add seasonal themes such as “Cyber Winter Ops” or “Black Hat Briefing Mode”.
- [ ] Add an optional typewriter effect for mission briefings, respecting reduced-motion settings.
- [ ] Add keyboard shortcuts such as `g l` for Labs, `g p` for Profile, and `/` for search.
- [ ] Add an easter egg achievement for completing all first levels.
- [ ] Add randomized agent codenames with deterministic assignment per user.
- [ ] Add “classified document” styling to remediation summaries.
- [ ] Add a compact “lab timer” widget that can be started, paused, and reset locally.
- [ ] Add a terminal-style “mission log” that records local UI milestones during a session.
- [ ] Add optional sound effects for alerts and completions, disabled by default.
- [ ] Add custom loading messages per lab category.
- [ ] Add a “daily challenge” highlight card on the labs page.
- [ ] Add “Did you know?” security facts in empty states.
- [ ] Add printable cheat sheets for common payload types.
- [ ] Add a visual map of the OWASP Top 10 with completed nodes highlighted.
- [ ] Add small hover previews for locked or coming-soon labs.
- [ ] Add a random “training tip” in the footer.
- [ ] Add “copy permalink” buttons for challenge sections.
- [ ] Add a personalized greeting that uses the learner’s role and completion history.
- [ ] Add a “focus mode” that hides sidebars while solving a challenge.
- [ ] Add a themed 500 error page that still avoids leaking stack traces.
- [ ] Add terminal-style skeleton loaders for military UI panels.
- [ ] Add a visual “secure core vs vulnerable lab” legend.
- [ ] Add tiny progress sparks/charts on lab cards.
- [ ] Add badge rarity tiers for achievements.
- [ ] Add anonymized class leaderboard aliases for privacy-friendly workshops.
- [ ] Add a “random unsolved lab” button.

## 📊 Performance Optimizations

- [ ] Replace Tailwind CDN usage with a built CSS build artifact for production and purge unused classes.
- [ ] Bundle and pin third-party assets locally or via Subresource Integrity for deterministic classroom environments.
- [ ] Defer non-critical JavaScript in `shared/military-ui/mil-ops.js` and standard header scripts.
- [ ] Split military UI JavaScript by feature so terminal effects are not loaded on every page.
- [ ] Replace repeated inline styles with shared CSS classes to reduce page size and improve cacheability.
- [ ] Add long-lived cache headers for static assets such as `public/favicon.svg`, CSS, and JS.
- [ ] Add cache-busting file hashes for local CSS/JS once a build pipeline exists.
- [ ] Avoid scanning lab directories on every request; cache discovered lab metadata in development-safe storage.
- [ ] Cache parsed challenge maps and lab totals for the labs overview and admin dashboard.
- [ ] Add database indexes for `lab_progress(user_id, lab_name, completed)`, `lab_progress(completed_at)`, and log/report tables used by dashboards.
- [ ] Replace full-table fetch + PHP `array_slice()` pagination in XSS admin pages with SQL `LIMIT` / `OFFSET`.
- [ ] Add pagination to leaderboard and admin dashboard queries before large classrooms cause slow responses.
- [ ] Reduce repeated `Database::getInstance()` and duplicate queries within a single request by passing prepared data from controllers to views.
- [ ] Avoid loading both standard and military headers/styles on the same page.
- [ ] Minify `mil-ops.css` and `mil-ops.js` for production deployments.
- [ ] Add gzip/Brotli compression guidance for Apache/Nginx deployments.
- [ ] Use `loading="lazy"` and explicit dimensions for any future images/badges to prevent layout shift.
- [ ] Reduce `setInterval` activity in animated system logs when the tab is hidden.
- [ ] Respect `prefers-reduced-motion` to disable expensive visual effects and CRT overlays.
- [ ] Add server timing or basic request duration logging for slow pages.
- [ ] Limit API rate-limit JSON cleanup work to periodic pruning instead of rewriting all IP buckets on every request.
- [ ] Use streaming or capped reads for log viewers instead of loading entire log files into memory.
- [ ] Add query result limits to public API endpoints such as leaderboard.
- [ ] Add `EXPLAIN` review for dashboard aggregate queries once realistic data is seeded.
- [ ] Add a small autoloader classmap or Composer autoload to avoid repeated filesystem checks.

## 🔒 Security Enhancements

- [ ] Add a Content Security Policy that allows intentional XSS labs only on isolated lab routes and keeps secure core pages strict.
- [ ] Add `X-Frame-Options` / `frame-ancestors` consistently for core pages while documenting any lab route exceptions.
- [ ] Add `Referrer-Policy`, `Permissions-Policy`, and `X-Content-Type-Options` headers globally.
- [ ] Enforce HTTPS redirects and secure cookies in production mode.
- [ ] Add trusted proxy configuration before relying on `REMOTE_ADDR`, `HTTPS`, or forwarded headers.
- [ ] Add CSRF middleware for all non-idempotent core actions instead of per-controller manual checks.
- [ ] Use `hash_equals()` for CSRF token comparisons.
- [ ] Rotate CSRF tokens periodically or per sensitive action where appropriate.
- [ ] Validate `HTTP_REFERER` usage in settings redirects to avoid open redirect behavior; redirect only to internal paths.
- [ ] Add allowlisted CORS origins from environment and avoid returning `Access-Control-Allow-Origin: *` for empty origins in API responses.
- [ ] Remove `POST` from `Access-Control-Allow-Methods` until write API endpoints exist.
- [ ] Add SameSite cookie mode that is compatible with CSRF training labs without weakening secure core routes.
- [ ] Add account password change support requiring current password and CSRF protection.
- [ ] Add password policy and breached-password checks for non-demo deployments.
- [ ] Add audit logging for failed login attempts, password changes, admin resets, and settings changes.
- [ ] Add IP/user rate limiting to login and sensitive admin actions.
- [ ] Store API rate-limit keys using hashed IP addresses to reduce sensitive log exposure.
- [ ] Sanitize filenames and randomize storage names in file upload labs even when demonstrating vulnerable checks.
- [ ] Keep uploaded files outside the web root where possible and serve them through controlled demo handlers.
- [ ] Add MIME sniffing and extension allowlist in patched file upload demonstrations.
- [ ] Add XML parser hardening examples for XXE patched mode (`LIBXML_NONET`, disabled external entities, schema limits).
- [ ] Add SSRF patched mode with URL parsing, DNS rebinding protections, private IP blocking, and protocol allowlists.
- [ ] Add JWT patched mode using a maintained library, explicit algorithm allowlist, expiry checks, issuer/audience checks, and strong secrets.
- [ ] Add deserialization patched mode avoiding PHP object deserialization of untrusted data.
- [ ] Add path traversal patched mode using canonical paths and allowlisted file IDs.
- [ ] Add IDOR patched mode with object-level authorization checks.
- [ ] Add SQLi patched mode with prepared statements and safe query builders.
- [ ] Add XSS patched mode using contextual escaping and safe DOM APIs.
- [ ] Add dependency scanning once Composer/npm dependencies are introduced.
- [ ] Add secret scanning in CI to prevent future `.env`, `.bak`, or log leaks.
- [ ] Add log redaction for cookies, session IDs, passwords, JWTs, and API tokens.
- [ ] Add log rotation and retention limits for `storage/logs/*.log`.
- [ ] Add database least-privilege guidance with separate app and labs users.
- [ ] Add explicit development warnings around seeded passwords and intentionally sensitive fake data.
- [ ] Add security headers tests to prevent regressions.

## 🧪 Testing & Quality

- [ ] Add Composer configuration so PHPUnit and autoloading are reproducible across machines.
- [ ] Make `phpunit.xml` runnable in a clean checkout by documenting/installing dependencies.
- [ ] Add PHP lint checks for every PHP file in CI.
- [ ] Add PHPStan or Psalm for static analysis of controllers, core classes, and lab scaffolding.
- [ ] Add PHPCS/PHP-CS-Fixer rules for formatting and naming consistency.
- [ ] Add unit tests for `Auth::login()` covering MD5 migration, failed login, session regeneration, and admin flags.
- [ ] Add unit tests for CSRF token generation and validation helpers.
- [ ] Add tests for `Router` dispatch of login, labs, settings, admin, patch reports, unknown routes, and lab routes.
- [ ] Add tests for `ChallengeLoader` with both valid and invalid challenge map files.
- [ ] Add tests asserting every `labs/*/challenge_map.php` entry points to an existing class implementing `ChallengeInterface`.
- [ ] Add tests for the API bootstrap to ensure JSON responses contain no leaked HTML.
- [ ] Add API tests for progress, leaderboard, challenge metadata, invalid actions, unauthenticated access, and bad parameters.
- [ ] Add database integration tests using a temporary test database or SQLite-compatible abstraction where feasible.
- [ ] Add migration tests that rebuild schema and seed data from scratch.
- [ ] Add accessibility tests with axe-core or Pa11y for core pages.
- [ ] Add browser/E2E tests for login, lab navigation, challenge completion, reset flow, settings changes, and admin reset.
- [ ] Add snapshot or DOM tests to catch double `<html>`/`<body>` rendering regressions.
- [ ] Add tests that ensure secure core pages escape user-controlled output.
- [ ] Add tests that verify intentionally vulnerable pages remain vulnerable only when lab mode is enabled.
- [ ] Add tests around `lab_progress` uniqueness and `ON DUPLICATE KEY UPDATE` behavior.
- [ ] Add mutation tests or negative tests for authorization boundaries.
- [ ] Add CI workflow to run lint, unit tests, static analysis, and secret scanning on every pull request.
- [ ] Add coverage reporting and a minimum coverage gate for secure core code.
- [ ] Add smoke tests for Docker/local install once containerization exists.
- [ ] Add visual regression tests for key pages if the UI continues to evolve.
- [ ] Add JavaScript linting for `mil-ops.js`.
- [ ] Add CSS linting for military UI styles.
- [ ] Add translation tests to ensure all keys exist in both `lang/en.json` and `lang/fr.json`.
- [ ] Add route coverage tests to ensure sidebar links, labs page links, and quick access links do not 404.
- [ ] Add performance budget checks for page weight and render time.
- [ ] Add test fixtures for each seeded role: learner, instructor/admin, and unauthenticated visitor.

## 📝 Documentation

- [ ] Fill in `README.md` with project purpose, threat model, intentional-vulnerability warning, requirements, setup, and screenshots.
- [ ] Add a prominent warning that this application is for local training and should not be exposed publicly without hardening.
- [ ] Document the three-tier architecture already described in `CHANGES.md`: secure core, lab scaffolding, and intentional vulnerabilities.
- [ ] Add installation steps for Apache/Nginx, PHP extensions, MySQL databases, schema import, seed import, and file permissions.
- [ ] Add default demo credentials and explain how to change them safely.
- [ ] Add `.env.example` documentation for every environment variable.
- [ ] Add database schema documentation describing app tables vs simulated/lab data tables.
- [ ] Add an architecture diagram showing request flow from `index.php` to `App`, `Router`, controllers, views, and challenge classes.
- [ ] Add an API reference for `api.php` endpoints, authentication requirements, parameters, and response shapes.
- [ ] Add a lab author guide explaining `ChallengeInterface`, `BaseChallenge`, `challenge_map.php`, views, validation, and progress persistence.
- [ ] Add a “how to mark intentional vulnerabilities” convention so future audits do not accidentally sanitize teaching payloads.
- [ ] Add deployment documentation for local-only workshops, Docker, and hardened internal environments.
- [ ] Add troubleshooting docs for database connection errors, missing extensions, 404 routing issues, and writable storage.
- [ ] Add contribution guidelines with coding style, test requirements, and security review expectations.
- [ ] Add a pull request template that asks whether changes touch secure core, lab scaffolding, or intentional vulnerabilities.
- [ ] Add issue templates for bug reports, lab ideas, security concerns, and documentation improvements.
- [ ] Add changelog discipline to replace the current mixed audit history with release-oriented entries.
- [ ] Add screenshots or GIFs for the home dashboard, labs catalog, challenge page, admin dashboard, and leaderboard.
- [ ] Add accessibility statement and keyboard navigation notes.
- [ ] Add localization guide for adding new languages and maintaining translation keys.
- [ ] Add data reset/runbook documentation for classroom facilitators.
- [ ] Add “known limitations” documenting blank or partially wired labs until they are complete.
- [ ] Add security header and CSP rationale docs explaining route-level exceptions for XSS labs.
- [ ] Add docs for logging, log rotation, and sensitive-data handling.
- [ ] Add docs for backups and restoring only non-sensitive progress data.
- [ ] Add coding examples for patched implementations of each vulnerability type.
- [ ] Add glossary entries for OWASP, CWE, CSRF, XSS, SQLi, XXE, SSRF, IDOR, JWT, RCE, and deserialization.
- [ ] Add a roadmap status table that links each planned lab to implementation state.
- [ ] Add maintainers guide explaining how to review challenge maps and seed data.
- [ ] Add license information if this project is intended to be shared.

## 🎯 Quick Wins (Under 1 Hour Each)

- [ ] Remove Markdown code fences from `.gitignore`.
- [ ] Add `.env.example` and stop tracking local `.env`.
- [ ] Add `config/*.bak` to `.gitignore` and remove the tracked backup file after confirming no unique information is needed.
- [ ] Change the login credential hint to appear only when `APP_ENV=development`.
- [x] Replace every `fa-shield-haltered` class with a valid Font Awesome shield icon.
- [ ] Add `rel="noopener noreferrer"` to any external links opened in new tabs.
- [ ] Add `aria-label` to the theme toggle button.
- [ ] Add a visible `<label>` or screen-reader label for the language selector.
- [x] Add `autocomplete="username"` and `autocomplete="current-password"` to login inputs.
- [x] Add `maxlength` attributes to login and lab text inputs.
- [ ] Store a CSRF token in session before rendering labs/admin reset forms.
- [ ] Replace native `confirm()` text with clearer copy that includes the lab name and user affected.
- [ ] Whitelist lab names in reset actions.
- [ ] Update `Active Labs` count on the home page.
- [ ] Add missing quick-access links for CSRF and XXE on the home page.
- [ ] Add “coming soon” badges to partially implemented SSRF, IDOR, JWT, path traversal, and deserialization links until routing/views are complete.
- [ ] Fix leaderboard query or view so codename is not always `Unknown`.
- [ ] Add SQL `LIMIT/OFFSET` to XSS report/log admin pages instead of slicing arrays after fetching all rows.
- [ ] Cap attacker log display to the most recent 100 entries.
- [ ] Add log directory existence checks before `file_put_contents()`.
- [ ] Add `LOCK_EX` where log files are appended.
- [ ] Add `JSON_THROW_ON_ERROR` to JSON encode/decode in API rate limiting where safe.
- [ ] Add `hash_equals()` for CSRF token comparisons.
- [ ] Add `http_response_code(405)` for unsupported methods on mutation routes.
- [ ] Add `Cache-Control: no-store` to authenticated pages.
- [ ] Add `X-Content-Type-Options: nosniff` to standard pages, not only API responses.
- [ ] Add `Referrer-Policy: same-origin` globally.
- [ ] Add an internal redirect helper to avoid repeating `header('Location: ...'); exit;`.
- [ ] Add an escaping helper `e($value)` to reduce repeated `htmlspecialchars()` boilerplate.
- [ ] Add translation keys for hardcoded login, dashboard, labs, profile, and leaderboard labels.
- [ ] Remove stale commented code in `AuthController::showLabs()` once progress grouping is finalized.
- [ ] Update `CHANGES.md` or move historic audit notes into a `docs/audits/` folder.
- [ ] Add a `docs/` directory index to make existing `overview.md`, `structure.md`, and `todo.md` discoverable.
- [ ] Add a `robots.txt` discouraging indexing if deployed for a workshop.
- [ ] Add a simple `sitemap.xml` only for safe public documentation pages, not vulnerable labs.
- [ ] Add a basic `make lint` command once Composer/task tooling exists.
- [ ] Add line endings and editor settings via `.editorconfig`.
- [ ] Add an empty-state link from profile achievements to `?page=labs`.
- [ ] Add a “last solved” display to leaderboard rows.
- [ ] Add internal comments to every blank view explaining whether it is intentionally placeholder or unfinished.

## 📈 Priority Matrix

### Immediate (This Week)

- [ ] Fix `.gitignore`, remove tracked `.env`, add `.env.example`, and rotate any real credentials.
- [ ] Replace MD5 authentication and seed hashes with `password_hash()` / `password_verify()`.
- [ ] Separate API bootstrapping from `index.php` to prevent HTML/router side effects in JSON endpoints.
- [ ] Normalize challenge map shape and make `ChallengeLoader` compatible with every lab directory.
- [ ] Disable GET-based reset actions and centralize CSRF token generation/validation.
- [x] Add secure session cookie settings before `session_start()`.
- [ ] Restrict XSS admin/demo endpoints to intended roles or explicit lab-demo mode.
- [ ] Fill in `README.md` with setup steps and intentional-vulnerability warnings.
- [ ] Fix home dashboard and labs page stale counts/progress calculations.
- [ ] Add PHP lint/static sanity checks to CI or a local task runner.

### Short Term (This Month)

- [ ] Complete routing and UI integration for SSRF, IDOR, JWT, path traversal, and deserialization labs.
- [ ] Add dynamic lab catalog generation from challenge maps.
- [ ] Add Docker Compose and one-command database reset for workshops.
- [ ] Add SQL-backed pagination for logs, reports, leaderboard, and admin roster.
- [ ] Add global security headers with route-level exceptions for intentional XSS labs.
- [ ] Add audit logging for login, logout, completion, reset, and settings changes.
- [ ] Add tests for auth, router, challenge loader, API, and challenge map integrity.
- [ ] Add accessibility improvements for navigation, icon buttons, labels, focus states, and reduced motion.
- [ ] Add OpenAPI/API docs and lab authoring documentation.
- [ ] Replace CDN-only Tailwind usage with a repeatable local build for production-style deployments.

### Medium Term (This Quarter)

- [ ] Add patched/vulnerable mode toggles for every lab.
- [ ] Add instructor dashboard analytics, cohort support, and CSV exports.
- [ ] Add achievements, hints, scoring, completion timers, and certificates.
- [ ] Add full i18n coverage for English/French across all views and lab copy.
- [ ] Add end-to-end browser tests for login, lab solving, resets, settings, admin, and API flows.
- [ ] Add static analysis, formatting, coverage reporting, secret scanning, and dependency scanning.
- [ ] Add migration tooling and repeatable database lifecycle commands.
- [ ] Add CSP policy design separating secure core from lab sandboxes.
- [ ] Add structured docs for each vulnerability type with exploit and remediation walkthroughs.
- [ ] Add performance budgets and optimize JavaScript/CSS loading.

### Long Term (This Year)

- [ ] Build a complete classroom/cohort management system with assignments and due dates.
- [ ] Add plugin-style lab packs with manifests, prerequisites, tests, translations, and documentation.
- [ ] Add real-time instructor monitoring and optional live notifications.
- [ ] Add privacy-preserving public leaderboards and shareable completion cards.
- [ ] Add multi-language expansion beyond English/French with translation QA tooling.
- [ ] Add visual OWASP/CWE learning paths and adaptive next-lab recommendations.
- [ ] Add advanced deployment hardening profiles for local, internal workshop, and production-like demo modes.
- [ ] Add automated vulnerable/patched regression suites to verify both attack and defense demonstrations.
- [ ] Add richer telemetry dashboards while avoiding sensitive payload/session logging.
- [ ] Add long-term governance: release notes, security policy, maintainers guide, and lab review process.

*Total improvements identified: 250+*
*Files analyzed: 70+ files across all sections*
