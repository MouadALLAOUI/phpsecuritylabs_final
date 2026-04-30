/**
 * MIL-OPS CONTROL SYSTEM - Military Theme JavaScript
 * Handles UI interactions, fake logs, and mission states
 */

// ============================================
// SYSTEM INITIALIZATION
// ============================================
document.addEventListener('DOMContentLoaded', function () {
  initializeSystem();
  startSystemLogs();
  updateSystemTime();
});

// ============================================
// SYSTEM TIME UPDATE
// ============================================
function updateSystemTime() {
  const timeElement = document.getElementById('systemTime');
  if (timeElement) {
    const now = new Date();
    const timeString = now.toLocaleTimeString('en-US', {
      hour12: false,
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit'
    });
    timeElement.textContent = timeString;

    // Update footer timestamp
    const footerTimestamp = document.getElementById('footerTimestamp');
    if (footerTimestamp) {
      footerTimestamp.textContent = now.toISOString().replace('T', ' ').substr(0, 19);
    }
  }
  setTimeout(updateSystemTime, 1000);
}

// ============================================
// SYSTEM LOGS (FAKE)
// ============================================
const systemLogMessages = [
  'Packet trace initialized',
  'Monitoring network traffic...',
  'Firewall rules loaded',
  'Encryption keys verified',
  'Secure channel established',
  'Target system scanned',
  'Vulnerability assessment complete',
  'Agent status: ACTIVE',
  'Database connection pooled',
  'Session tokens refreshed',
  'Intrusion detection active',
  'Proxy chain verified',
  'Payload decrypted',
  'Memory dump initiated',
  'Port scan complete: 3 open',
  'SSL certificate validated',
  'DNS resolution cached',
  'Authentication bypass detected',
  'SQL query logged',
  'XSS payload flagged'
];

const logColors = ['text-green', 'text-blue', 'text-amber'];

function startSystemLogs() {
  const logContainer = document.getElementById('systemLogMini');
  if (!logContainer) return;

  // Add initial log entry
  addLogEntry(logContainer, 'SYSTEM INITIALIZED', 'text-green');

  // Add random logs periodically
  setInterval(() => {
    const randomMessage = systemLogMessages[Math.floor(Math.random() * systemLogMessages.length)];
    const randomColor = logColors[Math.floor(Math.random() * logColors.length)];
    addLogEntry(logContainer, randomMessage, randomColor);
  }, 3000 + Math.random() * 5000);
}

function addLogEntry(container, message, colorClass = 'text-dim') {
  const now = new Date();
  const timeString = now.toLocaleTimeString('en-US', {
    hour12: false,
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  });

  const logEntry = document.createElement('div');
  logEntry.className = 'log-entry';
  logEntry.innerHTML = `<span class="log-time ${colorClass}">${timeString}</span> ${message}`;

  container.appendChild(logEntry);

  // Keep only last 10 entries
  while (container.children.length > 10) {
    container.removeChild(container.firstChild);
  }

  // Auto-scroll to bottom
  container.scrollTop = container.scrollHeight;
}

// ============================================
// LOADING OVERLAY
// ============================================
function showLoading(text = 'ESTABLISHING SECURE CHANNEL...') {
  const overlay = document.getElementById('loadingOverlay');
  const loadingText = document.getElementById('loadingText');

  if (overlay) {
    if (loadingText) loadingText.textContent = text;
    overlay.classList.remove('hidden');
  }
}

function hideLoading() {
  const overlay = document.getElementById('loadingOverlay');
  if (overlay) {
    overlay.classList.add('hidden');
  }
}

// Simulate loading with custom messages
function simulateLoading(messages, duration = 2000, callback = null) {
  let index = 0;

  showLoading(messages[0]);

  const interval = setInterval(() => {
    index++;
    if (index < messages.length) {
      showLoading(messages[index]);
    } else {
      clearInterval(interval);
      setTimeout(() => {
        hideLoading();
        if (callback) callback();
      }, 500);
    }
  }, duration / messages.length);
}

