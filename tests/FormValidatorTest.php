<?php

require_once '../aurox.php';

use OsdAurox\FormValidator;
use osd84\BrutalTestRunner\BrutalTestRunner;
use Respect\Validation\Validator as v;


$tester = new BrutalTestRunner();
$tester->header(__FILE__);

// Test de la méthode addError et getErrors
$validator = new FormValidator();
$validator->addError('email', 'Email invalide');
$validator->addError('username', 'Nom d\'utilisateur requis');
$tester->assertEqual(str_contains(json_encode($validator->getErrors()), 'Email invalide'), true, "Ajout et récupération des erreurs fonctionne correctement pour 'email'");
$tester->assertEqual(str_contains(json_encode($validator->getErrors()), 'Nom d\'utilisateur requis'), true, "Ajout et récupération des erreurs fonctionne correctement pour 'username'");

// Test de la méthode hasError
$tester->assertEqual($validator->hasError('email'), true, "La méthode hasError retourne true pour 'email' avec des erreurs");
$tester->assertEqual($validator->hasError('password'), false, "La méthode hasError retourne false pour un champ sans erreur");

// Test de la méthode popError
$errorsEmail = $validator->popError('email');
$tester->assertEqual(is_array($errorsEmail), true, "La méthode popError retourne un tableau d'erreurs");
$tester->assertEqual(str_contains(json_encode($errorsEmail), 'Email invalide'), true, "popError retourne correctement les erreurs du champ 'email'");
$tester->assertEqual($validator->hasError('email'), false, "Le champ 'email' n'a plus d'erreur après l'appel à popError");

// Test de la méthode validate avec respect/validation
$rules = [
    'email' => v::email(),
    'username' => v::notEmpty(),
];
$data = [
    'email' => 'invalid-email',
    'username' => '',
];
$result = $validator->validate($data, $rules);
$tester->assertEqual($result, false, "La validation échoue lorsque les données ne respectent pas les règles");

$errors = $validator->getErrors();
$tester->assertEqual(str_contains($errors['email'][0], 'Email must be valid email'), true, "La validation retourne une erreur pour un email non valide");
$tester->assertEqual(str_contains($errors['username'][1], 'Username must not be empty'), true, "La validation retourne une erreur pour un champ vide");

// Test de la méthode isValid avant validation
$validator2 = new FormValidator();
try {
    $validator2->isValid();
} catch (Exception $e) {
    $tester->assertEqual(str_contains($e->getMessage(), 'You must call validate() method before calling isValid()'), true, "La méthode isValid déclenche une exception si appelée sans validation");
}

// Test de genApiResult
$apiResult = $validator->genApiResult();
$tester->assertEqual(get_class($apiResult), OsdAurox\Api::class, "La méthode genApiResult retourne bien une instance d'Api");
$tester->assertEqual(str_contains(json_encode($apiResult), 'Please correct the following errors'), true, "La méthode genApiResult inclut un message global d'erreur");
$apiResultArr = json_decode(json_encode($apiResult), true);
$tester->assertEqual(str_contains($apiResultArr['validators'][2]['msg'], 'must be valid email'), true, "genApiResult inclut correctement les erreurs de validation pour 'email'");
$tester->assertEqual(str_contains($apiResultArr['validators'][1]['msg'], 'must not be empty'), true, "genApiResult inclut correctement les erreurs de validation pour 'username'");

$tester->footer(exit: false);