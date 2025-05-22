<?php

require_once '../aurox.php';

use OsdAurox\Sec;
use osd84\BrutalTestRunner\BrutalTestRunner;

$tester = new BrutalTestRunner();
$tester->header(__FILE__);


$r = Sec::isLoggedBool();
$tester->assertEqual(false, $r, "pas connecté");

$_SESSION['user'] = ['id' => 1, 'role' => 'user']; // mock d'un user connecté

$r = Sec::isLoggedBool();
$tester->assertEqual(true, $r, "connecté");

$r = Sec::isAdminBool();
$tester->assertEqual(false, $r, "pas admin");

$_SESSION['user']['role'] = 'admin';
$r = Sec::isAdminBool();
$tester->assertEqual(true, $r, "admin");


$tester->header("Test de la méthode getUserIdOrDie()");
// Test sans session utilisateur
$_SESSION = [];
try {
    Sec::getUserIdOrDie();
    $tester->assertEqual(true, false, "Doit lever une exception si pas de session user");
} catch (\Exception $e) {
    $tester->assertEqual($e->getMessage(), 'User not logged', "Message d'erreur correct pour absence de session");
}
// Test avec session utilisateur mais sans ID
$_SESSION['user'] = ['role' => 'user'];
try {
    Sec::getUserIdOrDie();
    $tester->assertEqual(true, false, "Doit lever une exception si pas d'ID utilisateur");
} catch (\Exception $e) {
    $tester->assertEqual($e->getMessage(), 'User not logged', "Message d'erreur correct pour absence d'ID");
}
// Test avec ID utilisateur valide
$_SESSION['user'] = ['id' => 42, 'role' => 'user'];
$userId = Sec::getUserIdOrDie();
$tester->assertEqual(42, $userId, "Doit retourner l'ID utilisateur correct");
// Test avec ID utilisateur sous forme de chaîne (doit être converti en entier)
$_SESSION['user'] = ['id' => '123', 'role' => 'user'];
$userId = Sec::getUserIdOrDie();
$tester->assertEqual(123, $userId, "Doit convertir l'ID en entier");
