<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Vulnerability Patch Report: SQL Injection</title>
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
        <div class="stat-value">SQL Injection (SQLi)</div>
      </div>
      <div class="mission-stat">
        <div class="stat-label">Severity Clearance</div>
        <div class="stat-value danger">THREAT LEVEL: CRITICAL</div>
      </div>
    </div>
  </div>

  <div class="patch-box">
    <!-- Level 1: Authentication Bypass -->
    <div class="vuln-card">
      <h2><i class="fas fa-satellite-dish text-amber-500"></i> Level 1: Authentication Bypass</h2>
      <p class="text-xs text-slate-400 leading-relaxed mb-4"><strong>Attack Vector:</strong> Insecure user credentials parsed directly into query structures without sanitization.</p>

      <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Vulnerable Input Payloads:</h3>
      <div class="payload-card">Username: admin' --</div>
      <div class="payload-card">Username: ' OR '1'='1' --</div>

      <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider mt-4 mb-2">Technical Remediation Fix:</h3>
      <div class="remediation-card"><span class="code-tag-comment">// ❌ VULNERABLE CODE:</span>
$sql = <span class="code-tag-string">"SELECT * FROM users WHERE username = '"</span> . $_POST[<span class="code-tag-string">'username'</span>] . <span class="code-tag-string">"' AND password = '"</span> . $password . <span class="code-tag-string">"'"</span>;
$result = mysqli_query($conn, $sql);

<span class="code-tag-comment">// ✅ SECURE REMEDIATION (Prepared Statements):</span>
$stmt = $pdo->prepare(<span class="code-tag-string">"SELECT * FROM users WHERE username = :username"</span>);
$stmt->execute([<span class="code-tag-string">'username'</span> => $_POST[<span class="code-tag-string">'username'</span>]]);
$user = $stmt->fetch();
if ($user && password_verify($password, $user[<span class="code-tag-string">'password'</span>])) {
    <span class="code-tag-comment">// Login authentication granted</span>
}</div>

      <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider mt-4 mb-2">Core Mitigation Directives:</h3>
      <ul class="list-disc pl-5 text-xs text-slate-400 space-y-1.5 mt-2">
        <li>Enforce strictly bound parameter mappings for all database evaluations.</li>
        <li>Never chain untrusted parameters directly inside SQL command variables.</li>
      </ul>
    </div>

    <!-- Level 2: UNION-BASED EXTRACTION -->
    <div class="vuln-card">
      <h2><i class="fas fa-key text-amber-500"></i> Level 2: UNION-Based Extraction</h2>
      <p class="text-xs text-slate-400 leading-relaxed mb-4"><strong>Attack Vector:</strong> Insecure search arguments allowing attackers to inject auxiliary SELECT statements to exfiltrate other tables.</p>

      <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Vulnerable Input Payloads:</h3>
      <div class="payload-card">' UNION SELECT NULL, secret_key, NULL, NULL FROM secrets --</div>

      <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider mt-4 mb-2">Technical Remediation Fix:</h3>
      <div class="remediation-card"><span class="code-tag-comment">// ❌ VULNERABLE CODE:</span>
$search = $_GET[<span class="code-tag-string">'q'</span>];
$sql = <span class="code-tag-string">"SELECT * FROM agents WHERE codename LIKE '%$search%'"</span>;

<span class="code-tag-comment">// ✅ SECURE REMEDIATION:</span>
$search = $_GET[<span class="code-tag-string">'q'</span>];
$stmt = $pdo->prepare(<span class="code-tag-string">"SELECT * FROM agents WHERE codename LIKE :search"</span>);
$stmt->execute([<span class="code-tag-string">'search'</span> => <span class="code-tag-string">'%'</span> . $search . <span class="code-tag-string">'%'</span>]);
$agents = $stmt->fetchAll();</div>

      <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider mt-4 mb-2">Core Mitigation Directives:</h3>
      <ul class="list-disc pl-5 text-xs text-slate-400 space-y-1.5 mt-2">
        <li>Execute database query directives only through structured PDO/MySQLi statement bindings.</li>
        <li>Use safe abstraction tools such as ORMs to build query parameters cleanly.</li>
      </ul>
    </div>

    <!-- GENERAL SUMMARY -->
    <div class="terminal-panel mt-6">
      <div class="terminal-header">
        <i class="fas fa-shield-alt text-blue-500"></i>
        <span>SQLi Defense Regulations Overview</span>
      </div>
      <div class="terminal-body font-mono text-xs text-slate-400">
        <ol class="space-y-2.5">
          <li>1. Zero Concatenations: Reject any dynamic queries constructed by chaining variables into raw commands.</li>
          <li>2. Restrict Privileges: Enforce standard least-privilege policies on data access accounts.</li>
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