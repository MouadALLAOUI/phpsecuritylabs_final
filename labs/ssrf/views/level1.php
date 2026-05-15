<?php
/**
 * SSRF Lab - Level 1 View Template
 * Basic SSRF Challenge Interface
 */
?>

<div class="lab-container max-w-4xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-orange-500">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">
            <i class="fas fa-globe text-orange-500 mr-2"></i>
            Level 1: Basic SSRF
        </h2>
        
        <div class="mission-brief mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Mission Brief</h3>
            <p class="text-gray-600">
                This web application fetches remote URLs on your behalf. Your objective is to 
                exploit this functionality to access internal services that should not be publicly accessible.
            </p>
        </div>
        
        <div class="challenge-form mb-6">
            <form method="POST" action="">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="url">
                        Enter URL to fetch:
                    </label>
                    <input type="url" 
                           name="url" 
                           id="url" 
                           placeholder="http://example.com"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500"
                           required>
                </div>
                <button type="submit" class="bg-orange-600 text-white px-6 py-2 rounded-md hover:bg-orange-700 transition">
                    <i class="fas fa-play mr-2"></i>Fetch URL
                </button>
            </form>
        </div>
        
        <?php if (!empty($response)): ?>
        <div class="response-section mt-6">
            <h4 class="text-md font-semibold text-gray-800 mb-2">Response:</h4>
            <pre class="bg-gray-100 p-4 rounded overflow-auto text-sm"><?= htmlspecialchars($response) ?></pre>
        </div>
        <?php endif; ?>
        
        <div class="hint-section mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded">
            <h4 class="text-sm font-semibold text-yellow-800 mb-2">
                <i class="fas fa-lightbulb mr-1"></i> Hint
            </h4>
            <p class="text-sm text-yellow-700">
                Try accessing localhost or internal IP addresses. What services might be running internally?
            </p>
        </div>
    </div>
</div>
