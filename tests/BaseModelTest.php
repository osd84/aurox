<?php


require_once '../aurox.php';

use App\PostsModel;
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

$tester->footer(exit: false);