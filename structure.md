# Project Structure

```bash
📦 phpsecuritylabs
 ┣ 📂 app                    # Core application (ALWAYS secure)
 ┃ ┣ 📂 Core
 ┃ ┃ ┣ 📜 App.php
 ┃ ┃ ┣ 📜 Router.php
 ┃ ┃ ┣ 📜 Controller.php
 ┃ ┃ ┣ 📜 Database.php
 ┃ ┃ ┣ 📜 LabEngine.php
 ┃ ┃ ┣ 📜 Input.php
 ┃ ┃ ┣ 📜 Session.php
 ┃ ┃ ┗ 📜 Security.php
 ┃ ┣ 📂 Helpers
 ┃ ┃ ┣ 📜 UI.php
 ┃ ┃ ┣ 📜 Auth.php
 ┃ ┃ ┗ 📜 Logger.php
 ┃ ┣ 📂 Middleware
 ┃ ┃ ┣ 📜 AuthMiddleware.php
 ┃ ┃ ┗ 📜 LabMiddleware.php
 ┃ ┗ 📂 Config
 ┃ ┃ ┣ 📜 app.php
 ┃ ┃ ┗ 📜 database.php
 ┃
 ┣ 📂 labs                   # ALL vulnerabilities live here
 ┃ ┣ 📂 xss
 ┃ ┃ ┣ 📂 challenges
 ┃ ┃ ┃ ┣ 📜 lvl1_reflected.php
 ┃ ┃ ┃ ┣ 📜 lvl2_filter_bypass.php
 ┃ ┃ ┃ ┣ 📜 lvl3_attribute_injection.php
 ┃ ┃ ┃ ┣ 📜 lvl4_stored.php
 ┃ ┃ ┃ ┣ 📜 lvl5_cookie_steal.php
 ┃ ┃ ┃ ┣ 📜 lvl6_filter_evasion.php
 ┃ ┃ ┃ ┣ 📜 lvl7_dom_xss.php
 ┃ ┃ ┃ ┣ 📜 lvl8_js_context.php
 ┃ ┃ ┃ ┣ 📜 lvl9_waf_bypass.php
 ┃ ┃ ┃ ┗ 📜 final_chain.php
 ┃ ┃ ┣ 📜 dashboard.php
 ┃ ┃ ┣ 📜 manual.php
 ┃ ┃ ┣ 📜 validator.php      # checks if user solved level
 ┃ ┃ ┣ 📜 config.php         # lab-specific toggles
 ┃ ┃ ┗ 📜 admin_panel.php    # for stored/blind XSS
 ┃ ┃
 ┃ ┣ 📂 sql_injection
 ┃ ┃ ┣ 📂 challenges
 ┃ ┃ ┣ 📜 dashboard.php
 ┃ ┃ ┣ 📜 manual.php
 ┃ ┃ ┗ 📜 patch_report.php
 ┃ ┃
 ┃ ┣ 📂 file_upload
 ┃ ┃ ┣ 📂 challenges
 ┃ ┃ ┣ 📜 dashboard.php
 ┃ ┃ ┗ 📜 manual.php
 ┃ ┃
 ┃ ┗ 📂 shared              # reusable lab components
 ┃ ┃ ┣ 📜 lab_header.php
 ┃ ┃ ┣ 📜 lab_footer.php
 ┃ ┃ ┗ 📜 hints.php
 ┃
 ┣ 📂 storage               # runtime data (NOT public)
 ┃ ┣ 📂 logs
 ┃ ┃ ┗ 📜 xss_hits.log
 ┃ ┣ 📂 sessions
 ┃ ┗ 📂 uploads
 ┃
 ┣ 📂 public                # ONLY public entry point
 ┃ ┣ 📂 assets
 ┃ ┃ ┣ 📂 css
 ┃ ┃ ┣ 📂 js
 ┃ ┃ ┗ 📂 images
 ┃ ┣ 📜 index.php          # main router entry
 ┃ ┣ 📜 attacker.php       # exfiltration endpoint
 ┃ ┗ 📜 .htaccess
 ┃
 ┣ 📂 database
 ┃ ┣ 📜 schema.sql
 ┃ ┗ 📜 seed.sql
 ┃
 ┣ 📂 lang
 ┃ ┣ 📜 en.php
 ┃ ┗ 📜 fr.php
 ┃
 ┣ 📂 shared               # global UI (outside labs)
 ┃ ┣ 📜 header.php
 ┃ ┣ 📜 footer.php
 ┃ ┗ 📜 sidebar.php
 ┃
 ┣ 📜 .env
 ┣ 📜 .htaccess
 ┣ 📜 composer.json (optional)
 ┗ 📜 README.md
```
