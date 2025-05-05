
# Aurox

Une collection d'utilitaires écrits en PHP
Aurox suit l'idée du Brutalisme et du Brutalism Dev Design.

C'est une **ALPHA** beaucoup de choses vont changer, ne pas utiliser en prod. 😉️

Nécessite un serveur web Apache2 et PHP >= 8.1

LICENCE MIT - Projet en cours - Ne pas utiliser en production

## A propos

Aurox peut être utilisée comme lib utilitaire via composer ou comme moteur d'application web. <br>
Ce depôt contient un kit de démarrage et un exemple d'utilisation dans [/public](public). <br>
<br>
Ce dépôt est en phase de R&D, beaucoup de chose sont seulement prototypées en attente de tests et benchmarks.<br>
Il manque de la documentation à tous les niveaux, mais le projet avance bien et va rapidement gagner en maturité.<br>
<br>
L'objectif est de rester très simple, ce n'est pas un framework.


![screen.png](public/img/screen.png)


## Utilisez comme lib

Vous pouvez utiliser se repo, comme collection de lib.
C'est son objectif initial.

```
composer require osd/aurox
```

Puis

```
require_once __DIR__ . '/vendor/autoload.php';
```


## Utiliser comme start app

Vous pouvez aussi utiliser se repo, comme squelette d'application de démarrage.
Il suffit de télécharger la dernière version en .zip, le dossier /public contient une start app.

Téléchargez la dernière release du projet : https://github.com/PetitCitron/aurox/releases/latest

Dans la racine lancer

```
composer install
composer dump-autoload         
```

Copier [conf_example.php](conf_example.php) et renommer en `conf.php` en modifiant et adaptant son contenu à votre projet.

