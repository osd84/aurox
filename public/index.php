<?php


use OsdAurox\Base;
use OsdAurox\Modal;

$title = 'Aurox';

require_once '../aurox.php';


?>
<?php require('../templates/header.php'); ?>
<main class="main-content">
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
                Modal #1
            </button>
        </div>
    </div>

    <?= Modal::newLoader(msg : 'Va se fermer dans 1 secondes') ?>
    <div class="row mt-2">
        <div class="col-12">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#modal-loader"
                    onclick="setTimeout(() => {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('modal-loader'));
                            modal.hide();
                    }, 1000)"
                >
                Modal #2
            </button>
        </div>
    </div>



</main>
<?php require('../templates/footer.php'); ?>