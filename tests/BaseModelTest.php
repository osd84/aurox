<?php


require_once '../aurox.php';

use App\PostsModel;
use OsdAurox\BaseModel;
use OsdAurox\Dbo;
use PetitCitron\BrutalTestRunner\BrutalTestRunner;

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

$tester->footer(exit: false);