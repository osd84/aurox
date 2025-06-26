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

$tester->header("Test hArrayKey()");
$result = Sec::hArrayKey([], 'name');
$tester->assertEqual($result, [], "Devrait retourner un tableau vide pour un tableau vide");
// Test avec clé inexistante
$data = [['age' => 25]];
$result = Sec::hArrayKey($data, 'name');
$tester->assertEqual($result, [], "Devrait retourner un tableau vide si la clé n'existe pas");
// Test avec données simples
$data = [
    ['name' => 'John'],
    ['name' => 'Jane'],
    ['name' => 'Bob']
];
$result = Sec::hArrayKey($data, 'name');
$tester->assertEqual($result, ['John', 'Jane', 'Bob'], "Devrait extraire correctement les valeurs");
// Test avec contenus HTML
$data = [
    ['content' => '<p>Hello</p>'],
    ['content' => '<script>alert("XSS")</script>'],
    ['content' => '<b>Bold</b>']
];
$result = Sec::hArrayKey($data, 'content');
$tester->assertEqual(
    in_array('<p>', $result),
    false,
    "Les balises HTML devraient être supprimées"
);
$tester->assertEqual(
    in_array('<script>', $result),
    false,
    "Les balises script devraient être supprimées"
);
// Test avec valeurs null ou vides
$data = [
    ['value' => null],
    ['value' => ''],
    ['value' => 'test']
];
$result = Sec::hArrayKey($data, 'value');
$tester->assertEqual(
    $result,
    ['', '', 'test'],
    "Les valeurs null et vides devraient être converties en chaînes vides"
);

// Test avec types mixtes
$data = [
    ['value' => 123],
    ['value' => true],
    ['value' => 'string'],
    ['value' => 3.14]
];
$result = Sec::hArrayKey($data, 'value');
$expected = ['123', '1', 'string', '3.14'];
$tester->assertEqual(
    $result,
    $expected,
    "Devrait gérer correctement différents types de données"
);


// Test tableau vide
$tester->header("Test de hArrayInt()");
$result = Sec::hArrayInt([], 'id');
$tester->assertEqual($result, [], "Devrait retourner un tableau vide pour un tableau vide");
// Test données valides
$data = [
    ['id' => '1'],
    ['id' => '42'],
    ['id' => '-5'],
    ['id' => '0']
];
$result = Sec::hArrayInt($data, 'id');
$tester->assertEqual($result, [1, 42, -5, 0], "Devrait convertir correctement les chaînes en entiers");
// Test avec valeurs non numériques
$data = [
    ['id' => 'abc'],
    ['id' => '12.34'],
    ['id' => 'null'],
    ['id' => '']
];
$result = Sec::hArrayInt($data, 'id');
$tester->assertEqual($result, [0, 12, 0, 0], "Devrait convertir les valeurs non numériques en 0");
// Test avec clé inexistante
$data = [
    ['autre' => '1'],
    ['autre' => '2']
];
$result = Sec::hArrayInt($data, 'id');
$tester->assertEqual($result, [], "Devrait retourner un tableau vide si la clé n'existe pas");
// Test avec valeurs mixtes
$data = [
    ['id' => '123'],
    ['id' => '<script>alert(456)</script>'],
    ['id' => ' 789 '],
    ['id' => true],
    ['id' => false]
];
$result = Sec::hArrayInt($data, 'id');
$tester->assertEqual($result, [123, 0, 789, 1, 0], "Devrait gérer correctement les valeurs mixtes");

$tester->header("Test storeReferer() && getReferer()");
unset($_SERVER['REQUEST_URI']);
unset($_SERVER['HTTP_HOST']);
Sec::storeReferer();
$tester->assertEqual(
    isset($_SESSION['previous_url']),
    false,
    "Ne devrait pas stocker d'URL si REQUEST_URI et HTTP_HOST sont manquants"
);
// Test avec REQUEST_URI défini mais HTTP_HOST manquant
$_SERVER['REQUEST_URI'] = '/test-page';
unset($_SERVER['HTTP_HOST']);
Sec::storeReferer();
$tester->assertEqual(
    isset($_SESSION['previous_url']),
    false,
    "Ne devrait pas stocker d'URL si HTTP_HOST est manquant"
);
// Test avec HTTP_HOST défini mais REQUEST_URI manquant
unset($_SERVER['REQUEST_URI']);
$_SERVER['HTTP_HOST'] = 'example.com';
Sec::storeReferer();
$tester->assertEqual(
    isset($_SESSION['previous_url']),
    false,
    "Ne devrait pas stocker d'URL si REQUEST_URI est manquant"
);

