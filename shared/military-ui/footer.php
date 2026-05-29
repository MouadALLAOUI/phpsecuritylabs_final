<?php

/**
 * MIL-OPS CONTROL SYSTEM - Military Theme Footer
 * Redesigned for Cyber Range Console
 */
?>
</main>
<?php
// Consistent Level Navigation Footer for Military UI
if (isset($_GET['page'], $_GET['lvl'])) {
    $currentLab = $_GET['page'];
    $currentLvl = (int)$_GET['lvl'];
    $mapFile = ROOT . "/labs/{$currentLab}/challenge_map.php";
    if (file_exists($mapFile)) {
        $map = require $mapFile;
        if (is_array($map)) {
            $levels = [];
            foreach (array_keys($map) as $key) {
                if (preg_match('/^lvl(\d+)$/', $key, $matches)) {
                    $levels[] = (int)$matches[1];
                }
            }
            sort($levels);
            if (in_array($currentLvl, $levels)) {
                $hasPrev = in_array($currentLvl - 1, $levels);
                $hasNext = in_array($currentLvl + 1, $levels);
                $prevUrl = $hasPrev ? "?page={$currentLab}&lvl=" . ($currentLvl - 1) : "#";
                $nextUrl = $hasNext ? "?page={$currentLab}&lvl=" . ($currentLvl + 1) : "#";
                ?>
                <div class="terminal-panel" style="margin-top: 20px; border-color: var(--border-color); background: rgba(11, 15, 25, 0.9);">
                  <div class="terminal-body" style="padding: 15px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                    <!-- Back to Catalog -->
                    <a href="?page=labs" class="mil-button" style="padding: 8px 16px; font-size: 11px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; background: rgba(0, 170, 255, 0.1); border-color: #00aaff; color: #00aaff;">
                      <i class="fas fa-th-large"></i> Back to Labs
                    </a>

                    <div style="display: flex; gap: 10px;">
                      <!-- Previous Level -->
                      <?php if ($hasPrev): ?>
                      <a href="<?= $prevUrl ?>" class="mil-button" style="padding: 8px 16px; font-size: 11px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-chevron-left"></i> Previous
                      </a>
                      <?php else: ?>
                      <span class="mil-button" style="padding: 8px 16px; font-size: 11px; display: inline-flex; align-items: center; gap: 8px; opacity: 0.4; cursor: not-allowed; pointer-events: none;">
                        <i class="fas fa-chevron-left"></i> Previous
                      </span>
                      <?php endif; ?>

                      <!-- Next Level -->
                      <?php if ($hasNext): ?>
                      <a href="<?= $nextUrl ?>" class="mil-button" style="padding: 8px 16px; font-size: 11px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; background: rgba(0, 255, 65, 0.15); border-color: #00ff41; color: #00ff41;">
                        Next <i class="fas fa-chevron-right"></i>
                      </a>
                      <?php else: ?>
                      <span class="mil-button" style="padding: 8px 16px; font-size: 11px; display: inline-flex; align-items: center; gap: 8px; opacity: 0.4; cursor: not-allowed; pointer-events: none;">
                        Next <i class="fas fa-chevron-right"></i>
                      </span>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
                <?php
            }
        }
    }
}
?>

<!-- SYSTEM ALERT MODAL -->
<div id="systemAlert" class="mil-alert-modal hidden">
  <div class="alert-content">
    <div class="alert-header">
      <i class="fas fa-exclamation-triangle"></i>
      <span>SYSTEM NOTICE</span>
    </div>
    <div class="alert-body" id="alertBody">
      <!-- Alert message inserted here -->
    </div>
    <div class="alert-footer">
      <button class="btn-dismiss" onclick="closeSystemAlert()">Acknowledge</button>
    </div>
  </div>
</div>

<!-- LOADING OVERLAY -->
<div id="loadingOverlay" class="mil-loading-overlay hidden">
  <div class="loading-content">
    <div class="loading-spinner"></div>
    <div class="loading-text" id="loadingText">ESTABLISHING SECURE CHANNEL...</div>
    <div class="loading-progress">
      <div class="progress-bar"></div>
    </div>
  </div>
</div>

<!-- TOAST NOTIFICATION -->
<div id="toastNotification" class="mil-toast hidden">
  <div class="toast-icon">
    <i class="fas fa-check-circle text-teal-400"></i>
  </div>
  <div class="toast-message" id="toastMessage">
    Challenge completed! Progress saved.
  </div>
  <div class="toast-close" onclick="closeToast()">
    <i class="fas fa-times"></i>
  </div>
</div>

<!-- Military UI JavaScript -->
<?php
$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($baseUrl === '/') $baseUrl = '';
?>
<script src="<?= $baseUrl ?>/shared/military-ui/mil-ops.js"></script>

<?php if (isset($_SESSION['toast_message'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  showToast(<?= json_encode($_SESSION['toast_message']) ?>);
  <?php unset($_SESSION['toast_message']); ?>
});
</script>
<?php endif; ?>

<footer class="mil-footer theme-transition">
  <div class="footer-content">
    <span class="classification">CLASSIFIED // FOR TRAINING PURPOSES ONLY</span>
    <span class="timestamp" id="footerTimestamp"><?php echo date('Y-m-d H:i:s'); ?></span>
    <span>CYBER RANGE TERMINAL v4.0.0</span>
  </div>
</footer>
</body>

</html>