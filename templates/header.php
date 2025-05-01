<?php

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) {
    die('Illegal request');
}

use App\AppUrls;
use OsdAurox\Sec;

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?=  Sec::hNoHtml($title ?? "Aurox - BDD") ?></title>
    <!-- Ajoutez vos feuilles de style -->
    <link rel="stylesheet" href="/style.css">
</head>
<body>
<header>
    <h1><?= Sec::hNoHtml($headerTitle ?? "Bienvenue sur mon site"); ?></h1>
    <nav>
        <ul>
            <li><a href="<?= Sec::hNoHtml(AppUrls::HOME) ?>">Accueil</a></li>
            <li><a href="<?= Sec::hNoHtml(AppUrls::NOT_FOUND) ?>">404</a></li>
        </ul>
    </nav>
</header>