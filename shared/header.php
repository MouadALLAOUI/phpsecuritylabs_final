<?php

/**
 * Global header for the secure core application.
 * Included at the start of every page.
 */
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PHP Security Labs</title>

  <!-- TailwindCSS (CDN) -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Font Awesome (optional, for icons) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- Minimal custom styles (can be overridden in page if needed) -->
  <style>
    body {
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }
  </style>
</head>

<body class="h-full bg-gray-100 text-gray-800">

  <!-- Navigation -->
  <nav class="bg-gray-900 shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <!-- Left side: logo / brand -->
        <div class="flex items-center space-x-3">
          <i class="fas fa-shield-haltered text-indigo-400 text-2xl"></i>
          <span class="text-xl font-semibold tracking-tight text-white">PHP Security Labs</span>
        </div>

        <!-- Right side: navigation links -->
        <div class="hidden md:block">
          <div class="flex items-baseline space-x-1">
            <a href="?page=home"
              class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white transition">
              <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
            </a>
            <a href="?page=labs"
              class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white transition">
              <i class="fas fa-flask mr-1"></i> Labs
            </a>
            <a href="?page=profile"
              class="px-3 py-2 rounded-md text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white transition">
              <i class="fas fa-user-circle mr-1"></i> Profile
            </a>
            <!-- Language Selector -->
            <select id="languageSelector" onchange="changeLanguage(this.value)" 
              class="ml-2 bg-gray-800 text-gray-300 border border-gray-600 rounded px-2 py-1 text-xs focus:outline-none focus:border-indigo-500">
              <option value="en" <?= (($_SESSION['lang'] ?? $_COOKIE['lang'] ?? 'en') === 'en') ? 'selected' : '' ?>>EN</option>
              <option value="fr" <?= (($_SESSION['lang'] ?? $_COOKIE['lang'] ?? 'en') === 'fr') ? 'selected' : '' ?>>FR</option>
            </select>
            <!-- Theme Toggle -->
            <button onclick="toggleTheme()" 
              class="ml-1 bg-gray-800 text-gray-300 border border-gray-600 rounded px-2 py-1 text-xs hover:bg-gray-700 focus:outline-none">
              <i class="fas fa-adjust"></i>
            </button>
          </div>
        </div>

        <!-- Mobile menu button -->
        <div class="md:hidden">
          <button type="button" class="text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-white"
            onclick="document.getElementById('mobile-menu').classList.toggle('hidden');">
            <i class="fas fa-bars text-xl"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile menu (hidden by default) -->
    <div id="mobile-menu" class="hidden md:hidden">
      <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
        <a href="?page=home"
          class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">
          <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
        </a>
        <a href="?page=labs"
          class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">
          <i class="fas fa-flask mr-2"></i> Labs
        </a>
        <a href="?page=profile"
          class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">
          <i class="fas fa-user-circle mr-2"></i> Profile
        </a>
      </div>
    </div>
  </nav>

  <!-- Main content area starts here. Pages will add their own container. -->
  <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

<script>
// Apply saved theme immediately on page load (before CSS loads to prevent flash)
(function() {
  var savedTheme = localStorage.getItem('theme') || 'military';
  document.body.className = 'theme-' + savedTheme;
})();

/**
 * Change language function - uses session-based language switching
 */
function changeLanguage(lang) {
  // Create a temporary form to submit the language change
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = '?page=settings&action=language';
  
  const input = document.createElement('input');
  input.type = 'hidden';
  input.name = 'language';
  input.value = lang;
  
  form.appendChild(input);
  document.body.appendChild(form);
  form.submit();
}

/**
 * Toggle theme function - switches between light/dark/military themes
 */
function toggleTheme() {
  const currentTheme = document.body.classList.contains('theme-light') ? 'light' : 
                       (document.body.classList.contains('theme-dark') ? 'dark' : 'military');
  const themes = ['military', 'light', 'dark'];
  const currentIndex = themes.indexOf(currentTheme);
  const nextTheme = themes[(currentIndex + 1) % themes.length];

  // Remove all theme classes and add the new one
  document.body.classList.remove('theme-light', 'theme-dark', 'theme-military');
  document.body.classList.add('theme-' + nextTheme);

  // Save to localStorage for persistence across pages
  localStorage.setItem('theme', nextTheme);
  
  // Persist to cookie
  document.cookie = 'theme=' + nextTheme + '; path=/; max-age=' + (30 * 24 * 60 * 60);

  // Also update via AJAX if possible
  fetch('?page=settings&action=theme', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'theme=' + encodeURIComponent(nextTheme)
  }).catch(err => console.log('Theme update failed:', err));
}
</script>
