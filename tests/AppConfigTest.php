<?php

require_once '../aurox.php';


use OsdAurox\AppConfig;
use PetitCitron\BrutalTestRunner\BrutalTestRunner;

$tester = new BrutalTestRunner();
$tester->header(__FILE__);


$tester->assertEqual( AppConfig::get('appName'), 'OsdAurox', 'AppConfig ok');

$instance = AppConfig::getInstance();
$tester->assertEqual( $instance->appName, 'OsdAurox', 'AppConfig singleton ok');

$tester->footer(exit: false);