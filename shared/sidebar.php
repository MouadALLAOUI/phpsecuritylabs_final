<?php

/**
 * Sidebar navigation for the main application layout.
 * Use alongside header and footer.
 */
?>
<aside class="hidden lg:block lg:fixed lg:inset-y-0 lg:left-0 lg:w-64 lg:z-40 bg-gray-900 text-gray-300 shadow-lg">
  <div class="flex flex-col h-full">
    <!-- Sidebar header (brand / logo area) -->
    <div class="flex items-center h-16 px-4 border-b border-gray-700">
      <i class="fas fa-shield-haltered text-indigo-400 text-2xl"></i>
      <span class="ml-2 text-lg font-semibold text-white tracking-tight">Security Labs</span>
    </div>

    <!-- Navigation links -->
    <nav class="flex-1 overflow-y-auto mt-4 px-2 space-y-1">
      <!-- Dashboard -->
      <a href="?page=home"
        class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition hover:bg-gray-800 hover:text-white">
        <i class="fas fa-tachometer-alt w-5 h-5 mr-3 text-indigo-400"></i>
        Dashboard
      </a>

      <!-- Labs (overview) -->
      <a href="?page=labs"
        class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition hover:bg-gray-800 hover:text-white">
        <i class="fas fa-flask w-5 h-5 mr-3 text-indigo-400"></i>
        All Labs
      </a>

      <!-- Divider -->
      <div class="pt-4 pb-2">
        <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Lab Modules</p>
      </div>

      <!-- XSS Lab -->
      <a href="?page=xss"
        class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition hover:bg-gray-800 hover:text-white">
        <i class="fas fa-code w-5 h-5 mr-3 text-yellow-400"></i>
        XSS Lab
      </a>

      <!-- SQLi Lab -->
      <a href="?page=sqli"
        class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition hover:bg-gray-800 hover:text-white">
        <i class="fas fa-database w-5 h-5 mr-3 text-red-400"></i>
        SQL Injection Lab
      </a>

      <!-- File Upload Lab -->
      <a href="?page=file_upload"
        class="flex items-center px-3 py-2 rounded-md text-sm font-medium transition hover:bg-gray-800 hover:text-white">
        <i class="fas fa-upload w-5 h-5 mr-3 text-green-400"></i>
        File Upload Lab
      </a>
    </nav>

    <!-- Sidebar footer (optional) -->
    <div class="px-3 py-4 border-t border-gray-700">
      <p class="text-xs text-gray-500">&copy; <?php echo date('Y'); ?> Internal Use Only</p>
    </div>
  </div>
</aside>

<!-- Mobile sidebar toggle (visible < lg) – simple script can be added if needed -->