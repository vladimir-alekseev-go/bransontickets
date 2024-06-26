<?php

use common\models\form\PackageForm;
use frontend\controllers\BaseController;

/**
 * @var PackageForm $packageForm
 */

$this->context->layout = BaseController::LAYOUT_EMPTY;
$selectedItems = $packageForm->getSelectedItems();

?>
<?php if (!empty($selectedItems)) { ?>
    <div class="selected-info">
        <div><small>Selected items:</small></div>
        <?php foreach ($selectedItems as $item) { ?>
            <div class="js-vp-item-remove float-end" data-id="<?= $item['id'] ?>">
                <span class="icon ib-x fs-5 cursor-pointer blue"></span>
            </div>
            <div class="name">
                <small>
                    <b><?= $item['item']->name ?></b> <?= $item['allotment']->name ?? '' ?>
                </small>
            </div>
            <div class="date mb-1"><small><?= $item['dateFormatting'] ?></small></div>
        <?php } ?>
    </div>
    <div class="line"></div>
<?php } ?>
