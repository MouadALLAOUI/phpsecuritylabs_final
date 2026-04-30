# Project Structure

```bash
php-security-labs/
│
├── 📄 index.php                          # Main entry point & router
├── 📄 .htaccess                          # Security rules (block direct access)
├── 📄 README.md                          # Project documentation
├── 📄 structure.md                       # Architecture overview
├── 📄 todo.md                            # Development roadmap
│
├── 📂 app/                               # Application Core
│   ├── Controllers/
│   │   ├── AuthController.php            # Login, logout, profile, labs, leaderboard, admin
│   │   └── AdminController.php           # Admin dashboard & user management
│   │
│   └── Core/
│       ├── App.php                       # Application bootstrap
│       ├── Auth.php                      # Authentication + progress tracking + admin methods
│       ├── BaseChallenge.php             # Abstract challenge base class
│       ├── ChallengeInterface.php        # Challenge interface (render, handle, validate)
│       ├── ChallengeLoader.php           # Dynamic challenge loader
│       ├── ChallengeDatabase.php         # Challenge DB helper
│       ├── Database.php                  # Dual-DB singleton (app + labs)
│       ├── LabEngine.php                 # Session-based lab state manager
│       ├── Router.php                    # URL routing logic
│       └── Session.php                   # Session wrapper
│
├── 📂 config/
│   └── config.php                        # Environment config loader
│
├── 📂 database/
│   ├── schema.sql                        # DB structure (users, agents, secrets, lab_progress)
│   └── seed.sql                          # Sample data + admin account
│
├── 📂 labs/                              # ALL VULNERABLE LABS
│   │
│   ├── xss/                              # XSS Lab (3 levels)
│   │   ├── challenge_map.php             # Maps lvl1, lvl2, lvl3
│   │   ├── challenges/
│   │   │   ├── Level1Reflected.php       # Reflected XSS (search)
│   │   │   ├── Level1ReflectedMilitary.php  # Alternative military version
│   │   │   ├── Level2Stored.php          # Stored XSS (reports)
│   │   │   ├── Level3Dom.php             # DOM XSS (hash injection)
│   │   │   └── TestChallenge.php         # Test challenge
│   │   ├── views/
│   │   └── dom_terminal.php              # Level 3 UI
│   │   ├── admin_panel.php               # Level 1 trigger page
│   │   └── admin_reports.php             # Level 2 trigger page
│   │
│   ├── sqli/                             # SQL Injection Lab (2 levels)
│   │   ├── challenge.php                 # Lab router
│   │   ├── challenge_map.php             # Maps lvl1, lvl2
│   │   ├── challenges/
│   │   │   ├── Level1AuthBypass.php      # Auth bypass injection
│   │   │   └── Level2UnionExtraction.php # UNION-based data extraction
│   │   └── views/
│   │       ├── auth_terminal.php         # Level 1 login UI
│   │       └── search_terminal.php       # Level 2 search UI
│   │
│   └── file_upload/                      # File Upload Lab (2 levels)
│       ├── challenge.php                 # Lab router
│       ├── challenge_map.php             # Maps lvl1, lvl2
│       ├── challenges/
│       │   ├── Level1ExtensionBypass.php # Extension validation bypass
│       │   └── Level2MimeBypass.php      # MIME type spoofing
│       ├── uploads/                      # Uploaded files storage
│       │   └── .htaccess                 # ⚠️ DENY ALL + block PHP
│       └── views/
│           ├── upload_terminal.php       # Level 1 UI
│           └── mime_upload_terminal.php  # Level 2 UI
│
├── 📂 public/                            # Public assets
│   └── attacker.php                      # Cookie collector for XSS labs
│
├── 📂 shared/                            # Reusable UI components
│   ├── header.php                        # Core light-mode header
│   ├── footer.php                        # Core light-mode footer
│   ├── sidebar.php                       # Navigation sidebar (with admin link)
│   │
│   └── military-ui/                      # 🎖️ DARK MILITARY THEME
│       ├── header.php                    # Tactical top bar + sidebar
│       ├── footer.php                    # Alert modals + toast notifications
│       ├── mil-ops.css                   # Dark theme styles (CRT, neon, glow)
│       ├── mil-ops.js                    # Terminal effects + toast system
│       └── README.md                     # Military UI documentation
│
├── 📂 storage/                           # Protected storage
│   └── .htaccess                         # ⚠️ DENY FROM ALL
│
└── 📂 app/Views/                         # CORE UI PAGES (light mode)
    ├── home.php                          # Dashboard with progress
    ├── login.php                         # Login form
    ├── profile.php                       # User profile
    ├── labs.php                          # Labs overview with reset buttons
    ├── leaderboard.php                   # Top users ranking
    ├── patch_xss.php                     # XSS security report
    ├── patch_sqli.php                    # SQLi security report
    ├── patch_fileupload.php              # File upload security report
    └── admin/
        └── dashboard.php                 # Admin panel (user management)
```
