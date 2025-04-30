<?php

require_once '../aurox.php';

use OsdAurox\Sec;
use PetitCitron\BrutalTestRunner\BrutalTestRunner;

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