Décompressez-le .zip à la racine de votre dossier web en exposant uniquement le dossier **public/** dans votre conf Apache2.

```
DocumentRoot /var/www/<my_project>/public/
```


## Usage & Attention

Copiez et modifiez le fichier   `conf.php`[conf_example.php](conf_example.php) en `conf.php` à la racine pour configurer votre application. <br>
Mettez vos routes dans [app/AppUrls.php](app/AppUrls.php) <br>
Mettez vos modèles dans [app/Models](app/Models) <br>

⚠️ important Faites pointer le root de votre configuration Apache2 sur /public/ <br>
Ne **jamais** exposer la racine directement sur le web. <br>
Incluez [aurox.php](aurox.php) dans tous vos fichiers .php.

Exemple dans [index.php](public/index.php)

Créer des fichiers templates, controllers où tout ce que vous voulez, comme vous le souhaitez. <br>
Aurox n'est pas un framework, c'est juste un moteur avec de petits utilitaires. <br>
<br>
Il n'y a aucun système de routing ou de sécurité inclus. <br>
C'est à vous de gérer, [BaseModel.php](src/OsdAurox/BaseModel.php) n'est pas un ORM.<br>
Les arguments `$field` des méthodes de `BaseModel` sont **vulnérables aux injections SQL**. <br>
N'y passez jamais de variables, le reste est normalement sécurisé via PDO.<br>
<br>
L'utilisation de cette lib est à vos risques et périls, elle est distribuée sous licence MIT. <br>
Sans aucune garantie.


La Doc est encore en ébauche, on fera mieux plus tard.

# Globals variables

Activer le mode debug dans : `conf.php`[conf.php](conf.php)


```php
'debug' => true
```


## API

Permet de retourner des réponses JSON dans un format standardisé compris par toute l'application, y compris par l'interface Front-end.
Exemple d'utilisation :

```php
$res = new Api();
$res->status = true;  // status de la réponse
$res->success[] = I18n::t('User updated');
$res->redirect_url = AppUrls::ADMIN_USERS;
$res->returnJsonResponse();
Base::dieOrThrow();
```

Attributs spéciaux

```php
$res->status : `true` ou `false`.

$res->errors
$res->warnings
$res->infos
$res->success : Messages affichés sous forme de "toast" en JS.

$res->data : Contient les données à transmettre.
$res->html : Si le rendu HTML est fait côté Back-end.
$res->validators : Format spécifique pour la validation des formulaires côté JS côté Front-end.
```


## Cache

Le cache est stocké dans sous forme de fichiers plats. `/cache_system_h€re`


```php
$cache = new Cache();
$cache_key = "BLOG_CONTACT_MAIL:{$ip}";

// lire le cache
if ($cache->get($cache_key)) {
    // On fait semblant
    Flash::success("Votre message a bien été envoyé.");
    $pass = false;
}

// écrire dans le cache
$cache->set($cache_key, true, 120); // 120 secondes de timeout

// delete dans le cache
$cache->delete($key)

// supprimer tous le cache
$cache->clear()
```

## Csrf

```php
// protéger une view
$csrf = Csrf::protect();

// écrire le token dans un form html
<?= Csrf::inputHtml(); ?>
```

## Base


```php
Base::isMobile()   // retourne bool vrai si tablette ou mobile
Base::dieOrThrow()   // termine l'exect du script via die() ou Throw exeception en cas de test unitaire
Base:asSelectList($array, $value_field = 'name', $key_field = 'id') // retourne un tableau ['id' => , 'name' => ]
Base:redirect($url) // redirect comme il faut
```


## BaseModel

```php
// SELECT JSON_ARRAYAGG( JSON_OBJECT( 'id', wg.id, 'name', wg.name, 'name_translated', COALESCE(NULLIF(wg.name_$locale, ''), wg.name, '') )
// as myKey
$array = BaseModel::jsonArrayAggDecode($wine, 'myKey');  // Raccourcis pour extraire un JSON_ARRAYAGG ou [ ] si erreur; d'un résultat Array PDO
>>> [ [ 'id' => 1, 'name' => 'foo', 'name_translated' => 'bar'], ... ]
```

## LOG

Écris les logs dans /logs/

```php
Log::info()
Log::debug()
Log::warning()
Log::error()
```

## Dict

Utilitaire et Alias pour les dictionnaires

### Dict::get(\$array, \$key, \$default == null)

Récupérer la valeur d'une clef ou defaut, null.

```php
$tab = ['couleur' => 'bleu', 'prix' => 99]
Dict::get($tab, 'couleur')
>>> 'bleu'
Dict::get($tab, 'clef_existe_pas')
>>> null
Dict::get($tab, 'clef_existe_pas', 'defaut_vert')
>>> 'defaut_vert'
```

### Dict:Base::isInArrayAndRevelant()

Retourne vrai si la clef existe Et qu'elle est pertinante.
Ignore ces valeurs: `['null', 'undefined', 'None', '', '0', ' ']`

```php
$tab = ['ok' => '1', 'ko' => 'null']
Dict::isInArrayAndRevelant($tab, 'ok')
>>> true
Dict::isInArrayAndRevelant($tab, 'ko')
>>> false
Dict::isInArrayAndRevelant($tab, 'clef_existe_pas')
>>> false
```


## Discord

### Discord configuration

Ajouter le webhook dans [conf.php](../../AppConfig.php)

```php
'discordWebhook' => 'https://discord.com/api/webhooks/{key}';
```

### Discord::send(\$message)

Envoyer un message sur un chan discord via webhook

```php
Discord::send($message);
```

## ErrorMonitoring

Permet d'alerter sur discord en PROD
Si des fatal errors arrivent

```php
ErrorMonitoring::initialize();
```

## Filter

Utilitaire pour les templates PHP

```php
<?= Filter::truncate($text, $length, $ending= '...') ?>
>>> Mon super texte ...

<?= Filter::dateFr($date) ?>
>>> d/m/Y

<?= Filter::dateMonthFr($date) ?>
>>> Avril 2025

<?= Filter::dateUs($date) ?>
>>> Y-m-d

<?= Filter::toDayDateUs($date) ?>
>>> Y-m-d // (date du jour direct)
```

## Flash

Injecte les messages par catégorie dans  $_SESSION['messages']

```php
Flash::error()
Flash::success()
Flash::info()
Flash::warning()
Flash::add()
Flash::get($clear=false) // peut récupérer tous les messages et les effacer de $_SESSION 
```

## Fmt

Permet de changer l'affichage de certain champs dans les tempaltes / formulaire

```php
Fmt::bool($field)  
>>> Yes / No, Oui / non (I18n)
```

## Forms

Génération auto de formulaire BS

todo

## FormsFilter

Query builder pour côté Admin

todo

## FormValidator

Système mixte pour valider des formulaires et afficher des erreurs

todo

## I18n

Traduire avec des fichiers JSON

FIchier dans : [fr.php](translations/fr.php)

```php
// intialisation du traducteur dans le scope 
$GLOBALS['i18n'] = new I18n('en');

// support des traductions classique via /translations
I18n::t('English')
>>> Anglais

// support des traductions bdd via des array
$entity = [
    'name' => 'default',
    'name_en' => 'trad_en',
    'name_fr' => 'trad_fr',
    'name_it' => 'trad_it',
];
$r = I18n::entity($entity);
>>> 'trad_en'


// locale actuelle
$locale = I18n::currentLocale();
>>> 'fr'
```

## Mailer

Envoyer des mails via PHP

```php
$mail_sent = Mailer::send(to: $mail_to, subject: $mail_subject, content: $html_content);
```

## Paginator

todo

## Sec

```php

use OsdAurox\Sec

Sec::isPost() // true si POST
Sec:getAction()  // lit $_GET['action'] et standardise sa lecture sécurisée
Sec::jsonDatas()   // Retourne une request JSON en tableau

Sec::getRealIpAddr()  // retourne vrai adresse ip du src request

Sec::h($string) // alias htmlspecialchars
Sec::hNoHtml($string) // alias htmlspecialchars

Sec::safeForLikeStrong($string)   // sécurise fortement un string pour son utilisation en LIKE SQL
Sec::safeForLike($string)   // sécurise légerement un string pour son utilisation en LIKE SQL

Sec::isAdminOrDie($flash = true, $redirect = true)    // regarde le $_SESSION['user']['role']
Sec::isAdminBool()    // regarde le $_SESSION['user']['role'] == 'admin'
Sec::isRoleOrDie($role, $flash = true, $redirect = true)
Sec::isRoleBool($role)  // $role == 'user' , regarde si $_SESSION['user']['role'] == $role et retourne true / false
Sec::isLogged($flash = true, $redirect = true)
Sec::isLoggedBool()  // retoune true ou false si utilisateur connecté

Sec::noneCsp() // retourne le NONCE Csp courant (typo)

Sec::getPage() // méthode securisée pour lire le $_GET['page']
Sec::getPerPage() // méthode securisée pour lire le $_GET['per_page']

```

## ViewsShortcuts

Vue complete en tant que méthode

```php
ViewsShortCuts::ListThisDirView($dir)
```

## Ban - Waf
```php

// ban system
Ban::blockBlackListed();
Ban::checkRequest();

# La liste des words sensibles dans les urls est ici 
Ban->$black_list_words

# Le waf de base s'utilise comme ça
Ban::blockBlackListed(); # 1 on regarde si l'ip est déjà bannie
Ban::checkRequest(); # 2 on regarde si la requete, son url actuelle mérite en ban

# Ban directement
Ban:ban()  # recherche l'ip réelle de la requête actuelle et la ban directement

# Ban sur detection de motif suspect en GET & POST
$r = Ban::banIfHackAttempt();
if($r) {
    Discord::send('[BAN] Hack attempt detected on ' . Sec::hNoHtml(AppConfig::get('appName')) . ' by ' . Sec::hNoHtml(Sec::getRealIpAddr()));
}

```


# urls
```php
AppUrls::HOME;
AppUrls::LOGIN;
```

# Utils protection view
```php
$nonce_csp
```


## Forms

TODO

```php
$form = new Forms($action_url,
                    validator: $validator,
                    entity: $user ?? null,
                    ajax: isset($user));
<?= $form->formStart(autocomplete: false) ?>
<?= $form->input('email', type: 'email', required: true) ?>
<?= if($use ?? null) ? $form->input('password', type: 'password', placeholder: 'Mot de passe', required: true) : '' ?>
<?= $form->select2Ajax(
    ajax_url: AppUrls::ADMIN_COMPANIES . '?action=select2',
    name: 'id_company',
    id: 'id_company',
    label: 'Company',
    selected: $user['id_company'] ?? null,
)
?>
<?= $form->select2($l_users_types, 'id_user_type', selected: $user['type'] ?? 3) ?>
<?= $form->select($l_roles, 'role', value_field: 'value', name_field: 'label', selected: $user['role'] ?? 'user') ?>
<?= $form->checkbox('active', checked: $user['active'] ?? true) ?>
<?= $form->input('country') ?>
<?= $form->submit(I18n::t('Save')) ?>
<?= $form->formEnd() ?>
<?php if ($user ?? null): ?>
    <?= $form->ajaxSubmit() ?>
<?php endif; ?>


```

## TESTS

Create a mysql database `aurox_test`
Restore the dump `aurox_test.sql`

```sh
cd tests && php run.php
```

Quiet mode 
```sh
php run.php | grep  'fails' | grep -v '0 fails'
```
