<?php

use common\models\VacationPackage;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * @var VacationPackage $item
 * @var int             $key
 */
$items = $item->getItems();

?>
<div id="packages-list-<?= $item->id?>" class="item">
    <div class="package-name text-center text-md-start mb-3">
        <div class="name">
            <a href="<?= Url::to(['packages/detail', 'code' => $item->code]) ?>"><?= $item->name ?> Package</a>
        </div>
        <?php if ($item->getSaveUpTo()) { ?>
            <div class="btn btn-secondary cursor-default">Save up to $<?= $item->getSaveUpTo() ?></div>
        <?php } ?>
    </div>
    <div class="row">
        <div class="col-md-6 js-package">
            <div class="info mb-3">
                <div class="info-item">
                <div class="info-title">Available Dates:</div>
                    <span>
                        <?= (new DateTime($item['valid_start']))->format('M d, Y') ?> -
                        <?= (new DateTime($item['valid_end']))->format('M d, Y') ?>
                    </span>
                </div>
                <div class="info-item">
                <div class="info-title">Category:</div>
                    <span><?= implode(', ', $item->getTypes()) ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="price mb-3">
                <div class="price-item">
                    <div class="price-title">Package from</div>
                    <?php $prices = ArrayHelper::getColumn($item->vacationPackagePrices, 'price'); ?>
                    <?php $min = count($prices) > 0 ? min($prices) : 0; ?>
                    <?php $max = count($prices) > 0 ? max($prices) : 0; ?>
                    <span>$ <?= number_format($min, 2, '.', '') ?></span>
                </div>
                <div class="price-item">
                    <div class="price-title">Pick
                        <?php foreach (ArrayHelper::getColumn($item->vacationPackagePrices, 'count') as $k => $c) {?>
                            <?php if ($k === count($item->vacationPackagePrices)-1 && count($item->vacationPackagePrices) !== 1) {
                                echo ' or ';}?>
                            <?php if ($k !== 0 && $k !== count($item->vacationPackagePrices)-1) { echo ', ';} echo $c;?>
                        <?php }?> items</div>
                        <span>
                            <?php if ($min === $max) {?>
                                for <b class="dark">$ <?= number_format($min, 2, '.', '') ?></b>
                            <?php } else {?>
                                from <b class="dark">
                                    $ <?= number_format($min, 2, '.', '') ?> to $ <?= number_format($max, 2, '.', '') ?>
                                </b>
                            <?php }?>
                        </span>
                    </div>
                </div>
        </div>
        <div class="col-12">
            <div class="line mb-3"></div>
        </div>
        <div class="col-md-8">
            <div class="description mb-4"><?= $item->description ?></div>
        </div>
        <div class="col-md-4">
            <div class="description mb-4">
            <a href="<?= Url::to(['packages/detail', 'code' => $item->code])?>" class="btn btn-primary w-100">
                Buy Package
            </a>
            </div>
        </div>
        <div class="col-12 js-package items-in-list js-items-in-list">
            <div class="items-in row">
                <?php foreach ($items as $it) { ?>
                    <?php $itemExternal = $it->itemExternal; ?>
                    <div class="col-12 col-md-6 col-xl-4 mb-3 item-in">
                        <a href="<?= $itemExternal->getUrl() ?>" class="package-item">
                            <?php if (!empty($itemExternal->preview->url)) { ?>
                                <img src="<?= $itemExternal->preview->url ?>" alt="<?= $itemExternal->name ?>">
                            <?php } else { ?>
                                <div class="img img-empty">
                                    <img class="preview" width="260" src="/img/bransontickets-noimage.png" alt=""/>
                                </div>
                            <?php } ?>
                            <span><?= $itemExternal->name ?><span>
                        </a>
                    </div>
                <?php } ?>
            </div>
            <?php if (count($items) > 3) { ?>
            <div class="text-center">
                <a href="#" onclick="$('.js-items-in-list').toggleClass('items-in-list-full'); return false;">
                    <span class="view-more d-md-none">View more <span class="icon br-t-points"></span></span>
                    <span class="view-more-hide d-none">Hide <span class="icon br-t-points"></span></span>
                </a>
            </div>
            <?php }?>
        </div>
    </div>
 </div>

 <?php $this->registerJs(
    "
    $('.package-show').click(function () {
        $(this).closest('.item').find('.js-package').css('display', 'block');
        $(this).css('display', 'none');
        $(this).siblings('.package-hide').css('display', 'block');
    });
      
    $('.package-hide').click(function () {
        $(this).closest('.item').find('.js-package').css('display', 'none');
        $(this).css('display', 'none');
        $(this).siblings('.package-show').css('display', 'block');
    });
    "
);
?>
