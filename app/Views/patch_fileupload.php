<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Vulnerability Patch Report: File Upload</title>
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
        <div class="stat-value">File Upload Vulnerabilities</div>
      </div>
      <div class="mission-stat">
        <div class="stat-label">Severity Clearance</div>
        <div class="stat-value danger">THREAT LEVEL: CRITICAL</div>
      </div>
    </div>
  </div>

  <div class="patch-box">
    <!-- Level 1: Extension Bypass -->
    <div class="vuln-card">
      <h2><i class="fas fa-file-excel text-amber-500"></i> Level 1: Extension Bypass</h2>
      <p class="text-xs text-slate-400 leading-relaxed mb-4"><strong>Attack Vector:</strong> Validating only the file suffix on files allows malicious code execution via nested scripts.</p>

      <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Vulnerable Input Payloads:</h3>
      <div class="payload-card"># PHP webshell disguised as dynamic image extension
echo "&lt;?php system(\$_GET['cmd']); ?&gt;" &gt; shell.jpg</div>

      <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider mt-4 mb-2">Technical Remediation Fix:</h3>
      <div class="remediation-card"><span class="code-tag-comment">// ❌ VULNERABLE CODE:</span>
$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
if (in_array($ext, ['jpg', 'png', 'gif'])) {
    move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $_FILES['file']['name']);
}

<span class="code-tag-comment">// ✅ SECURE REMEDIATION (Multiple Validation Stages):</span>
function validateUpload($file) {
    // 1. Validate MIME type dynamically
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($mime, $allowedMimes)) {
        return ['valid' => false, 'error' => 'Invalid file type'];
    }

    // 2. Validate extension safely
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($ext, $allowedExts)) {
        return ['valid' => false, 'error' => 'Invalid extension'];
    }

    // 3. Rename uploaded resource
    $newName = bin2hex(random_bytes(16)) . '.' . $ext;
    return ['valid' => true, 'name' => $newName];
}</div>

      <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider mt-4 mb-2">Core Mitigation Directives:</h3>
      <ul class="list-disc pl-5 text-xs text-slate-400 space-y-1.5 mt-2">
        <li>Always validate server-side MIME types via `finfo_file()` to reject client-controlled headers.</li>
        <li>Isolate and store uploaded assets outside the server's public document root directory.</li>
      </ul>
    </div>

    <!-- Level 2: MIME Type Spoofing -->
    <div class="vuln-card">
      <h2><i class="fas fa-file-zipper text-amber-500"></i> Level 2: MIME Type Spoofing</h2>
      <p class="text-xs text-slate-400 leading-relaxed mb-4"><strong>Attack Vector:</strong> Trusting header parameters like client Content-Type allows arbitrary file loading using request injection.</p>

      <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Vulnerable Input Payloads:</h3>
      <div class="payload-card"># Modify Content-Type in request parameters:
Content-Type: image/jpeg
# While pushing dynamic PHP execution payload</div>

      <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider mt-4 mb-2">Technical Remediation Fix:</h3>
      <div class="remediation-card"><span class="code-tag-comment">// ❌ VULNERABLE CODE:</span>
$mime = $_FILES['file']['type']; // Client-controlled!
if ($mime === 'image/jpeg') {
    // Accept uploading resource
}

<span class="code-tag-comment">// ✅ SECURE REMEDIATION:</span>
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$actualMime = finfo_file($finfo, $_FILES['file']['tmp_name']);
finfo_close($finfo);

// Check file magic bytes/signatures manually
$handle = fopen($_FILES['file']['tmp_name'], 'rb');
$magic = fread($handle, 8);
fclose($handle);

// FF D8 FF indicates JPEG image magic bytes</div>

      <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider mt-4 mb-2">Core Mitigation Directives:</h3>
      <ul class="list-disc pl-5 text-xs text-slate-400 space-y-1.5 mt-2">
        <li>Enforce strict magic byte validation rather than parsing client-provided files.</li>
        <li>Strip potential metadata payloads by re-saving dynamic uploads using GD libraries.</li>
      </ul>
    </div>

    <!-- GENERAL SUMMARY -->
    <div class="terminal-panel mt-6">
      <div class="terminal-header">
        <i class="fas fa-shield-alt text-blue-500"></i>
        <span>File Upload Defense Regulations Overview</span>
      </div>
      <div class="terminal-body font-mono text-xs text-slate-400">
        <ol class="space-y-2.5">
          <li>1. Store Remotely: Direct uploaded resources to dedicated storage volumes outside public paths.</li>
          <li>2. Disable Executions: Add configurations (like .htaccess engine blocks) to upload directories to reject runtime scripts.</li>
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