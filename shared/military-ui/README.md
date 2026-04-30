# MIL-OPS CONTROL SYSTEM
## Military Theme UI Layer for Cybersecurity Labs

---

## 📁 FILE STRUCTURE

```
/shared/military-ui/
├── header.php          # Military theme header with top bar & sidebar
├── footer.php          # Military theme footer with alerts & loading overlay
├── mil-ops.css         # Complete CSS styling (dark tactical theme)
└── mil-ops.js          # JavaScript for UI interactions, logs, effects

/labs/sqli/
└── challenge.php       # SQL Injection Lab - Database Breach Mission

/labs/xss/challenges/
└── Level1ReflectedMilitary.php  # XSS Lab - Signal Intercept Mission

/labs/file_upload/
└── challenge.php       # File Upload Lab - Intel Upload Mission
```

---

## 🎯 CORE COMPONENTS

### 1. HEADER (`header.php`)
Reusable military-themed header that includes:
- **TOP BAR**: System title, secure link status, agent codename, clearance level, live clock
- **SIDEBAR**: Mission selector with active/locked operations, system log feed

**Usage:**
```php
include_once ROOT . '/shared/military-ui/header.php';
```

### 2. FOOTER (`footer.php`)
Reusable footer with:
- System alert modal
- Loading overlay with progress animation
- Classification markings

**Usage:**
```php
include_once ROOT . '/shared/military-ui/footer.php';
```

