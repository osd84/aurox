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

$result = $rules[0]->validate($data['email']);
$tester->assertEqual($result[0]['msg'],  'doit être une email valide', 'email + length : doit être une email valide');
$tester->assertEqual($result[1]['msg'],  'doit contenir minimum 20 caractères', 'email + length : doit contenir minimum 20 caractères');

// test notEmpty
$result = Validator::create('username')->notEmpty()->validate($data['username']);
$tester->assertEqual($result[0]['msg'], 'doit être rempli', 'notEmpty : doit être rempli');

// test length
$result = Validator::create('email')->length(min : 0, max: 10)->validate($data['email']);
$tester->assertEqual($result[0]['msg'], 'doit contenir maximum 10 caractères', 'length : doit contenir maximum 10 caractères');

$result = Validator::create('email')->length(min : 10, max: 0)->validate($data['email']);
$tester->assertEqual($result[0]['msg'], 'doit contenir minimum 10 caractères', 'length : doit contenir minimum 10 caractères');

$result = Validator::create('email')->length(min : 5, max: 10)->validate($data['email']);
$tester->assertEqual($result[0]['msg'], 'doit contenir entre 5 et 10 caractères', 'length :  doit contenir entre 5 et 10 caractères');

// test required

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

// Test de combinaison avec d'autres validateurs
$result = Validator::create('field')
    ->required()
    ->length(min: 5, max: 10)
    ->validate('abc');
$tester->assertEqual(count($result), 1, 'required + length : chaîne trop courte doit être invalide');
$tester->assertEqual($result[0]['msg'], 'doit contenir entre 5 et 10 caractères', 'required + length : message correct');



$tester->footer(exit: false);