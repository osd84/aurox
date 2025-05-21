<?php


use OsdAurox\Base;

$title = 'Aurox';

require_once '../aurox.php';


?>
<?php require('../templates/header.php'); ?>
<main class="main-content">
    <h1>Accueil</h1>
    <p>Contenu de la page</p>

    <?php if(Base::isMobile()): ?>
        <p>C'est un petit écran</p>
    <?php else: ?>
        <p>C'est un grand écran</p>
    <?php endif; ?>



</main>
<?php require('../templates/footer.php'); ?>
