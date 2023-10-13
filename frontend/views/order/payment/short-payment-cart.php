<?php

use common\models\TrBasket;
use yii\helpers\Url;

$basket = TrBasket::build();
?>

<div class="white-block shadow-block border-block margin-block-small cart-short-block">

    <div class="row">
        <div class="col-5"><h5><strong>Cart contents</strong></h5></div>
        <div class="col-7 text-end">
            <a href="<?= Url::to(['order/cart']) ?>">
                <span class="icon ib-edit"></span> <strong>Edit cart contents</strong>
            </a>
        </div>
    </div>

    <div class="cart-short">
        <?php foreach ($basket->getPackages() as $package) { ?>
            <div class="it">
                <div class="title"><?= $package->name ?></div>
                <div class="cost cost-small gray float-end">$ <?= number_format(
                        $package->full_total,
                        2,
                        '.',
                        ''
                    ) ?></div>
                <div class="type gray"><small><?= $package->getTicketsQty() ?> Tickets</small></div>
            </div>
        <?php } ?>
        <div class="mb-4"></div>
        <?php if (!empty($basket->getSaved())) { ?>
            <div class="mb-1">
                <div class="cost cost-small green float-end">$ <?= number_format(
                        $basket->getSaved(),
                        2,
                        '.',
                        ''
                    ) ?></div>
                <div><small class="gray">Total Savings:</small></div>
            </div>
        <?php } ?>
        <?php if ($basket->getCoupon() !== null) { ?>
            <div class="mb-1">
                <div class="cost cost-small green float-end">$ <?= number_format(
                        $basket->getCoupon()->getDiscount(),
                        2,
                        '.',
                        ''
                    ) ?></div>
                <div><small class="gray">Discount:</small></div>
            </div>
        <?php } ?>
        <div class="pt-0">
            <div class="cost float-end">$ <?= number_format(
                    $basket->getFullTotal(),
                    2,
                    '.',
                    ''
                ) ?></div>
            <div><strong>Cart Total:</strong></div>
        </div>
    </div>
</div>