// ---- Test 2: storeReferer() avec des valeurs valides ----
// Configuration des valeurs valides
$_SERVER['REQUEST_URI'] = '/test-page';
$_SERVER['HTTP_HOST'] = 'example.com';
$beforeTimestamp = time();
Sec::storeReferer();
$afterTimestamp = time();
// Vérification du stockage correct
$tester->assertEqual(
    isset($_SESSION['previous_url']),
    true,
    "Devrait stocker l'URL dans la session"
);
$tester->assertEqual(
    $_SESSION['previous_url']['url'],
    '/test-page',
    "Devrait stocker l'URL correcte"
);
$tester->assertEqual(
    $_SESSION['previous_url']['host'],
    'example.com',
    "Devrait stocker le host correct"
);
$tester->assertEqual(
    $_SESSION['previous_url']['timestamp'] >= $beforeTimestamp &&
    $_SESSION['previous_url']['timestamp'] <= $afterTimestamp, true,
    "Devrait stocker un timestamp valide"
);
// ---- Test 3: getReferer() quand aucune URL n'est stockée ----
$_SESSION = []; // Réinitialisation de la session
$result = Sec::getReferer();
$tester->assertEqual(
    $result,
    null,
    "Devrait retourner null si aucune URL n'est stockée"
);

// ---- Test 4: getReferer() avec host correspondant ----
// Préparation de la session
$_SESSION['previous_url'] = [
    'url' => '/dashboard',
    'host' => 'example.com',
    'timestamp' => time()
];
$_SERVER['HTTP_HOST'] = 'example.com';
$result = Sec::getReferer();
$tester->assertEqual(
    $result,
    '/dashboard',
    "Devrait retourner l'URL stockée quand le host correspond"
);
$tester->assertEqual(
    isset($_SESSION['previous_url']),
    true,
    "La session ne devrait pas être supprimée après la récupération"
);
// ---- Test 5: getReferer() avec host différent ----
// Préparation de la session
$_SESSION['previous_url'] = [
    'url' => '/dashboard',
    'host' => 'example.com',
    'timestamp' => time()
];
$_SERVER['HTTP_HOST'] = 'autre-domaine.com';
$result = Sec::getReferer();
$tester->assertEqual(
    $result,
    null,
    "Devrait retourner null quand le host ne correspond pas"
);
$tester->assertEqual(
    isset($_SESSION['previous_url']),
    false,
    "La session devrait être supprimée quand le host ne correspond pas"
);

// ---- Test 6: getReferer() avec HTTP_HOST manquant ----
// Préparation de la session
$_SESSION['previous_url'] = [
    'url' => '/dashboard',
    'host' => 'example.com',
    'timestamp' => time()
];
unset($_SERVER['HTTP_HOST']);

$result = Sec::getReferer();
$tester->assertEqual(
    $result,
    null,
    "Devrait retourner null quand HTTP_HOST est manquant"
);
$tester->assertEqual(
    isset($_SESSION['previous_url']),
    false,
    "La session devrait être supprimée quand HTTP_HOST est manquant"
);

// ---- Test 7: Séquence complète storeReferer() puis getReferer() ----
// Stockage initial
$_SERVER['REQUEST_URI'] = '/profile';
$_SERVER['HTTP_HOST'] = 'app.example.com';
Sec::storeReferer();
// Récupération avec le même host
$result = Sec::getReferer();
$tester->assertEqual(
    $result,
    '/profile',
    "Devrait retourner l'URL correcte dans un scénario réel"
);

$tester->header("Test de uuidV4()");
// Test de validation du format d'UUID v4
$uuid = Sec::uuidV4();
$tester->assertEqual(
    preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $uuid),
    1,
    "L'UUID doit suivre le format standard UUID v4"
);
// Test d'unicité - génération de plusieurs UUIDs
$uuids = [];
for ($i = 0; $i < 100; $i++) {
    $uuids[] = Sec::uuidV4();
}
$uniqueUuids = array_unique($uuids);
$tester->assertEqual(
    count($uuids),
    count($uniqueUuids),
    "Tous les UUIDs générés doivent être uniques"
);

// Test de la version et de la variante
$uuid = Sec::uuidV4();
$hexParts = explode('-', $uuid);
$tester->assertEqual(
    substr($hexParts[2], 0, 1),
    '4',
    "Le premier caractère du troisième groupe doit être '4' (version 4)"
);
$tester->assertEqual(
    in_array(substr($hexParts[3], 0, 1), ['8', '9', 'a', 'b']),
    true,
    "Le premier caractère du quatrième groupe doit être 8, 9, a ou b (variante RFC 4122)"
);