// ============================================
// SYSTEM ALERTS
// ============================================
function showSystemAlert(message, type = 'warning') {
  const modal = document.getElementById('systemAlert');
  const alertBody = document.getElementById('alertBody');

  if (modal && alertBody) {
    alertBody.textContent = message;
    modal.classList.remove('hidden');

    // Play alert sound (optional, commented out by default)
    // playAlertSound();
  }
}

function closeSystemAlert() {
  const modal = document.getElementById('systemAlert');
  if (modal) {
    modal.classList.add('hidden');
  }
}

// ============================================
// TERMINAL OUTPUT
// ============================================
function appendToTerminal(terminalId, text, type = 'normal') {
  const terminal = document.getElementById(terminalId);
  if (!terminal) return;

  const timestamp = new Date().toLocaleTimeString('en-US', {
    hour12: false,
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  });

  const line = document.createElement('div');
  line.className = 'terminal-line';

  let prefix = '';
  let colorClass = '';

  switch (type) {
    case 'error':
      prefix = '[ERROR]';
      colorClass = 'text-red';
      terminal.classList.add('error');
      break;
    case 'warning':
      prefix = '[WARN]';
      colorClass = 'text-amber';
      terminal.classList.add('warning');
      break;
    case 'success':
      prefix = '[OK]';
      colorClass = 'text-green';
      break;
    case 'info':
      prefix = '[INFO]';
      colorClass = 'text-blue';
      break;
    default:
      prefix = '[LOG]';
      colorClass = 'text-green';
  }

  line.innerHTML = `<span class="${colorClass}">${timestamp} ${prefix}</span> ${escapeHtml(text)}`;
  terminal.appendChild(line);
  terminal.scrollTop = terminal.scrollHeight;
}

function clearTerminal(terminalId) {
  const terminal = document.getElementById(terminalId);
  if (terminal) {
    terminal.innerHTML = '';
    terminal.classList.remove('error', 'warning');
  }
}

// ============================================
// UTILITY FUNCTIONS
// ============================================
function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

function generateRandomHex(length = 16) {
  let result = '';
  const characters = '0123456789ABCDEF';
  for (let i = 0; i < length; i++) {
    result += characters.charAt(Math.floor(Math.random() * characters.length));
  }
  return result;
}

function generateTimestamp() {
  const now = new Date();
  return now.toISOString().replace(/[-:]/g, '').substr(0, 14);
}

// ============================================
// MISSION STATE MANAGEMENT
// ============================================
function setMissionStatus(missionId, status) {
  const statusElement = document.querySelector(`[data-mission="${missionId}"] .mission-status`);
  if (statusElement) {
    statusElement.className = `mission-status status-${status}`;
  }
}

function lockMission(missionId) {
  const missionLink = document.querySelector(`[data-mission="${missionId}"]`);
  if (missionLink) {
    missionLink.classList.add('locked');
    setMissionStatus(missionId, 'locked');
  }
}

function unlockMission(missionId) {
  const missionLink = document.querySelector(`[data-mission="${missionId}"]`);
  if (missionLink) {
    missionLink.classList.remove('locked');
    setMissionStatus(missionId, 'active');
  }
}

// ============================================
// FORM INTERCEPTION (FOR DEMO PURPOSES)
// ============================================
function interceptFormSubmit(formSelector, callback) {
  const form = document.querySelector(formSelector);
  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();

      // Show loading state
      showLoading('PROCESSING REQUEST...');

      // Simulate network delay
      setTimeout(() => {
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        hideLoading();

        if (callback) {
          callback(data);
        }
      }, 1000 + Math.random() * 1000);
    });
  }
}

// ============================================
// RANDOM SECURITY ALERTS
// ============================================
const securityAlerts = [
  'Unauthorized access attempt detected',
  'Firewall anomaly in sector 7G',
  'Suspicious packet pattern identified',
  'Brute force attempt blocked',
  'SQL injection signature detected',
  'XSS payload intercepted',
  'Session hijacking attempt failed',
  'CSRF token mismatch detected'
];

