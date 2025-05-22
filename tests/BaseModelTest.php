<?php


require_once '../aurox.php';

use App\PostsModel;
use OsdAurox\BaseModel;
use OsdAurox\Dbo;
use osd84\BrutalTestRunner\BrutalTestRunner;

$tester = new BrutalTestRunner();
$tester->header(__FILE__);

$pdo = Dbo::getPdo();

$instancePost = new PostsModel();
$tester->assertEqual($instancePost->getTable(), 'posts', 'getTable() ok');

$post = PostsModel::get($pdo, 1);
$tester->assertEqual($post['id'], 1, 'get() ok');

$post = PostsModel::getBy($pdo, 'title', 'title1');
$tester->assertEqual($post['id'], 1, 'getBy() ok');

$posts = PostsModel::getAllBy($pdo, 'status', 'draft');
$tester->assertEqual(count($posts), 2, 'getAllBy() ok');

$count = PostsModel::count($pdo);
$tester->assertEqual($count, 2, 'count() ok');

$uniq = PostsModel::check_uniq($pdo, 'title', 'title1');
$tester->assertEqual($uniq, false, 'check_uniq() ok');
$uniq = PostsModel::check_uniq($pdo, 'title', 'titleFake');
$tester->assertEqual($uniq, true, 'check_uniq() ok');

$jsonAgg = '[{"id": 3, "name": "Elem1", "name_translated": "Elem1Fr"}, {"id": 4, "name": "Elem2", "name_translated": "Elem2Fr"}]';
$entity = ['keyJson' => $jsonAgg, 'keyNone' => null, 'keyNoJson' => 'noJson'];
$r = BaseModel::jsonArrayAggDecode($entity, 'keyJson');
$tester->assertEqual(array_column($r, 'name'), ['Elem1', 'Elem2'], 'jsonArrayAggDecode() keyJson ok');

$r = BaseModel::jsonArrayAggDecode($entity, 'keyNone');
$tester->assertEqual($r, [], 'jsonArrayAggDecode() keyNone ok');

$r = BaseModel::jsonArrayAggDecode($entity, 'keyNoJson');
$tester->assertEqual($r, [], 'jsonArrayAggDecode() keyNoJson ok');

$r = BaseModel::jsonArrayAggDecode($entity, 'keyNoJson', default: ['my', 'default']);
$tester->assertEqual($r, ['my', 'default'], 'jsonArrayAggDecode() keyNoJson + default ok');


// Tests pour idsExistsOrEmpty
$tester->header("Test de la méthode idsExistsOrEmpty()");

$result = BaseModel::idsExistsOrEmpty($pdo, 'posts', []);
$tester->assertEqual($result, true, 'idsExistsOrEmpty : tableau vide doit retourner true');
// Test avec ID existant
$result = BaseModel::idsExistsOrEmpty($pdo, 'posts', [1]);
$tester->assertEqual($result, true, 'idsExistsOrEmpty : ID existant doit retourner true');
// Test avec plusieurs IDs existants
$result = BaseModel::idsExistsOrEmpty($pdo, 'posts', [1, 2]);
$tester->assertEqual($result, true, 'idsExistsOrEmpty : plusieurs IDs existants doivent retourner true');
// Test avec ID inexistant
$result = BaseModel::idsExistsOrEmpty($pdo, 'posts', [999]);
$tester->assertEqual($result, false, 'idsExistsOrEmpty : ID inexistant doit retourner false');
// Test avec ID invalide
try {
    BaseModel::idsExistsOrEmpty($pdo, 'posts', ['abc']);
    $tester->assertEqual(0,1 ,'idsExistsOrEmpty : ID non numérique doit lever une exception');
} catch (InvalidArgumentException $e) {
    $tester->assertEqual(1, 1,'idsExistsOrEmpty : ID non numérique lève bien une exception');
}

// Tests pour getByIds
$tester->header("Test de la méthode getByIds()");
// Test avec tableau vide
$result = BaseModel::getByIds($pdo, 'posts', []);
$tester->assertEqual($result, [], 'getByIds : tableau vide doit retourner tableau vide');

// Test avec un seul ID
$result = BaseModel::getByIds($pdo, 'posts', [1]);
$tester->assertEqual(count($result), 1, 'getByIds : un ID doit retourner un résultat');
$tester->assertEqual($result[0]['id'], 1, 'getByIds : ID correct retourné');
$tester->assertEqual($result[0]['title'], 'title1', 'getByIds : données correctes retournées');

// Test avec plusieurs IDs
$result = BaseModel::getByIds($pdo, 'posts', [1, 2]);
$tester->assertEqual(count($result), 2, 'getByIds : deux IDs doivent retourner deux résultats');

// Test avec ID inexistant
$result = BaseModel::getByIds($pdo, 'posts', [999]);
$tester->assertEqual($result, [], 'getByIds : ID inexistant doit retourner tableau vide');

// Test avec ID invalide
try {
    BaseModel::getByIds($pdo, 'posts', ['abc']);
    $tester->assertEqual(0,1 ,'getByIds : ID non numérique doit lever une exception');
} catch (InvalidArgumentException $e) {
    $tester->assertEqual(1,1 ,'getByIds : ID non numérique lève bien une exception');
}
// Test avec select spécifique
$result = BaseModel::getByIds($pdo, 'posts', [1], 'title');
$tester->assertEqual(in_array('title', array_keys($result[0])), true, 'getByIds : select spécifique retourne uniquement les colonnes demandées');
$tester->assertEqual(in_array('id', array_keys($result[0])), false, 'getByIds : select spécifique retourne uniquement les colonnes demandées');



// Tests pour exist
$tester->header("Test de la méthode exist()");

// Test avec ID existant
$result = PostsModel::exist($pdo, 1);
$tester->assertEqual($result, true, 'exist : ID existant doit retourner true');
// Test avec ID inexistant
$result = PostsModel::exist($pdo, 999);
$tester->assertEqual($result, false, 'exist : ID inexistant doit retourner false');
// Test avec ID zéro (généralement invalide)
$result = PostsModel::exist($pdo, 0);
$tester->assertEqual($result, false, 'exist : ID zéro doit retourner false');
// Test avec valeur non numérique (qui sera convertie en entier)
$result = PostsModel::exist($pdo, "abc");
$tester->assertEqual($result, false, 'exist : ID non numérique converti en 0 doit retourner false');
// Test avec valeur numérique sous forme de chaîne
$result = PostsModel::exist($pdo, "1");
$tester->assertEqual($result, true, 'exist : ID numérique sous forme de chaîne doit être converti et retourner true');


$tester->footer(exit: false);