<?php

/**
 * MIL-OPS CONTROL SYSTEM - Military Theme Footer
 */
?>
</main>

<!-- SYSTEM ALERT MODAL -->
<div id="systemAlert" class="mil-alert-modal hidden">
  <div class="alert-content">
    <div class="alert-header">
      <i class="fas fa-exclamation-triangle"></i>
      <span>SYSTEM ALERT</span>
    </div>
    <div class="alert-body" id="alertBody">
      <!-- Alert message inserted here -->
    </div>
    <div class="alert-footer">
      <button class="btn-dismiss" onclick="closeSystemAlert()">ACKNOWLEDGE</button>
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
    <i class="fas fa-check-circle"></i>
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

<footer class="mil-footer">
  <div class="footer-content">
    <span class="classification">UNCLASSIFIED//FOUO</span>
    <span class="timestamp" id="footerTimestamp"><?php echo date('Y-m-d H:i:s'); ?></span>
    <span>MIL-OPS TERMINAL v3.2.1</span>
  </div>
</footer>
</body>

</html>