<?php

/**
 * Landing Page View – Home / Dashboard
 * Loaded by the Router when ?page=home
 */

include_once ROOT . '/shared/header.php';
?>

<!-- Welcome Section -->
<div class="mb-8">
  <h1 class="text-3xl font-bold text-gray-900">
    <i class="fas fa-shield-haltered text-indigo-600 mr-2"></i>Defense Intelligence Dashboard
  </h1>
  <p class="mt-2 text-sm text-gray-600">
    Welcome back, <span class="font-medium text-gray-800">Agent</span>. Your security clearance is <span
      class="font-mono bg-green-100 text-green-800 px-2 py-0.5 rounded">LEVEL 4</span>.
  </p>
</div>

<!-- System Status Panel -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
  <!-- Card 1 -->
  <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-400">
    <div class="flex items-center">
      <i class="fas fa-server text-green-500 text-2xl mr-3"></i>
      <div>
        <p class="text-sm text-gray-500">System Status</p>
        <p class="text-lg font-semibold text-gray-900">All Systems Operational</p>
      </div>
    </div>
  </div>

  <!-- Card 2 -->
  <div class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-400">
    <div class="flex items-center">
      <i class="fas fa-shield-alt text-blue-500 text-2xl mr-3"></i>
      <div>
        <p class="text-sm text-gray-500">Active Labs</p>
        <p class="text-lg font-semibold text-gray-900">3 Modules Available</p>
      </div>
    </div>
  </div>

  <!-- Card 3 -->
  <div class="bg-white rounded-lg shadow p-5 border-l-4 border-yellow-400">
    <div class="flex items-center">
      <i class="fas fa-user-secret text-yellow-500 text-2xl mr-3"></i>
      <div>
        <p class="text-sm text-gray-500">Current Agent Level</p>
        <p class="text-lg font-semibold text-gray-900">Clearance: SECRET</p>
      </div>
    </div>
  </div>
</div>

<!-- Quick Access & Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <!-- Quick Access -->
  <div class="lg:col-span-1">
    <div class="bg-white rounded-lg shadow p-5">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">
        <i class="fas fa-bolt text-indigo-500 mr-2"></i>Quick Access
      </h2>
      <div class="space-y-3">
        <a href="?page=labs" class="flex items-center px-3 py-2 bg-gray-50 rounded-md hover:bg-gray-100 transition">
          <i class="fas fa-flask text-indigo-500 w-5 mr-2"></i>
          <span class="text-sm font-medium text-gray-700">Training Labs</span>
        </a>
        <a href="?page=xss" class="flex items-center px-3 py-2 bg-gray-50 rounded-md hover:bg-gray-100 transition">
          <i class="fas fa-code text-yellow-500 w-5 mr-2"></i>
          <span class="text-sm font-medium text-gray-700">XSS Exercises</span>
        </a>
        <a href="?page=sqli" class="flex items-center px-3 py-2 bg-gray-50 rounded-md hover:bg-gray-100 transition">
          <i class="fas fa-database text-red-500 w-5 mr-2"></i>
          <span class="text-sm font-medium text-gray-700">SQL Injection Lab</span>
        </a>
        <a href="?page=file_upload"
          class="flex items-center px-3 py-2 bg-gray-50 rounded-md hover:bg-gray-100 transition">
          <i class="fas fa-upload text-green-500 w-5 mr-2"></i>
          <span class="text-sm font-medium text-gray-700">File Upload Lab</span>
        </a>
      </div>
    </div>
  </div>

  <!-- Recent Activity -->
  <div class="lg:col-span-2">
    <div class="bg-white rounded-lg shadow p-5">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">
        <i class="fas fa-history text-gray-500 mr-2"></i>Recent Activity
      </h2>
      <table class="min-w-full text-sm">
        <thead>
          <tr class="text-left text-gray-500 border-b">
            <th class="py-2">Action</th>
            <th class="py-2">Lab</th>
            <th class="py-2">Time</th>
          </tr>
        </thead>
        <tbody class="text-gray-700">
          <tr class="border-b">
            <td class="py-2"><i class="fas fa-check-circle text-green-500 mr-2"></i>Challenge Completed</td>
            <td>XSS Lab (Level 1)</td>
            <td>10 min ago</td>
          </tr>
          <tr class="border-b">
            <td class="py-2"><i class="fas fa-play-circle text-blue-500 mr-2"></i>Lab Started</td>
            <td>SQL Injection</td>
            <td>1 hour ago</td>
          </tr>
          <tr class="border-b">
            <td class="py-2"><i class="fas fa-file-upload text-yellow-500 mr-2"></i>Report Submitted</td>
            <td>File Upload</td>
            <td>3 hours ago</td>
          </tr>
          <tr>
            <td class="py-2"><i class="fas fa-sign-in-alt text-gray-500 mr-2"></i>Login</td>
            <td>System</td>
            <td>5 hours ago</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php
include_once ROOT . '/shared/footer.php';
?>