function triggerRandomSecurityAlert(delay = 30000) {
  setTimeout(() => {
    const alert = securityAlerts[Math.floor(Math.random() * securityAlerts.length)];
    showSystemAlert(alert, 'danger');

    // Add to system log
    const logContainer = document.getElementById('systemLogMini');
    if (logContainer) {
      addLogEntry(logContainer, `SECURITY: ${alert}`, 'text-red');
    }

    // Recursively schedule next alert
    triggerRandomSecurityAlert(delay + Math.random() * 30000);
  }, delay);
}

// Start random security alerts (optional)
// triggerRandomSecurityAlert();

// ============================================
// TYPEWRITER EFFECT FOR TERMINALS
// ============================================
function typeWriter(elementId, text, speed = 30, callback = null) {
  const element = document.getElementById(elementId);
  if (!element) return;

  let i = 0;
  element.textContent = '';

  function type() {
    if (i < text.length) {
      element.textContent += text.charAt(i);
      i++;
      setTimeout(type, speed);
    } else if (callback) {
      callback();
    }
  }

  type();
}

// ============================================
// GLITCH EFFECT (OPTIONAL VISUAL)
// ============================================
function triggerGlitch(elementSelector) {
  const element = document.querySelector(elementSelector);
  if (element) {
    element.style.animation = 'none';
    element.offsetHeight; /* trigger reflow */
    element.style.animation = 'flicker 0.1s linear 3';

    setTimeout(() => {
      element.style.animation = '';
    }, 300);
  }
}

// Toast Notification System
function showToast(message) {
  const toast = document.getElementById('toastNotification');
  const toastMessage = document.getElementById('toastMessage');

  if (toast && toastMessage) {
    toastMessage.textContent = message;
    toast.classList.remove('hidden');

    // Auto-hide after 3 seconds
    setTimeout(() => {
      closeToast();
    }, 3000);
  }
}

function closeToast() {
  const toast = document.getElementById('toastNotification');
  if (toast) {
    toast.classList.add('hidden');
  }
}
// ============================================
// INITIALIZE SYSTEM
// ============================================
function initializeSystem() {
  console.log('%c MIL-OPS CONTROL SYSTEM INITIALIZED ',
    'background: #00ff41; color: #0b0f14; font-weight: bold; padding: 5px;');

  // Add startup log entry
  const logContainer = document.getElementById('systemLogMini');
  if (logContainer) {
    addLogEntry(logContainer, 'TERMINAL READY', 'text-green');
  }
}

// ============================================
// LANGUAGE SWITCHER
// ============================================
function changeLanguage(lang) {
  // Save to session via AJAX
  fetch('?page=settings&action=set_language', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'lang=' + encodeURIComponent(lang)
  })
  .then(response => response.text())
  .then(data => {
    console.log('Language changed to:', lang);
    // Reload page to apply new language
    window.location.reload();
  })
  .catch(error => {
    console.error('Error changing language:', error);
  });
}

// ============================================
// THEME TOGGLE
// ============================================
function toggleTheme() {
  const body = document.body;
  const currentTheme = localStorage.getItem('theme') || 'military';
  
  let newTheme;
  if (currentTheme === 'military') {
    newTheme = 'light';
    body.classList.remove('mil-body');
    body.classList.add('light-theme');
  } else {
    newTheme = 'military';
    body.classList.remove('light-theme');
    body.classList.add('mil-body');
  }
  
  localStorage.setItem('theme', newTheme);
  
  // Save to session via AJAX
  fetch('?page=settings&action=set_theme', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'theme=' + encodeURIComponent(newTheme)
  })
  .then(response => response.text())
  .then(data => {
    console.log('Theme changed to:', newTheme);
  })
  .catch(error => {
    console.error('Error changing theme:', error);
  });
}

// Initialize theme on page load
document.addEventListener('DOMContentLoaded', function() {
  const savedTheme = localStorage.getItem('theme') || 'military';
  if (savedTheme === 'light') {
    document.body.classList.add('light-theme');
    document.body.classList.remove('mil-body');
  }
});
