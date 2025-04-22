<?php

namespace  OsdAurox;

class I18n
{
    private $translations = [];
    private $locale;

    public function __construct($locale = 'en')
    {
        $this->locale = $locale;
        $this->loadTranslations();
    }

    private function loadTranslations(): bool
    {
        $this->translations = [];
        $filePath = APP_ROOT . '/translations/' . $this->locale . '.php';
        if (file_exists($filePath)) {
            $this->translations = include $filePath;
            return true;
        }
        return false;

    }

    public function translate(string $key, array $placeholders = [], bool $safe = false)
    {
        $translation = $this->translations[$key] ?? $key;
        foreach ($placeholders as $placeholder => $value) {
            $translation = str_replace('{' . $placeholder . '}', $value, $translation);
        }
        if (!$safe) {
            $translation = htmlspecialchars($translation, ENT_QUOTES, 'UTF-8');
        }
        return $translation;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
        $this->loadTranslations();
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public static function t(string $key, array $placeholders = [], bool $safe = false): string|null
    {
        $translator = $GLOBALS['i18n'];
        if (!$translator) {
            throw new \LogicException('Out context; I18n not initialized');
        }
        return $translator->translate(key : $key, placeholders: $placeholders, safe : $safe);
    }
}
?>