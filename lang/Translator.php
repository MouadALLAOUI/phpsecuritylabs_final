<?php
/**
 * Multi-language translation system for PHP Security Labs
 */

class Translator
{
    private static ?Translator $instance = null;
    private array $translations = [];
    private string $currentLang = 'en';

    private function __construct()
    {
        $this->loadLanguage('en');
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function setLanguage(string $lang): void
    {
        $supportedLanguages = ['en', 'es', 'fr', 'de'];
        if (in_array($lang, $supportedLanguages)) {
            $this->currentLang = $lang;
            $this->loadLanguage($lang);
            // Store in session for persistence
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['language'] = $lang;
        }
    }

    public function getLanguage(): string
    {
        return $this->currentLang;
    }

    private function loadLanguage(string $lang): void
    {
        $filePath = __DIR__ . "/{$lang}.json";
        if (file_exists($filePath)) {
            $this->translations = json_decode(file_get_contents($filePath), true) ?? [];
        } else {
            $this->translations = [];
        }
    }

    public function trans(string $key, array $params = []): string
    {
        $translation = $this->translations[$key] ?? $key;
        
        // Replace parameters like {{name}} with actual values
        foreach ($params as $paramKey => $paramValue) {
            $translation = str_replace('{{' . $paramKey . '}}', $paramValue, $translation);
        }
        
        return $translation;
    }

    public function t(string $key, array $params = []): string
    {
        return $this->trans($key, $params);
    }

    public function getAvailableLanguages(): array
    {
        return [
            'en' => 'English',
            'es' => 'Español',
            'fr' => 'Français',
            'de' => 'Deutsch'
        ];
    }
}

// Helper function for quick translations
function __(string $key, array $params = []): string
{
    return Translator::getInstance()->trans($key, $params);
}
