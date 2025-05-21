<?php

require_once '../aurox.php';

use OsdAurox\Validator;
use OsdAurox\I18n;
use osd84\BrutalTestRunner\BrutalTestRunner;


$tester = new BrutalTestRunner();
$tester->header(__FILE__);

// Test de la méthode addError et getErrors
$GLOBALS['i18n'] = new I18n('fr');

$rules = [
    Validator::create('email')->email()->length(20),
    Validator::create('username')->notEmpty(),
];
$data = [
    'email' => 'invalid-email',
    'username' => '',
];

$tester->header("Test de la méthode combiné");

$result = $rules[0]->validate($data['email']);
$tester->assertEqual($result[0]['msg'],  'doit être une email valide', 'email + length : doit être une email valide');
$tester->assertEqual($result[1]['msg'],  'doit contenir minimum 20 caractères', 'email + length : doit contenir minimum 20 caractères');

// test notEmpty
$tester->header("Test de la méthode notEmpty()");

$result = Validator::create('username')->notEmpty()->validate($data['username']);
$tester->assertEqual($result[0]['msg'], 'doit être rempli', 'notEmpty : doit être rempli');

$tester->header("Test de la méthode length()");
// test length
$result = Validator::create('email')->length(min : 0, max: 10)->validate($data['email']);
$tester->assertEqual($result[0]['msg'], 'doit contenir maximum 10 caractères', 'length : doit contenir maximum 10 caractères');

$result = Validator::create('email')->length(min : 10, max: 0)->validate($data['email']);
$tester->assertEqual($result[0]['msg'], 'doit contenir minimum 10 caractères', 'length : doit contenir minimum 10 caractères');

$result = Validator::create('email')->length(min : 5, max: 10)->validate($data['email']);
$tester->assertEqual($result[0]['msg'], 'doit contenir entre 5 et 10 caractères', 'length :  doit contenir entre 5 et 10 caractères');

// test required
$tester->header("Test de la méthode required()");

// Test avec une chaîne vide
$result = Validator::create('field')->required()->validate('');
$tester->assertEqual($result[0]['msg'], 'champ obligatoire', 'required : chaîne vide doit être invalide');
$tester->assertEqual($result[0]['valid'], false, 'required : chaîne vide doit retourner false');

// Test avec une chaîne contenant uniquement des espaces
$result = Validator::create('field')->required()->validate('   ');
$tester->assertEqual($result[0]['valid'], false, 'required : chaîne avec espaces doit être invalide');

// Test avec une chaîne valide
$result = Validator::create('field')->required()->validate('valeur');
$tester->assertEqual(count($result), 0, 'required : chaîne non-vide doit être valide');

// Test avec null
$result = Validator::create('field')->required()->validate(null);
$tester->assertEqual($result[0]['valid'], false, 'required : null doit être invalide');

// Test avec tableau vide
$result = Validator::create('field')->required()->validate([]);
$tester->assertEqual($result[0]['valid'], false, 'required : tableau vide doit être invalide');

// Test avec tableau non-vide
$result = Validator::create('field')->required()->validate(['item']);
$tester->assertEqual(count($result), 0, 'required : tableau non-vide doit être valide');

// Test avec nombre
$result = Validator::create('field')->required()->validate(0);
$tester->assertEqual(count($result), 0, 'required : nombre doit être valide');

// Test avec booléen
$result = Validator::create('field')->required()->validate(false);
$tester->assertEqual(count($result), 0, 'required : booléen doit être valide');


$tester->header("Test de la méthode stringType()");
$result = Validator::create('field')->stringType()->validate("test");
$tester->assertEqual(count($result), 0, 'stringType : chaîne valide doit passer');

$result = Validator::create('field')->stringType()->validate("");
$tester->assertEqual(count($result), 0, 'stringType : chaîne vide doit passer');

$result = Validator::create('field')->stringType()->validate(123);
$tester->assertEqual($result[0]['valid'], false, 'stringType : nombre doit échouer');

$result = Validator::create('field')->stringType()->validate(null);
$tester->assertEqual($result[0]['valid'], false, 'stringType : null doit échouer');

$result = Validator::create('field')->stringType()->validate([]);
$tester->assertEqual($result[0]['valid'], false, 'stringType : tableau doit échouer');

// Tests pour intType()
$tester->header("Test de la méthode intType()");

$result = Validator::create('field')->intType()->validate(123);
$tester->assertEqual(count($result), 0, 'intType : entier valide doit passer');

