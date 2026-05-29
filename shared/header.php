<?php

/**
 * Global header for the secure core application.
 * Included at the start of every page.
 */
?>
<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PHP Security Labs - Cyber Range Console</title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

  <!-- TailwindCSS (CDN) -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
            mono: ['JetBrains Mono', 'Fira Code', 'monospace'],
          },
          colors: {
            cyber: {
              main: '#0b0f19',
              surface: '#0f172a',
              card: '#1e293b',
              panel: '#1e293b',
              border: '#334155',
              primary: '#3b82f6',
              secondary: '#0d9488',
              accent: '#7c3aed',
              amber: '#d97706',
              red: '#dc2626',
              green: '#0d9488',
              text: '#f8fafc',
              muted: '#94a3b8'
            }
          }
        }
      }
    }
  </script>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  <style>
    /* Global style overrides to support standard light/dark/military cycling cleanly */
    body {
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      transition: background-color 0.25s ease, color 0.2s ease;
    }
    
    /* Support theme-specific colors cleanly */
    body.theme-military {
      background-color: #0b0f19;
      color: #f8fafc;
    }
    body.theme-dark {
      background-color: #030712;
      color: #f3f4f6;
    }
    body.theme-light {
      background-color: #f8fafc;
      color: #0f172a;
    }

    /* Style focus rings globally */
    a:focus-visible, button:focus-visible, select:focus-visible, input:focus-visible {
      outline: 2px solid #3b82f6 !important;
      outline-offset: 2px;
    }

    /* Smooth transitions for theme transitions */
    .theme-transition {
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Premium custom scrollbars globally */
    ::-webkit-scrollbar {
      width: 8px;
      height: 8px;
    }
    ::-webkit-scrollbar-track {
      background: transparent;
    }
    ::-webkit-scrollbar-thumb {
      background: rgba(148, 163, 184, 0.2);
      border-radius: 9999px;
      border: 2px solid transparent;
      background-clip: padding-box;
      transition: background-color 0.2s ease;
    }
    ::-webkit-scrollbar-thumb:hover {
      background: rgba(148, 163, 184, 0.4);
      border: 2px solid transparent;
      background-clip: padding-box;
    }
    /* Firefox support */
    * {
      scrollbar-width: thin;
      scrollbar-color: rgba(148, 163, 184, 0.2) transparent;
    }
  </style>
</head>

<body class="h-full theme-military theme-transition text-slate-200">

  <!-- Top Sticky Navigation -->
  <nav class="bg-slate-900 border-b border-slate-800 sticky top-0 z-50 shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        
        <!-- Left Brand / Logo -->
        <div class="flex items-center space-x-3">
          <div class="bg-blue-600/10 p-2 rounded-lg border border-blue-500/20">
            <i class="fas fa-shield-halved text-blue-500 text-xl"></i>
          </div>
          <span class="text-md font-bold tracking-wider text-slate-100 uppercase">Cyber Range Console</span>
        </div>

        <!-- Right navigation actions (desktop) -->
        <div class="hidden md:flex items-center space-x-4">
          <div class="flex items-center space-x-1 border-r border-slate-800 pr-4">
            <a href="?page=home"
              class="px-3 py-2 rounded-lg text-xs font-semibold text-slate-300 hover:bg-slate-800 hover:text-white transition flex items-center gap-1.5">
              <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="?page=labs"
              class="px-3 py-2 rounded-lg text-xs font-semibold text-slate-300 hover:bg-slate-800 hover:text-white transition flex items-center gap-1.5">
              <i class="fas fa-flask"></i> Labs
            </a>
            <a href="?page=profile"
              class="px-3 py-2 rounded-lg text-xs font-semibold text-slate-300 hover:bg-slate-800 hover:text-white transition flex items-center gap-1.5">
              <i class="fas fa-user-circle"></i> Profile
            </a>
          </div>

          <!-- Utility Selectors & Theme toggler -->
          <div class="flex items-center space-x-3">
            <!-- Language Selector -->
            <div class="relative">
              <label for="languageSelector" class="sr-only">Select Language</label>
              <select id="languageSelector" onchange="changeLanguage(this.value)" 
                class="bg-slate-850 text-slate-300 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs font-medium focus:outline-none focus:border-blue-500 bg-slate-900 cursor-pointer">
                <option value="en" <?= (($_SESSION['lang'] ?? $_COOKIE['lang'] ?? 'en') === 'en') ? 'selected' : '' ?>>EN</option>
                <option value="fr" <?= (($_SESSION['lang'] ?? $_COOKIE['lang'] ?? 'en') === 'fr') ? 'selected' : '' ?>>FR</option>
              </select>
            </div>
            
            <!-- Theme Toggle Button -->
            <button onclick="toggleTheme()" aria-label="Toggle visual theme"
              class="bg-slate-800 text-slate-300 border border-slate-700 rounded-lg p-2 hover:bg-slate-700 focus:outline-none transition flex items-center justify-center">
              <i class="fas fa-adjust text-sm"></i>
            </button>
          </div>
        </div>

        <!-- Mobile Menu Hamburger button -->
        <div class="md:hidden">
          <button type="button" class="text-slate-400 hover:text-white p-2 rounded-lg hover:bg-slate-800 transition"
            aria-label="Toggle navigation menu"
            onclick="document.getElementById('mobile-menu').classList.toggle('hidden');">
            <i class="fas fa-bars text-lg"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Drawer Menu (hidden by default) -->
    <div id="mobile-menu" class="hidden md:hidden border-t border-slate-800 bg-slate-950 px-4 py-3 space-y-2">
      <a href="?page=home"
        class="block px-3 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition flex items-center">
        <i class="fas fa-tachometer-alt mr-3 text-blue-500"></i> Dashboard
      </a>
      <a href="?page=labs"
        class="block px-3 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition flex items-center">
        <i class="fas fa-flask mr-3 text-teal-500"></i> Labs
      </a>
      <a href="?page=profile"
        class="block px-3 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition flex items-center">
        <i class="fas fa-user-circle mr-3 text-violet-500"></i> Profile
      </a>
      
      <!-- Selectors (Mobile) -->
      <div class="flex items-center justify-between pt-4 border-t border-slate-800">
        <select onchange="changeLanguage(this.value)" 
          class="bg-slate-900 text-slate-300 border border-slate-700 rounded-lg px-3 py-1.5 text-xs font-medium">
          <option value="en" <?= (($_SESSION['lang'] ?? $_COOKIE['lang'] ?? 'en') === 'en') ? 'selected' : '' ?>>EN</option>
          <option value="fr" <?= (($_SESSION['lang'] ?? $_COOKIE['lang'] ?? 'en') === 'fr') ? 'selected' : '' ?>>FR</option>
        </select>
        <button onclick="toggleTheme()" 
          class="bg-slate-800 text-slate-300 border border-slate-700 rounded-lg p-2 flex items-center">
          <i class="fas fa-adjust text-sm"></i>
        </button>
      </div>
    </div>
  </nav>

  <!-- Base Theme Handler Script -->
  <script>
    // Apply theme immediately on load to prevent light flash on dark/military themes
    (function() {
      var savedTheme = localStorage.getItem('theme') || 'military';
      document.body.classList.remove('theme-light', 'theme-dark', 'theme-military');
      document.body.classList.add('theme-' + savedTheme);
    })();

    /**
     * POST language selection form helper
     */
    function changeLanguage(lang) {
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
     * Cycle through military -> light -> dark themes
     */
    function toggleTheme() {
      const currentTheme = document.body.classList.contains('theme-light') ? 'light' : 
                           (document.body.classList.contains('theme-dark') ? 'dark' : 'military');
      const themes = ['military', 'light', 'dark'];
      const currentIndex = themes.indexOf(currentTheme);
      const nextTheme = themes[(currentIndex + 1) % themes.length];

      // Update state
      document.body.classList.remove('theme-light', 'theme-dark', 'theme-military');
      document.body.classList.add('theme-' + nextTheme);

      localStorage.setItem('theme', nextTheme);
      document.cookie = 'theme=' + nextTheme + '; path=/; max-age=' + (30 * 24 * 60 * 60);

      // Submit theme setting via fetch API
      fetch('?page=settings&action=theme', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'theme=' + encodeURIComponent(nextTheme)
      }).catch(err => console.log('Theme synchronization failed:', err));
    }
  </script>

  <!-- Main view wrapper -->
  <main class="w-full">
