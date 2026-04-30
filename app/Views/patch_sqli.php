<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Patch Report: SQL Injection - MIL-OPS</title>
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

  .code-var {
    color: #60a5fa;
  }
  </style>
</head>

<body class="mil-ops-bg">
  <?php include_once ROOT . '/shared/sidebar.php'; ?>

  <main class="mil-ops-main">
    <div class="mission-header">
      <h1><span class="icon">🛡️</span> VULNERABILITY PATCH REPORT</h1>
      <p class="objective">SQL Injection (SQLi) - Analysis & Remediation</p>
      <div class="clearance-badge">THREAT LEVEL: CRITICAL</div>
    </div>

    <div class="patch-container">
      <!-- Level 1: Authentication Bypass -->
      <div class="vuln-section">
        <h2><span class="icon">⚠️</span> LEVEL 1: AUTHENTICATION BYPASS</h2>
        <p><strong>Vector:</strong> User input concatenated directly into SQL query string</p>

        <h3>Attack Payloads:</h3>
        <div class="payload-box">Username: admin' --</div>
        <div class="payload-box">Username: ' OR '1'='1' --</div>
        <div class="payload-box">Password: ' OR 1=1 --</div>

        <h3>Vulnerability Analysis:</h3>
        <p>Login form accepts username/password and concatenates them directly into SQL query without parameterization.
        </p>

        <h3>Secure Fix:</h3>
        <div class="fix-box">
          <span class="code-comment">// ❌ VULNERABLE:</span>
          $sql = <span class="code-string">"SELECT * FROM users WHERE username = '"</span> . $_POST[<span
            class="code-string">'username'</span>] . <span class="code-string">"' AND password = '"</span> . $password .
          <span class="code-string">"'"</span>;
          $result = mysqli_query($conn, $sql);

          <span class="code-comment">// ✅ SECURE (Prepared Statements):</span>
          $stmt = $pdo->prepare(<span class="code-string">"SELECT * FROM users WHERE username = :username"</span>);
          $stmt->execute([<span class="code-string">'username'</span> => $_POST[<span
            class="code-string">'username'</span>]]);
          $user = $stmt->fetch();
          <span class="code-keyword">if</span> ($user && password_verify($password, $user[<span
            class="code-string">'password'</span>])) {
          <span class="code-comment">// Login successful</span>
          }
        </div>

        <h3>Key Defense:</h3>
        <ul>
          <li>Use prepared statements with parameterized queries</li>
          <li>Never concatenate user input into SQL strings</li>
          <li>Use password_hash() / password_verify() for credentials</li>
          <li>Implement rate limiting on login endpoints</li>
        </ul>
      </div>

      <!-- Level 2: UNION Extraction -->
      <div class="vuln-section">
        <h2><span class="icon">⚠️</span> LEVEL 2: UNION-BASED EXTRACTION</h2>
        <p><strong>Vector:</strong> Search parameter allows UNION SELECT to extract data from other tables</p>

        <h3>Attack Payloads:</h3>
        <div class="payload-box">' UNION SELECT NULL, secret_key, NULL, NULL FROM secrets --</div>
        <div class="payload-box">' UNION SELECT 1, GROUP_CONCAT(secret_key), 3, 4 FROM secrets --</div>

        <h3>Vulnerability Analysis:</h3>
        <p>Search functionality uses unsanitized input in WHERE clause, allowing attacker to append UNION SELECT
          statements to extract data from arbitrary tables.</p>

        <h3>Secure Fix:</h3>
        <div class="fix-box">
          <span class="code-comment">// ❌ VULNERABLE:</span>
          $search = $_GET[<span class="code-string">'q'</span>];
          $sql = <span class="code-string">"SELECT * FROM agents WHERE codename LIKE '%$search%'"</span>;

          <span class="code-comment">// ✅ SECURE (Prepared Statements with LIKE):</span>
          $search = $_GET[<span class="code-string">'q'</span>];
          $stmt = $pdo->prepare(<span class="code-string">"SELECT * FROM agents WHERE codename LIKE :search"</span>);
          $stmt->execute([<span class="code-string">'search'</span> => <span class="code-string">'%'</span> . $search .
          <span class="code-string">'%'</span>]);
          $agents = $stmt->fetchAll();
        </div>

        <h3>Key Defense:</h3>
        <ul>
          <li>Always use prepared statements, even for SELECT queries</li>
          <li>Bind parameters for LIKE clauses (include % wildcards in value)</li>
          <li>Apply principle of least privilege to database accounts</li>
          <li>Consider using stored procedures for complex queries</li>
        </ul>
      </div>

      <div class="intel-brief">
        <h3>GENERAL SQL INJECTION PREVENTION STRATEGY</h3>
        <ol>
          <li><strong>Prepared Statements:</strong> Use PDO or MySQLi with bound parameters for ALL queries</li>
          <li><strong>ORM/Query Builders:</strong> Use frameworks that abstract raw SQL (Eloquent, Doctrine)</li>
          <li><strong>Input Validation:</strong> Whitelist expected data types and formats</li>
          <li><strong>Error Handling:</strong> Never expose SQL errors to users (use custom error pages)</li>
          <li><strong>Database Permissions:</strong> Limit database user privileges (no DROP, ALTER)</li>
          <li><strong>Web Application Firewall:</strong> Deploy WAF rules to detect SQLi patterns</li>
          <li><strong>Regular Audits:</strong> Perform code reviews and penetration testing</li>
        </ol>
      </div>

      <div class="vuln-section">
        <h3>ADDITIONAL SECURITY MEASURES</h3>
        <div class="fix-box">
          <span class="code-comment">// Example: Secure database helper function</span>
          <span class="code-keyword">function</span> getUserByUsername($username) {
          $db = Database::getInstance(<span class="code-string">'app'</span>);
          $pdo = $db->getConnection();

          $stmt = $pdo->prepare(<span class="code-string">"SELECT id, username, email, role FROM users WHERE username =
            :username"</span>);
          $stmt->execute([<span class="code-string">'username'</span> => $username]);

          <span class="code-keyword">return</span> $stmt->fetch(PDO::FETCH_ASSOC);
          }
        </div>
      </div>

      <div style="text-align: center; margin-top: 2rem;">
        <a href="?page=labs" class="btn-primary">← RETURN TO LABS</a>
      </div>
    </div>
  </main>

  <?php include_once ROOT . '/shared/military-ui/footer.php'; ?>
</body>

</html>