$result = Validator::create('field')->intType()->validate(0);
$tester->assertEqual(count($result), 0, 'intType : zéro doit passer');

$result = Validator::create('field')->intType()->validate(-123);
$tester->assertEqual(count($result), 0, 'intType : entier négatif doit passer');

$result = Validator::create('field')->intType()->validate("123");
$tester->assertEqual($result[0]['valid'], false, 'intType : chaîne numérique doit échouer');

$result = Validator::create('field')->intType()->validate(12.3);
$tester->assertEqual($result[0]['valid'], false, 'intType : float doit échouer');

// Tests pour floatType()
$tester->header("Test de la méthode floatType()");

$result = Validator::create('field')->floatType()->validate(12.3);
$tester->assertEqual(count($result), 0, 'floatType : float valide doit passer');

$result = Validator::create('field')->floatType()->validate(-12.3);
$tester->assertEqual(count($result), 0, 'floatType : float négatif doit passer');

$result = Validator::create('field')->floatType()->validate(123);
$tester->assertEqual($result[0]['valid'], false, 'floatType : entier doit échouer');

$result = Validator::create('field')->floatType()->validate("12.3");
$tester->assertEqual($result[0]['valid'], false, 'floatType : chaîne numérique doit échouer');

// Tests pour min()
$tester->header("Test de la méthode min()");

$result = Validator::create('field')->min(10)->validate(15);
$tester->assertEqual(count($result), 0, 'min : nombre supérieur doit passer');

$result = Validator::create('field')->min(10)->validate(10);
$tester->assertEqual(count($result), 0, 'min : nombre égal doit passer');

$result = Validator::create('field')->min(10)->validate(5);
$tester->assertEqual($result[0]['valid'], false, 'min : nombre inférieur doit échouer');

$result = Validator::create('field')->min(10.5)->validate(10.6);
$tester->assertEqual(count($result), 0, 'min : float supérieur doit passer');

$result = Validator::create('field')->min(10)->validate("abc");
$tester->assertEqual($result[0]['valid'], false, 'min : chaîne non numérique doit échouer');

// Tests pour max()
$tester->header("Test de la méthode max()");

$result = Validator::create('field')->max(10)->validate(5);
$tester->assertEqual(count($result), 0, 'max : nombre inférieur doit passer');

$result = Validator::create('field')->max(10)->validate(10);
$tester->assertEqual(count($result), 0, 'max : nombre égal doit passer');

$result = Validator::create('field')->max(10)->validate(15);
$tester->assertEqual($result[0]['valid'], false, 'max : nombre supérieur doit échouer');

$result = Validator::create('field')->max(10.5)->validate(10.4);
$tester->assertEqual(count($result), 0, 'max : float inférieur doit passer');

$result = Validator::create('field')->max(10)->validate("abc");
$tester->assertEqual($result[0]['valid'], false, 'max : chaîne non numérique doit échouer');

// Tests pour startWith()
$tester->header("Test de la méthode startWith()");

$result = Validator::create('field')->startWith('test')->validate("test123");
$tester->assertEqual(count($result), 0, 'startWith : correspondance exacte doit passer');

$result = Validator::create('field')->startWith('test')->validate("abc123");
$tester->assertEqual($result[0]['valid'], false, 'startWith : sans correspondance doit échouer');

$result = Validator::create('field')->startWith('Test', false)->validate("test123");
$tester->assertEqual(count($result), 0, 'startWith : insensible à la casse doit passer');

$result = Validator::create('field')->startWith('Test')->validate("test123");
$tester->assertEqual($result[0]['valid'], false, 'startWith : sensible à la casse doit échouer');

$result = Validator::create('field')->startWith('test')->validate(123);
$tester->assertEqual($result[0]['valid'], false, 'startWith : non-string doit échouer');

$result = Validator::create('field')->startWith('')->validate("test");
$tester->assertEqual(count($result), 0, 'startWith : préfixe vide doit passer');

// Tests combinés
$tester->header("Tests combinés");

$result = Validator::create('field')
    ->required()
    ->intType()
    ->min(0)
    ->max(100)
    ->validate(50);
$tester->assertEqual(count($result), 0, 'combinaison : valeur valide doit passer');

$result = Validator::create('field')
    ->required()
    ->stringType()
    ->startWith('test')
    ->validate("test123");
$tester->assertEqual(count($result), 0, 'combinaison : chaîne valide doit passer');


$tester->footer(exit: false);