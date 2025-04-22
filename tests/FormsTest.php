<?php

require_once '../aurox.php';

use OsdAurox\Forms;
use OsdAurox\I18n;
use PetitCitron\BrutalTestRunner\BrutalTestRunner;


$tester = new BrutalTestRunner();
$tester->header(__FILE__);

// Test de la méthode action
$elem = ['id' => 123];
$html = Forms::action($elem);
$tester->assertEqual(str_contains($html, '<a href="?action=edit&id=123"'), true, "action génère correctement le lien d'édition");
$tester->assertEqual(str_contains($html, '<a href="?action=detail&id=123"'), true, "action génère correctement le lien de détail");
$tester->assertEqual(str_contains($html, '<form action="?action=delete"'), true, "action génère correctement le formulaire de suppression");

// Test de la génération d'un champ select
$form = new Forms('/submit.php'); // Initialise un objet form
$list = [
    ['id' => 1, 'name' => 'Option 1'],
    ['id' => 2, 'name' => 'Option 2']
];
$htmlSelect = $form->select($list, 'testSelect', 'selectTestId');
$tester->assertEqual(str_contains($htmlSelect, '<select id="selectTestId" name="testSelect"'), true, "select génère correctement la balise <select>");
$tester->assertEqual(str_contains($htmlSelect, '<option value="1" class="" >Option 1</option>'), true, "select génère correctement l'option avec value 1");
$tester->assertEqual(str_contains($htmlSelect, '<option value="2" class="" >Option 2</option>'), true, "select génère correctement l'option avec value 2");

// Test de la génération d'un champ input
$htmlInput = $form->input('username', 'Nom utilisateur', 'inputUserId', 'text', 'Entrez votre nom');
$tester->assertEqual(str_contains($htmlInput, '<input type="text" id="inputUserId" name="username"'), true, "input génère correctement la balise <input>");
$tester->assertEqual(str_contains($htmlInput, 'placeholder="Entrez votre nom"'), true, "input inclut correctement l’attribut placeholder");
$tester->assertEqual(str_contains($htmlInput, '<label for="inputUserId"'), true, "input inclut correctement le label associé");

// Test de la méthode formStart
$htmlFormStart = $form->formStart('post', true, false);
$tester->assertEqual(str_contains($htmlFormStart, '<form action="/submit.php" method="post"'), true, "formStart génère correctement la balise <form>");
$tester->assertEqual(str_contains($htmlFormStart, 'enctype="multipart/form-data"'), true, "formStart inclut correctement l’attribut enctype pour les formulaires multipart");

// Test de la méthode formEnd
$htmlFormEnd = $form->formEnd();
$tester->assertEqual('</form></div>', $htmlFormEnd, "formEnd génère correctement la fermeture du formulaire");

// Test de la méthode checkbox
$htmlCheckbox = $form->checkbox('terms', 'Conditions d\'utilisation', 'checkboxId', 'form-check-input', true);
$tester->assertEqual(str_contains($htmlCheckbox, '<input type="checkbox" id="checkboxId" name="terms"'), true, "checkbox génère correctement la balise <input>");
$tester->assertEqual(str_contains($htmlCheckbox, 'checked'), true, "checkbox ajoute correctement l’attribut checked");
$tester->assertEqual(str_contains($htmlCheckbox, '<label class="form-check-label"'), true, "checkbox inclut correctement la balise label");

// Test de méthode submit avec un bouton AJAX
$form->ajax = true;
$htmlSubmitAjax = $form->submit('Envoyer');
$tester->assertEqual(str_contains($htmlSubmitAjax, '<a href="javascript:void(0)"'), true, "submit génère correctement un bouton pour soumission AJAX");
$tester->assertEqual(str_contains($htmlSubmitAjax, 'onclick="submitAjaxForm'), true, "submit inclut correctement la fonction JS de soumission AJAX");

$tester->footer(exit: false);