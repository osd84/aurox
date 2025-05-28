<?php

use OsdAurox\Modal;
use OsdAurox\Sec;

$modal = $modal ?? null;
if(!$modal instanceof Modal)
{
    throw new Exception('Modal not defined');
}

?>
<!-- Modal -->
<div class="<?= Sec::hNoHtml($modal->class) ?>" id="<?= Sec::hNoHtml($modal->id) ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="<?= Sec::hNoHtml($modal->id) ?>" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="<?= Sec::hNoHtml($modal->id) ?>Title"><?= Sec::hNoHtml($modal->title) ?></h5>
            </div>
            <div class="modal-body">
                <?= Sec::hNoHtml($modal->msg) ?>
            </div>
            <div class="modal-footer">
                <?php if ($modal->showBtn): ?>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= Sec::hNoHtml($modal->btnCancel) ?></button>
                    <button type="button" class="btn btn-primary"><?= Sec::hNoHtml($modal->btnAccept) ?></button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>