### 3. CSS (`mil-ops.css`)
Complete styling system featuring:
- Dark military color palette (#0b0f14 background)
- Neon green/amber highlights
- CRT scanline overlay effect
- Terminal-style panels and forms
- Animated elements (pulse, flicker, glow)
- Responsive design

### 4. JavaScript (`mil-ops.js`)
Interactive features including:
- Live system time updates
- Fake security log generation
- Loading overlay with custom messages
- System alert modals
- Terminal output helpers
- Mission state management
- Typewriter effects

---

## 🧩 LAB TEMPLATES

### SQL INJECTION - "Database Breach"
**File:** `/labs/sqli/challenge.php`

**Mission Elements:**
- Authentication terminal (login form)
- Query execution log display
- Result output terminal
- Raw SQL query visualization

**Vulnerability Preserved:** ✅ Direct string interpolation in queries

---

### XSS - "Signal Intercept"  
**File:** `/labs/xss/challenges/Level1ReflectedMilitary.php`

**Mission Elements:**
- Agent search terminal
- Intercepted communications panel
- Dynamic result rendering (vulnerable)
- Admin chat simulation

**Vulnerability Preserved:** ✅ Direct innerHTML rendering without escaping

---

### FILE UPLOAD - "Intel Upload"
**File:** `/labs/file_upload/challenge.php`

**Mission Elements:**
- Secure file transfer form
- File scanning simulation
- Transfer log display
- Security scanner configuration panel
- Uploaded files list

**Vulnerability Preserved:** ✅ Extension-only validation, no content checking

---

## 🎨 UI COMPONENTS REFERENCE

### Mission Header Panel
```html
<div class="mission-header">
  <div class="mission-title">
    <i class="fas fa-database"></i>
    <span>MISSION: DATABASE BREACH</span>
  </div>
  <div class="mission-grid">
    <div class="mission-stat">
      <div class="stat-label">OBJECTIVE</div>
      <div class="stat-value">EXTRACT AGENT RECORDS</div>
    </div>
    <!-- More stats... -->
  </div>
</div>
```

### Terminal Panel
```html
<div class="terminal-panel">
  <div class="terminal-header">
    <i class="fas fa-terminal"></i>
    <span>AUTHENTICATION TERMINAL</span>
  </div>
  <div class="terminal-body">
    <!-- Content here -->
  </div>
</div>
```

### Military Input
```html
<input type="text" name="username" class="mil-input" placeholder="Enter operator ID...">
```

### Military Button
```html
<button type="submit" class="mil-button">AUTHENTICATE</button>
<button type="submit" class="mil-button danger">DELETE</button>
<button type="submit" class="mil-button amber">UPLOAD</button>
```

### Output Terminal
```html
<div class="output-terminal">
  System output text...
</div>

<div class="output-terminal error">
  Error messages (red)...
</div>

<div class="output-terminal warning">
  Warning messages (amber)...
</div>
```

---

## ⚙️ JAVASCRIPT UTILITIES

### Show Loading Overlay
```javascript
showLoading('ESTABLISHING SECURE CHANNEL...');
hideLoading();

// With multiple messages
simulateLoading([
  'INITIATING...',
  'SCANNING...',
  'PROCESSING...'
], 3000);
```

### Show System Alert
```javascript
showSystemAlert('Unauthorized access detected', 'warning');
closeSystemAlert();
```

### Append to Terminal
```javascript
appendToTerminal('terminalId', 'Message text', 'error');
// Types: normal, error, warning, success, info

clearTerminal('terminalId');
```

### Add System Log Entry
```javascript
const logContainer = document.getElementById('systemLogMini');
addLogEntry(logContainer, 'Custom message', 'text-green');
```

---

## 🎯 IMMERSION FEATURES

All labs include:
- ✅ CRT scanline overlay effect
- ✅ Live system clock
- ✅ Dynamic agent codenames
- ✅ Clearance level display
- ✅ Fake security logs
- ✅ Loading animations
- ✅ System alerts
- ✅ Mission briefing panels
- ✅ Success/completion states
- ✅ Classified styling throughout

---

## 🔧 INTEGRATION WITH EXISTING LABS

### Option 1: Replace Existing Header/Footer
In your existing lab PHP files:
```php
// Replace this:
include_once ROOT . '/shared/header.php';
include_once ROOT . '/shared/sidebar.php';

// With this:
include_once ROOT . '/shared/military-ui/header.php';

// ... your lab content ...

include_once ROOT . '/shared/military-ui/footer.php';
```

### Option 2: Create Parallel Military Versions
Keep original labs intact and create new military-themed versions:
- `Level1Reflected.php` → `Level1ReflectedMilitary.php`
- Users can choose their preferred interface

---

## 🎨 COLOR PALETTE

| Color | Variable | Hex | Usage |
|-------|----------|-----|-------|
| Dark BG | `--mil-dark` | #0b0f14 | Main background |
| Panel | `--mil-panel` | #111820 | Card backgrounds |
| Green | `--mil-green` | #00ff41 | Success, primary |
| Amber | `--mil-amber` | #ffb000 | Warnings, highlights |
| Red | `--mil-red` | #ff3333 | Errors, danger |
| Blue | `--mil-blue` | #00d9ff | Info, links |

---

## 🚀 QUICK START

1. **Add military UI to a new lab:**
```php
<?php
namespace Labs\YourLab;
use App\Core\BaseChallenge;

class YourChallenge extends BaseChallenge
{
    public function render(): void
    {
        include_once ROOT . '/shared/military-ui/header.php';
?>

<div class="mission-header">
  <!-- Your mission info -->
</div>

<div class="terminal-panel">
  <!-- Your lab content -->
</div>

<?php
        include_once ROOT . '/shared/military-ui/footer.php';
    }
}
```

2. **Style your forms:**
```html
<input type="text" class="mil-input" placeholder="...">
<button class="mil-button">ACTION</button>
```

3. **Add interactivity:**
```html
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Your lab-specific JS
});
</script>
```

---

## ⚠️ IMPORTANT NOTES

- **DO NOT** sanitize user inputs in vulnerable labs
- **DO NOT** remove or fix vulnerabilities
- Backend logic remains unchanged
- This is purely a UI transformation layer
- Each lab must feel like a real classified system

---

## 📦 DELIVERABLES SUMMARY

✅ Reusable layout (header + sidebar + footer)
✅ Complete CSS styling system
✅ JavaScript utilities library
✅ SQL Injection Lab UI template
✅ XSS Lab UI template  
✅ File Upload Lab UI template
✅ Documentation (this file)

---

**CLASSIFICATION:** UNCLASSIFIED//FOUO
**SYSTEM:** MIL-OPS TERMINAL v3.2.1
