<?php

require_once '../aurox.php';

use OsdAurox\I18n;
use PetitCitron\BrutalTestRunner\BrutalTestRunner;

$tester = new BrutalTestRunner();
$tester->header(__FILE__);

// Initialisation de l'instance globale pour I18n
$GLOBALS['i18n'] = new I18n('en');

// Test de la méthode getLocale()
$locale = $GLOBALS['i18n']->getLocale();
$tester->assertEqual($locale, 'en', "getLocale() retourne bien 'en' pour la locale par défaut");

// Test de la méthode setLocale() et de son effet
$GLOBALS['i18n']->setLocale('fr');
$newLocale = $GLOBALS['i18n']->getLocale();
$tester->assertEqual($newLocale, 'fr', "setLocale() modifie correctement la locale en 'fr'");

// Test de la méthode translate() avec une traduction existante
$translationKey = 'Welcome';
$translatedValue = $GLOBALS['i18n']->translate($translationKey);
$tester->assertEqual($translatedValue, 'Bienvenue', "translate() retourne bien 'Bienvenue' pour la clé 'welcome' dans la locale 'fr'");

// Test de la méthode translate() avec une traduction non existante
$unknownKey = 'nonexistent_key';
$unknownTranslation = $GLOBALS['i18n']->translate($unknownKey);
$tester->assertEqual($unknownTranslation, $unknownKey, "translate() retourne la clé elle-même si elle est introuvable");

// Test de la méthode translate() avec des placeholders
$placeholdersKey = 'hello_user';
$translatedWithPlaceholders = $GLOBALS['i18n']->translate($placeholdersKey, ['name' => 'Jean']);
$tester->assertEqual($translatedWithPlaceholders, 'Bonjour Jean', "translate() applique correctement les placeholders dans la traduction");

// Test de la méthode translate() avec l'option safe (désactivée par défaut)
$htmlKey = 'hello_user';
$htmlTranslation = $GLOBALS['i18n']->translate($htmlKey, ['name' => '<b>Jean</b>'], safe: true);
$tester->assertEqual($htmlTranslation, 'Bonjour <b>Jean</b>', "translate() retourne bien du HTML quand l'option safe est définie sur true");

// Test de la méthode translate() avec l'option safe désactivé (default)
$htmlKey = 'hello_user';
$htmlTranslationSafe = $GLOBALS['i18n']->translate($htmlKey,  ['name' => '<b>Jean</b>'], safe: false);
$expectedSafeOutput = 'Bonjour &lt;b&gt;Jean&lt;/b&gt;';
$tester->assertEqual($htmlTranslationSafe, $expectedSafeOutput, "translate() échappe correctement le HTML quand l'option safe est activée");

// Test de la méthode statique t()
$staticTranslation = I18n::t('Welcome');
$tester->assertEqual($staticTranslation, 'Bienvenue', "I18n::t() retourne la traduction correcte de la clé 'welcome'");

// Test de la méthode statique t() avec I18n non initialisé
$GLOBALS['i18n'] = null;
try {
    I18n::t('welcome');
    $tester->assertEqual(true, false, "I18n::t() devrait lancer une exception si I18n n'est pas initialisé");
} catch (\LogicException $e) {
    $tester->assertEqual(str_contains($e->getMessage(), 'I18n not initialized'), true, "I18n::t() lance correctement une exception si I18n est non initialisé");
}

$tester->footer(exit: false);