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
    <div class="mb-2"><small>Items in Package</small></div>
    <div class="items-in row">
        <?php foreach ($items as $it) { ?>
            <?php $itemExternal = $it->itemExternal;?>
            <div class="col-12 col-sm-6 col-md-4">
                <a href="<?= $itemExternal->getUrl() ?>">
                    <?php if (!empty($itemExternal->preview->url)) { ?>
                        <img src="<?= $itemExternal->preview->url ?>" alt="<?= $itemExternal->name ?>">
                    <?php } else { ?>
                        <div class="img img-empty">
                            <img class="preview" width="260" src="/img/bransontickets-noimage.png" alt=""/>
                        </div>
                    <?php } ?>
                    <span><?= $itemExternal->name ?></span>
                </a>
            </div>
        <?php } ?>
    </div>
</div>
