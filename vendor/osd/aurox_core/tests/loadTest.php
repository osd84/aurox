<?php

use OsdAurox\Dict;

require_once '../vendor/autoload.php';


$arr = ['key' => 'value'];
$val = Dict::get($arr, 'key');

if ($val === 'value') {
    echo "OK";
} else {
    echo "KO";
}