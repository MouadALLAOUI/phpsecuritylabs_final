<?php

namespace Labs\XXE\Challenges;

use App\Core\BaseChallenge;
use App\Core\Session;

class Level1XXE extends BaseChallenge
{
  private string $message = '';
  private bool $solved = false;
  private array $parsedData = null;
  private string $xmlContent = '';

  public function handle(): void
  {
    // Check if already solved
    if (Session::get('xxe_lvl1_solved') === true) {
      $this->solved = true;
    }

    // Handle XML upload
    if ($this->isPost() && isset($_POST['xml_data'])) {
      $this->xmlContent = $_POST['xml_data'];
      
      try {
        // VULNERABLE: libxml_disable_entity_loader is deprecated in PHP 8+
        // This code is intentionally vulnerable for educational purposes
        // PHP 8+ compatibility: use alternative approach
        $oldEntityLoader = null;
        if (version_compare(PHP_VERSION, '8.0.0', '<')) {
          $oldEntityLoader = libxml_disable_entity_loader(false);
        } else {
          // PHP 8+: libxml_disable_entity_loader is deprecated, use external entity loader callback
          libxml_set_external_entity_loader(null); // Allow external entities (vulnerable)
        }
        $internalErrors = libxml_use_internal_errors(true);
        
        $dom = new \DOMDocument();
        $dom->loadXML($this->xmlContent);
        
        // Extract data from XML
        $this->parsedData = $this->extractXmlData($dom);
        
        // Check for XXE success indicators
        if ($this->checkForXXESuccess($this->parsedData)) {
          Session::set('xxe_lvl1_solved', true);
          $this->solved = true;
          // markCompleted() will be called in validate() instead
          $this->message = "XXE Attack Successful! You extracted sensitive system information.";
        } else {
          $this->message = "XML processed successfully. Can you extract sensitive data?";
        }
        
        // Restore libxml settings
        if (version_compare(PHP_VERSION, '8.0.0', '<') && $oldEntityLoader !== null) {
          libxml_disable_entity_loader($oldEntityLoader);
        }
        libxml_use_internal_errors($internalErrors);
        
      } catch (\Exception $e) {
        $this->message = "XML Parse Error: " . htmlspecialchars($e->getMessage());
        $this->parsedData = null;
      }
    }

    // Reset action
    if ($this->isPost() && isset($_POST['reset_lab'])) {
      Session::delete('xxe_lvl1_solved');
      $this->solved = false;
      $this->message = "Lab reset. Try again!";
    }
  }

  private function extractXmlData(\DOMDocument $dom): array
  {
    $data = [];
    $root = $dom->documentElement;
    
    if ($root) {
      foreach ($root->childNodes as $child) {
        if ($child->nodeType === XML_ELEMENT_NODE) {
          $data[$child->nodeName] = $child->textContent;
        }
      }
    }
    
    return $data;
  }

  private function checkForXXESuccess(array $data): bool
  {
    // Check if attacker successfully extracted sensitive info
    $sensitivePatterns = [
      '/root:/i',           // /etc/passwd content
      '/[a-z]:\\/windows/i', // Windows paths
      '/flag\\{/i',         // CTF flag format
      '/secret/i',          // Secret keywords
      '/password/i',        // Password fields
      '/admin/i'            // Admin info
    ];
    
    foreach ($data as $key => $value) {
      foreach ($sensitivePatterns as $pattern) {
        if (preg_match($pattern, $value) || preg_match($pattern, $key)) {
          return true;
        }
      }
    }
    
    return false;
  }

