<?php
/**
 * Settings Page - Theme toggle and language selection
 */

use App\Core\Session;

// Initialize session
if (session_status() === PHP_SESSION_NONE) {
  Session::start();
}

// Handle theme change
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['theme'])) {
    Session::set('theme', $_POST['theme']);
    setcookie('theme', $_POST['theme'], time() + (30 * 24 * 60 * 60), '/');
  }
  
  if (isset($_POST['language'])) {
    $lang = $_POST['language'];
    if (!in_array($lang, ['en', 'fr'])) {
      $lang = 'en';
    }
    Session::set('lang', $lang);
    setcookie('lang', $lang, time() + (30 * 24 * 60 * 60), '/');
  }
  
  $successMessage = "Settings saved successfully!";
}

$currentTheme = $_COOKIE['theme'] ?? Session::get('theme', 'military');
$currentLanguage = $_COOKIE['lang'] ?? Session::get('lang', 'en');

$themes = [
  'light' => 'Light Theme',
  'dark' => 'Dark Theme',
  'military' => 'Cyber Range Theme (Default)'
];

$languages = [
  'en' => 'English (US/UK)',
  'fr' => 'Français (French)'
];

include_once ROOT . '/shared/header.php';
include_once ROOT . '/shared/sidebar.php';
?>

<div class="lg:ml-64 p-6 min-h-[85vh] theme-transition">
  <div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Page Title Header -->
    <div class="border-b border-slate-800 pb-4 mb-6">
      <h1 class="text-xl font-bold text-slate-100 uppercase tracking-wide flex items-center gap-2.5">
        <i class="fas fa-sliders text-blue-500"></i> Console Settings
      </h1>
      <p class="text-slate-400 text-xs mt-1">Configure your personal operator controls and console behaviors.</p>
    </div>

    <!-- Success Feedback notice -->
    <?php if (isset($successMessage)): ?>
    <div class="bg-teal-950/40 border border-teal-500/20 p-4 rounded-xl flex items-start space-x-3 text-teal-200">
      <i class="fas fa-check-circle text-teal-400 mt-0.5"></i>
      <p class="text-xs font-semibold"><?= htmlspecialchars($successMessage) ?></p>
    </div>
    <?php endif; ?>

    <!-- Theme Settings Card -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-md">
      <h2 class="text-sm font-bold text-slate-100 uppercase tracking-wider mb-5 flex items-center gap-2">
        <i class="fas fa-palette text-blue-500"></i> Theme Options
      </h2>
      
      <form method="POST" class="space-y-6">
        <div>
          <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-3">Select Console Skin</label>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <?php foreach ($themes as $value => $label): ?>
            <label class="relative flex items-center p-4 border rounded-lg cursor-pointer transition select-none <?= $currentTheme === $value ? 'border-blue-600 bg-blue-600/10' : 'border-slate-850 bg-slate-950/40 hover:border-slate-700' ?>">
              <input type="radio" name="theme" value="<?= $value ?>" 
                     class="sr-only" 
                     <?= $currentTheme === $value ? 'checked' : '' ?>>
              <div class="flex items-center">
                <div class="w-4 h-4 rounded-full border border-slate-600 mr-3 flex items-center justify-center transition <?= $currentTheme === $value ? 'border-blue-500' : '' ?>">
                  <?php if ($currentTheme === $value): ?>
                  <div class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse"></div>
                  <?php endif; ?>
                </div>
                <span class="text-xs font-semibold text-slate-200 uppercase tracking-wide"><?= $label ?></span>
              </div>
            </label>
            <?php endforeach; ?>
          </div>
        </div>
        
        <div class="pt-4 border-t border-slate-800/80 flex justify-between items-center gap-4">
          <p class="text-[10px] text-slate-400 font-mono flex items-center gap-1.5"><i class="fas fa-circle-info text-blue-500"></i> Cyber Range is optimized for dark training sessions.</p>
          <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 px-6 rounded-lg text-xs uppercase tracking-wider transition">
            Apply Skin
          </button>
        </div>
      </form>
    </div>

    <!-- Language Settings Card -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-md">
      <h2 class="text-sm font-bold text-slate-100 uppercase tracking-wider mb-5 flex items-center gap-2">
        <i class="fas fa-language text-blue-500"></i> Language Settings
      </h2>
      
      <form method="POST" class="space-y-4">
        <div class="space-y-2">
          <label for="language" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Interface Language</label>
          <select name="language" id="language" 
                  class="w-full md:w-1/2 px-3.5 py-3 border border-slate-700 bg-slate-950 text-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-xs font-semibold uppercase tracking-wider cursor-pointer">
            <?php foreach ($languages as $code => $name): ?>
            <option value="<?= $code ?>" <?= $currentLanguage === $code ? 'selected' : '' ?>>
              <?= $name ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        
        <div class="pt-4 border-t border-slate-800/80 flex justify-end">
          <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 px-6 rounded-lg text-xs uppercase tracking-wider transition">
            Save Language
          </button>
        </div>
      </form>
    </div>

    <!-- Sandbox configuration card -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-md">
      <h2 class="text-sm font-bold text-slate-100 uppercase tracking-wider mb-4 flex items-center gap-2">
        <i class="fas fa-bug text-blue-500"></i> Sandbox Configuration Toggles
      </h2>
      <div class="bg-blue-950/20 border border-blue-500/20 p-4 rounded-lg flex items-start space-x-3 text-blue-200">
        <i class="fas fa-info-circle text-blue-400 mt-0.5"></i>
        <div>
          <p class="text-xs font-bold uppercase tracking-wider">Persistence Status</p>
          <p class="text-xs text-slate-400 mt-1 leading-relaxed">
            Sandbox vulnerability toggle flags are persisted automatically under your active session and database profile. Go to the training catalog to toggling states.
          </p>
        </div>
      </div>
      <div class="mt-5">
        <a href="?page=labs" class="inline-flex items-center text-xs font-bold text-blue-400 hover:text-blue-300 transition gap-1">
          Catalog directives <i class="fas fa-arrow-right text-[9px] ml-1"></i>
        </a>
      </div>
    </div>

    <!-- Danger Zone Panel Card -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-md border-l-4 border-l-red-500">
      <h2 class="text-sm font-bold text-red-400 uppercase tracking-wider mb-4 flex items-center gap-2">
        <i class="fas fa-triangle-exclamation"></i> Danger Zone
      </h2>
      
      <form method="POST" action="?page=settings&action=reset" onsubmit="return confirm('Clear all settings flags? This action is irreversible.');">
        <p class="text-xs text-slate-400 leading-relaxed mb-4">
          This operation resets all personalized variables, layouts, language parameters, and styles to their default settings.
        </p>
        <button type="submit" class="bg-red-600/10 border border-red-500/20 hover:bg-red-600 hover:text-white text-red-400 font-bold py-2.5 px-6 rounded-lg text-xs uppercase tracking-wider transition">
          Reset Console Profile
        </button>
      </form>
    </div>

  </div>
</div>

<?php include_once ROOT . '/shared/footer.php'; ?>
