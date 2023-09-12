<?php

use common\models\VacationPackage;
use yii\helpers\Url;

/**
 * @var VacationPackage $item
 * @var int             $key
 */
$items = $item->getItems();

?>
<div class="vacation-package">
    <div class="item-up">
        <a class="title" href="<?= Url::to(['packages/overview', 'code' => $item->code]) ?>">
            <?= $item->name ?> Package
        </a>
        <?php if (!empty($item->description)) { ?>
            <div class="description"><?= $item->description ?></div><?php } ?>
    </div>
    <div class="gray-light mb-2"><small>Items in Package</small></div>
    <div class="items-in row">
        <?php foreach ($items as $it) { ?>
            <?php $itemExternal = $it->itemExternal; ?>
            <?php if (!empty($itemExternal->itemsPhoto[0])) { ?>
                <?php $itemsPhoto = $itemExternal->itemsPhoto[0]; ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <a href="<?= $itemExternal->getUrl() ?>">
                        <img src="<?= $itemsPhoto->preview->url ?>" alt="<?= $itemExternal->name ?>">
                        <span><?= $itemExternal->name ?></span>
                    </a>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
</div> 