// Test de longueur correcte
$tester->assertEqual(
    strlen($uuid),
    36,
    "L'UUID doit avoir une longueur de 36 caractères"
);

// Test que deux appels successifs renvoient des valeurs différentes
$uuid1 = Sec::uuidV4();
$uuid2 = Sec::uuidV4();
$tester->assertEqual(
    $uuid1 !== $uuid2,
    true,
    "Deux appels successifs doivent générer des UUIDs différents"
);

$tester->header("Test de la méthode getParam()");

// Sauvegarde des superglobales originales
$originalGet = $_GET;
$originalPost = $_POST;
$originalRequest = $_REQUEST;

// Test 1: Source GET (source=0)
$_GET = ['username' => 'jean-pierre123'];
$_POST = [];
$_REQUEST = [];

$result = Sec::getParam('username', 'alphaextra', 0);
$tester->assertEqual(
    $result,
    'jean-pierre',
    "Devrait récupérer et nettoyer depuis GET avec type alphaextra"
);

// Test 2: Source POST (source=1)
$_GET = [];
$_POST = ['userid' => '42abc'];
$_REQUEST = [];

$result = Sec::getParam('userid', 'int', 1);
$tester->assertEqual(
    $result,
    42,
    "Devrait récupérer et convertir en entier depuis POST"
);

// Test 3: Source POST puis GET (source=3, défaut)
$_GET = ['email' => 'test@example.com'];
$_POST = [];
$_REQUEST = [];

$result = Sec::getParam('email');
$tester->assertEqual(
    $result,
    'testexamplecom',
    "Devrait récupérer depuis GET et nettoyer avec alphaextra (par défaut) quand POST est vide"
);

// Test 4: Source POST puis GET (source=3), avec POST prioritaire
$_GET = ['name' => 'fallback'];
$_POST = ['name' => 'primary'];
$_REQUEST = [];

$result = Sec::getParam('name');
$tester->assertEqual(
    $result,
    'primary',
    "Devrait prioriser POST sur GET avec source=3"
);

// Test 5: Source REQUEST (source=2)
$_GET = [];
$_POST = [];
$_REQUEST = ['status' => '<b>Active</b>'];

$result = Sec::getParam('status', 'nohtml', 2);
$tester->assertEqual(
    $result,
    'Active',
    "Devrait récupérer depuis REQUEST et appliquer le nettoyage nohtml"
);

// Test 6: Paramètre inexistant
$_GET = [];
$_POST = [];
$_REQUEST = [];

$result = Sec::getParam('nonexistent');
$tester->assertEqual(
    $result,
    null,
    "Devrait retourner null pour un paramètre inexistant"
);

// Test 7: Type de nettoyage alpha
$_GET = ['text' => 'Hello123!'];
$_POST = [];
$_REQUEST = [];

$result = Sec::getParam('text', 'alpha', 0);
$tester->assertEqual(
    $result,
    'Hello',
    "Devrait ne garder que les caractères alphabétiques avec type alpha"
);

// Test 8: Type de nettoyage aZ09
$_GET = ['code' => 'Test-123!@'];
$_POST = [];
$_REQUEST = [];

$result = Sec::getParam('code', 'aZ09', 0);
$tester->assertEqual(
    $result,
    'Test123',
    "Devrait ne garder que les caractères alphanumériques avec type aZ09"
);

// Test 9: Type de nettoyage restricthtml
$_GET = ['html' => '<p>Text with <b>bold</b> and <script>alert("xss")</script>'];
$_POST = [];
$_REQUEST = [];

$result = Sec::getParam('html', 'restricthtml', 0);
$tester->assertEqual(
    $result,
    'Text with <b>bold</b> and alert("xss")',
    "Devrait conserver uniquement les balises HTML autorisées"
);

// Test 10: Traitement des tableaux
$_GET = ['items' => ['item1', 'item2']];
$_POST = [];
$_REQUEST = [];

$result = Sec::getParam('items', 'alpha', 0);
$tester->assertEqual(
    $result,
    ['item1', 'item2'],
    "Devrait préserver les tableaux sans modification"
);

// Restauration des superglobales originales
$_GET = $originalGet;
$_POST = $originalPost;
$_REQUEST = $originalRequest;

$tester->footer(exit: false);
