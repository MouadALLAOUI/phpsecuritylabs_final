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
    // Persist to cookie for cross-session persistence
    setcookie('theme', $_POST['theme'], time() + (30 * 24 * 60 * 60), '/');
  }
  
  if (isset($_POST['language'])) {
    Session::set('language', $_POST['language']);
    // Persist to cookie for cross-session persistence
    setcookie('language', $_POST['language'], time() + (30 * 24 * 60 * 60), '/');
  }
  
  $successMessage = "Settings saved successfully!";
}

// Get current settings (check cookies first, then session, then defaults)
$currentTheme = $_COOKIE['theme'] ?? Session::get('theme', 'military');
$currentLanguage = $_COOKIE['language'] ?? Session::get('language', 'en');

$themes = [
  'light' => 'Light Mode',
  'dark' => 'Dark Mode',
  'military' => 'Military Mode (Default)'
];

$languages = [
  'en' => 'English',
  'es' => 'Español (Spanish)',
  'fr' => 'Français (French)',
  'de' => 'Deutsch (German)'
];

include_once ROOT . '/shared/header.php';
?>

<div class="max-w-4xl mx-auto">
  <!-- Page Header -->
  <div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">
      <i class="fas fa-cog text-indigo-600 mr-2"></i> Settings
    </h1>
    <p class="text-gray-600 mt-2">Customize your PHP Security Labs experience</p>
  </div>

  <!-- Success Message -->
  <?php if (isset($successMessage)): ?>
  <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
    <div class="flex items-center">
      <i class="fas fa-check-circle text-green-500 mr-3"></i>
      <p class="text-green-700"><?= htmlspecialchars($successMessage) ?></p>
    </div>
  </div>
  <?php endif; ?>

  <!-- Theme Settings -->
  <div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">
      <i class="fas fa-palette text-indigo-600 mr-2"></i> Theme Settings
    </h2>
    
    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Select Theme</label>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <?php foreach ($themes as $value => $label): ?>
          <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer 
                        <?= $currentTheme === $value ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200 hover:border-gray-300' ?>">
            <input type="radio" name="theme" value="<?= $value ?>" 
                   class="sr-only" 
                   <?= $currentTheme === $value ? 'checked' : '' ?>>
            <div class="flex items-center">
              <div class="w-4 h-4 rounded-full border-2 border-gray-400 mr-3 
                          <?= $currentTheme === $value ? 'border-indigo-600' : '' ?>">
                <?php if ($currentTheme === $value): ?>
                <div class="w-full h-full rounded-full bg-indigo-600"></div>
                <?php endif; ?>
              </div>
              <span class="text-gray-700"><?= $label ?></span>
            </div>
          </label>
          <?php endforeach; ?>
        </div>
        <p class="mt-2 text-sm text-gray-500">
          <i class="fas fa-info-circle mr-1"></i>
          Note: Military mode is forced dark theme with tactical UI elements.
        </p>
      </div>
      
      <div class="pt-4">
        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
          <i class="fas fa-save mr-2"></i> Save Theme
        </button>
      </div>
    </form>
  </div>

  <!-- Language Settings -->
  <div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">
      <i class="fas fa-language text-indigo-600 mr-2"></i> Language Settings
    </h2>
    
    <form method="POST" class="space-y-4">
      <div>
        <label for="language" class="block text-sm font-medium text-gray-700 mb-2">Select Language</label>
        <select name="language" id="language" 
                class="w-full md:w-1/2 px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
          <?php foreach ($languages as $code => $name): ?>
          <option value="<?= $code ?>" <?= $currentLanguage === $code ? 'selected' : '' ?>>
            <?= $name ?>
          </option>
          <?php endforeach; ?>
        </select>
        <p class="mt-2 text-sm text-gray-500">
          <i class="fas fa-info-circle mr-1"></i>
          Language translations are stored in the /lang/ directory.
        </p>
      </div>
      
      <div class="pt-4">
        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
          <i class="fas fa-save mr-2"></i> Save Language
        </button>
      </div>
    </form>
  </div>

  <!-- Lab Vulnerability Settings Info -->
  <div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">
      <i class="fas fa-bug text-indigo-600 mr-2"></i> Lab Vulnerability Toggles
    </h2>
    
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
      <div class="flex items-start">
        <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-3"></i>
        <div>
          <p class="font-medium text-blue-900">Vulnerability Persistence</p>
          <p class="text-sm text-blue-700 mt-1">
            Lab vulnerability toggles are now persisted to your session and database. 
            Your settings will be saved across page reloads and browser sessions.
          </p>
        </div>
      </div>
    </div>
    
    <div class="mt-4">
      <a href="?page=labs" class="inline-flex items-center text-indigo-600 hover:text-indigo-800">
        <i class="fas fa-flask mr-2"></i> Go to Labs to configure vulnerabilities
        <i class="fas fa-arrow-right ml-2"></i>
      </a>
    </div>
  </div>

  <!-- Reset All Settings -->
  <div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">
      <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i> Danger Zone
    </h2>
    
    <form method="POST" action="?page=settings&action=reset" onsubmit="return confirm('Are you sure you want to reset all settings?');">
      <p class="text-sm text-gray-600 mb-4">
        This will reset all your personalized settings to their default values.
      </p>
      <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
        <i class="fas fa-trash-alt mr-2"></i> Reset All Settings
      </button>
    </form>
  </div>
</div>

<?php include_once ROOT . '/shared/footer.php'; ?>