  public function render(): void
  {
    include_once ROOT . '/shared/military-ui/header.php';
?>
<div class="mil-content-wrapper">
  <div class="terminal-container">
    <!-- Header -->
    <div class="terminal-header">
      <h1><i class="fas fa-file-code"></i> XML INTELLIGENCE PARSER – XXE Lab (Level 1)</h1>
      <p class="terminal-subtitle">XML External Entity Injection Vulnerability</p>
    </div>

    <!-- Objective -->
    <div class="mission-briefing">
      <div class="briefing-icon"><i class="fas fa-bullseye"></i></div>
      <div class="briefing-content">
        <h3>Mission Objective</h3>
        <p>Exploit the XML parser to read sensitive files from the server. The system accepts XML agent reports but doesn't properly disable external entities.</p>
        <p class="hint-text">Craft a malicious XML payload with an external entity that reads <code>/etc/passwd</code> or other sensitive files.</p>
      </div>
    </div>

    <!-- Message Display -->
    <?php if (!empty($this->message)): ?>
    <div class="alert-box <?= $this->solved ? 'alert-success' : 'alert-info' ?>">
      <i class="fas fa-info-circle"></i> <?= $this->message ?>
    </div>
    <?php endif; ?>

    <!-- Parsed Data Display -->
    <?php if ($this->parsedData): ?>
    <div class="info-panel">
      <h3><i class="fas fa-table"></i> Parsed XML Data</h3>
      <table class="mil-table">
        <thead>
          <tr>
            <th>Field</th>
            <th>Value</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($this->parsedData as $key => $value): ?>
          <tr>
            <td><?= htmlspecialchars($key) ?></td>
            <td class="xml-value"><?= htmlspecialchars($value) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>

    <!-- XML Upload Form -->
    <div class="terminal-form">
      <h2><i class="fas fa-upload"></i> Submit Agent Report (XML Format)</h2>
      <form method="POST" class="mil-form">
        <div class="form-group">
          <label for="xml_data">XML Report Content:</label>
          <textarea id="xml_data" name="xml_data" rows="10" class="mil-input mil-textarea" placeholder="Enter XML content..."><?php echo htmlspecialchars($this->xmlContent ?: '<?xml version="1.0"?>
<agent_report>
  <codename>YourCodename</codename>
  <mission>Mission Description</mission>
  <status>Complete</status>
</agent_report>'); ?></textarea>
        </div>
        <button type="submit" class="mil-btn mil-btn-primary">
          <i class="fas fa-play"></i> PARSE XML
        </button>
      </form>
      
      <form method="POST" style="margin-top: 1rem;">
        <button type="submit" name="reset_lab" class="mil-btn mil-btn-secondary">
          <i class="fas fa-redo"></i> RESET LAB
        </button>
      </form>
    </div>

    <!-- Attack Examples -->
    <div class="code-panel">
      <h3><i class="fas fa-code"></i> XXE Payload Examples</h3>
      
      <h4>Basic XXE - Read /etc/passwd:</h4>
      <pre><code>&lt;?xml version="1.0"?&gt;
&lt;!DOCTYPE agent_report [
  &lt;!ENTITY xxe SYSTEM "file:///etc/passwd"&gt;
]&gt;
&lt;agent_report&gt;
  &lt;codename&gt;&amp;xxe;&lt;/codename&gt;
  &lt;mission&gt;Test&lt;/mission&gt;
&lt;/agent_report&gt;</code></pre>

      <h4>XXE with Internal Entity:</h4>
      <pre><code>&lt;?xml version="1.0"?&gt;
&lt;!DOCTYPE agent_report [
  &lt;!ENTITY file SYSTEM "file:///etc/passwd"&gt;
  &lt;!ENTITY wrapper "&amp;file;"&gt;
]&gt;
&lt;agent_report&gt;
  &lt;codename&gt;&amp;wrapper;&lt;/codename&gt;
&lt;/agent_report&gt;</code></pre>
    </div>

    <!-- Success Message -->
    <?php if ($this->solved): ?>
    <div class="success-banner">
      <i class="fas fa-check-circle"></i>
      <div>
        <h3>MISSION ACCOMPLISHED</h3>
        <p>You successfully exploited the XXE vulnerability to extract sensitive server data!</p>
      </div>
    </div>
    <?php endif; ?>

    <!-- Hint Section -->
    <details class="intel-briefing">
      <summary><i class="fas fa-book"></i> Intelligence Briefing (Hint)</summary>
      <div class="briefing-details">
        <p>XXE (XML External Entity) attacks occur when XML parsers allow external entity references. By defining a custom entity that references a file, you can read server files.</p>
        <p>The DOCTYPE declaration allows you to define entities. Use <code>&lt;!ENTITY name SYSTEM "file:///path/to/file"&gt;</code> to reference files.</p>
        <p>Try reading <code>/etc/passwd</code> on Linux systems or look for other sensitive configuration files.</p>
      </div>
    </details>
  </div>
</div>

<style>
.mil-content-wrapper { padding: 20px; }
.terminal-container { max-width: 1000px; margin: 0 auto; }
.terminal-header h1 { color: #00ff41; font-size: 1.5rem; margin-bottom: 0.5rem; }
.terminal-subtitle { color: #008f11; font-size: 0.9rem; }
.mission-briefing { background: rgba(0, 255, 65, 0.1); border-left: 3px solid #00ff41; padding: 15px; margin: 20px 0; display: flex; gap: 15px; }
.briefing-icon { font-size: 2rem; color: #00ff41; }
.briefing-content h3 { color: #00ff41; margin: 0 0 10px 0; }
.briefing-content p { color: #c0c0c0; margin: 5px 0; }
.hint-text { font-style: italic; color: #888; }
.alert-box { padding: 15px; margin: 15px 0; border-radius: 4px; }
.alert-success { background: rgba(0, 255, 65, 0.2); border: 1px solid #00ff41; color: #00ff41; }
.alert-info { background: rgba(0, 170, 255, 0.2); border: 1px solid #00aaff; color: #00aaff; }
.info-panel { background: rgba(0, 0, 0, 0.3); padding: 20px; margin: 20px 0; border: 1px solid #333; }
.info-panel h3 { color: #00ff41; margin-bottom: 15px; }
.mil-table { width: 100%; border-collapse: collapse; }
.mil-table th, .mil-table td { padding: 10px; text-align: left; border: 1px solid #333; }
.mil-table th { background: rgba(0, 255, 65, 0.1); color: #00ff41; }
.mil-table td { color: #c0c0c0; }
.xml-value { font-family: monospace; word-break: break-all; color: #ffaa00; }
.terminal-form { background: rgba(0, 0, 0, 0.3); padding: 20px; margin: 20px 0; border: 1px solid #333; }
.terminal-form h2 { color: #00ff41; margin-bottom: 20px; }
.mil-form .form-group { margin-bottom: 15px; }
.mil-form label { display: block; color: #888; margin-bottom: 5px; }
.mil-input { width: 100%; padding: 10px; background: #111; border: 1px solid #333; color: #00ff41; font-family: monospace; }
.mil-textarea { font-family: 'Courier New', monospace; resize: vertical; }
.mil-input:focus { outline: none; border-color: #00ff41; }
.mil-btn { padding: 10px 20px; border: none; cursor: pointer; font-family: monospace; text-transform: uppercase; margin-right: 10px; }
.mil-btn-primary { background: #00ff41; color: #000; }
.mil-btn-primary:hover { background: #00cc33; }
.mil-btn-secondary { background: #333; color: #888; }
.code-panel { background: #0a0a0a; padding: 15px; margin: 20px 0; border: 1px solid #333; }
.code-panel h3 { color: #00ff41; margin-bottom: 10px; }
.code-panel h4 { color: #008f11; margin: 15px 0 10px 0; font-size: 0.9rem; }
.code-panel pre { background: #111; padding: 15px; overflow-x: auto; color: #0f0; margin: 10px 0; }
.success-banner { background: rgba(0, 255, 65, 0.2); border: 2px solid #00ff41; padding: 20px; margin: 20px 0; display: flex; gap: 20px; align-items: center; }
.success-banner i { font-size: 3rem; color: #00ff41; }
.success-banner h3 { color: #00ff41; margin: 0; }
.intel-briefing { background: rgba(255, 255, 255, 0.05); padding: 15px; margin: 20px 0; cursor: pointer; }
.intel-briefing summary { color: #00ff41; font-weight: bold; }
.briefing-details { padding: 15px 0 0 20px; color: #888; border-left: 2px solid #333; margin-top: 10px; }
</style>
<?php
    include_once ROOT . '/shared/military-ui/footer.php';
  }

  public function validate(): bool
  {
    $solved = Session::get('xxe_lvl1_solved') === true;
    if ($solved) {
      $this->markCompleted('xxe', 'lvl1');
    }
    return $solved;
  }
}
