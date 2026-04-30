<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Patch Report: File Upload - MIL-OPS</title>
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
      <p class="objective">File Upload Vulnerabilities - Analysis & Remediation</p>
      <div class="clearance-badge">THREAT LEVEL: CRITICAL</div>
    </div>

    <div class="patch-container">
      <!-- Level 1: Extension Bypass -->
      <div class="vuln-section">
        <h2><span class="icon">⚠️</span> LEVEL 1: EXTENSION BYPASS</h2>
        <p><strong>Vector:</strong> Only file extension is validated, content is not checked</p>

        <h3>Attack Payload:</h3>
        <div class="payload-box"># Create a PHP webshell disguised as image
          echo "&lt;?php system(\$_GET['cmd']); ?&gt;" &gt; shell.jpg</div>
        <div class="payload-box"># Upload shell.jpg - server accepts it based on extension only</div>
        <div class="payload-box"># Access: /uploads/shell.jpg?cmd=id</div>

        <h3>Vulnerability Analysis:</h3>
        <p>Server validates only the file extension (.jpg, .png) but does not verify actual file content. Attacker can
          upload PHP code with image extension.</p>

        <h3>Secure Fix:</h3>
        <div class="fix-box">
          <span class="code-comment">// ❌ VULNERABLE:</span>
          $ext = pathinfo($_FILES[<span class="code-string">'file'</span>][<span class="code-string">'name'</span>],
          PATHINFO_EXTENSION);
          <span class="code-keyword">if</span> (in_array($ext, [<span class="code-string">'jpg'</span>, <span
            class="code-string">'png'</span>, <span class="code-string">'gif'</span>])) {
          move_uploaded_file($_FILES[<span class="code-string">'file'</span>][<span
            class="code-string">'tmp_name'</span>], <span class="code-string">'uploads/'</span> . $_FILES[<span
            class="code-string">'file'</span>][<span class="code-string">'name'</span>]);
          }

          <span class="code-comment">// ✅ SECURE (Multiple Validations):</span>
          <span class="code-keyword">function</span> validateUpload($file) {
          <span class="code-comment">// 1. Check MIME type using finfo</span>
          $finfo = finfo_open(FILEINFO_MIME_TYPE);
          $mime = finfo_file($finfo, $file[<span class="code-string">'tmp_name'</span>]);
          finfo_close($finfo);

          $allowedMimes = [<span class="code-string">'image/jpeg'</span>, <span class="code-string">'image/png'</span>,
          <span class="code-string">'image/gif'</span>];
          <span class="code-keyword">if</span> (!in_array($mime, $allowedMimes)) {
          <span class="code-keyword">return</span> [<span class="code-string">'valid'</span> => false, <span
            class="code-string">'error'</span> => <span class="code-string">'Invalid file type'</span>];
          }

          <span class="code-comment">// 2. Validate extension</span>
          $ext = strtolower(pathinfo($file[<span class="code-string">'name'</span>], PATHINFO_EXTENSION));
          $allowedExts = [<span class="code-string">'jpg'</span>, <span class="code-string">'jpeg'</span>, <span
            class="code-string">'png'</span>, <span class="code-string">'gif'</span>];
          <span class="code-keyword">if</span> (!in_array($ext, $allowedExts)) {
          <span class="code-keyword">return</span> [<span class="code-string">'valid'</span> => false, <span
            class="code-string">'error'</span> => <span class="code-string">'Invalid extension'</span>];
          }

          <span class="code-comment">// 3. Generate random filename</span>
          $newName = bin2hex(random_bytes(16)) . <span class="code-string">'.'</span> . $ext;

          <span class="code-keyword">return</span> [<span class="code-string">'valid'</span> => true, <span
            class="code-string">'name'</span> => $newName];
          }
        </div>

        <h3>Key Defense:</h3>
        <ul>
          <li>Validate MIME type using <code>finfo_file()</code> (not Content-Type header)</li>
          <li>Check file extension against whitelist</li>
          <li>Verify magic bytes/file signature</li>
          <li>Rename files to random names (prevent overwrites and direct access)</li>
        </ul>
      </div>

      <!-- Level 2: MIME Type Bypass -->
      <div class="vuln-section">
        <h2><span class="icon">⚠️</span> LEVEL 2: MIME TYPE SPOOFING</h2>
        <p><strong>Vector:</strong> Server trusts client-supplied Content-Type header</p>

        <h3>Attack Payload:</h3>
        <div class="payload-box"># In Burp Suite or custom script, modify Content-Type header:
          Content-Type: image/jpeg
          # While uploading actual PHP file</div>
        <div class="payload-box">&lt;?php system($_GET['cmd']); ?&gt;</div>

        <h3>Vulnerability Analysis:</h3>
        <p>Server reads Content-Type from HTTP request headers (easily spoofed) instead of inspecting actual file
          content.</p>

        <h3>Secure Fix:</h3>
        <div class="fix-box">
          <span class="code-comment">// ❌ VULNERABLE:</span>
          $mime = $_FILES[<span class="code-string">'file'</span>][<span class="code-string">'type'</span>]; <span
            class="code-comment">// Client-controlled!</span>
          <span class="code-keyword">if</span> ($mime === <span class="code-string">'image/jpeg'</span>) {
          <span class="code-comment">// Accept file</span>
          }

          <span class="code-comment">// ✅ SECURE (Server-side MIME Detection):</span>
          <span class="code-comment">// Use finfo to detect actual MIME type from file content</span>
          $finfo = finfo_open(FILEINFO_MIME_TYPE);
          $actualMime = finfo_file($finfo, $_FILES[<span class="code-string">'file'</span>][<span
            class="code-string">'tmp_name'</span>]);
          finfo_close($finfo);

          <span class="code-comment">// Also check magic bytes manually for critical uploads</span>
          $handle = fopen($_FILES[<span class="code-string">'file'</span>][<span class="code-string">'tmp_name'</span>],
          <span class="code-string">'rb'</span>);
          $magic = fread($handle, 8);
          fclose($handle);

          <span class="code-comment">// JPEG magic bytes: FF D8 FF</span>
          <span class="code-comment">// PNG magic bytes: 89 50 4E 47 0D 0A 1A 0A</span>
          <span class="code-comment">// GIF magic bytes: 47 49 46 38</span>
        </div>

        <h3>Key Defense:</h3>
        <ul>
          <li>Never trust client-supplied Content-Type header</li>
          <li>Use <code>finfo_file()</code> to detect actual MIME type</li>
          <li>Verify magic bytes match expected file format</li>
          <li>Consider re-encoding images with GD/Imagick</li>
        </ul>
      </div>

      <div class="intel-brief">
        <h3>GENERAL FILE UPLOAD SECURITY STRATEGY</h3>
        <ol>
          <li><strong>Store Outside Webroot:</strong> Save uploads outside public directory, serve via script</li>
          <li><strong>Disable Execution:</strong> Add .htaccess with "php_flag engine off" in upload directories</li>
          <li><strong>Random Filenames:</strong> Never preserve original filenames (use hash/random strings)</li>
          <li><strong>Size Limits:</strong> Enforce strict file size limits in PHP and web server</li>
          <li><strong>Content Validation:</strong> Use finfo_file() + magic byte verification</li>
          <li><strong>Image Re-encoding:</strong> For images, load with GD/Imagick and re-save to strip
            metadata/payloads</li>
          <li><strong>Scan for Malware:</strong> Integrate antivirus scanning for sensitive applications</li>
          <li><strong>Access Control:</strong> Require authentication for uploads, log all activity</li>
        </ol>
      </div>

      <div class="vuln-section">
        <h3>SECURE UPLOAD DIRECTORY STRUCTURE</h3>
        <div class="fix-box">
          <span class="code-comment"># /storage/uploads/ (outside webroot)</span>
          <span class="code-comment"># Serve via controlled script:</span>

          <span class="code-keyword">function</span> serveFile($filename) {
          $filepath = STORAGE_PATH . <span class="code-string">'/uploads/'</span> . basename($filename);

          <span class="code-keyword">if</span> (!file_exists($filepath)) {
          http_response_code(404);
          <span class="code-keyword">return</span>;
          }

          <span class="code-comment">// Detect and send correct MIME type</span>
          $finfo = finfo_open(FILEINFO_MIME_TYPE);
          $mime = finfo_file($finfo, $filepath);
          finfo_close($finfo);

          header(<span class="code-string">'Content-Type: '</span> . $mime);
          header(<span class="code-string">'Content-Disposition: inline; filename="'</span> . basename($filename) .
          <span class="code-string">'"'</span>);
          readfile($filepath);
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