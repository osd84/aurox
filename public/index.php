<?php


use OsdAurox\Base;
use OsdAurox\Modal;

$title = 'Aurox';

require_once '../aurox.php';


?>
<?php require('../templates/header.php'); ?>
<main class="py-4">
    <h1>Accueil</h1>
    <p>Contenu de la page</p>

    <h3>Detection des petits écran Base::isMobile()</h3>

    <?php if(Base::isMobile()): ?>
        <p>C'est un petit écran</p>
    <?php else: ?>
        <p>C'est un grand écran</p>
    <?php endif; ?>

    <h3>Gestionnaire minimaliste de Modal</h3>
    <?= Modal::newModal('Ma petite Modal', 'Contenu de la modal', 'info') ?>
    <div class="row">
        <div class="col-12">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-default">
                Modale classique #1
            </button>
        </div>
    </div>

    <?= Modal::newLoader(showClose: True) ?>
    <div class="row mt-2">
        <div class="col-12">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-loader">
                Modale de chargement #2
            </button>
        </div>
    </div>

    <?= Modal::newPrompt(showClose: True) ?>
    <div class="row mt-2">
        <div class="col-12">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-prompt">
                Modale de saisie #3
            </button>
        </div>
    </div>



</main>
<?php require('../templates/footer.php'); ?>
