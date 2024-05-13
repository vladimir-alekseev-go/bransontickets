<?php

use common\models\TrOrders;
use yii\helpers\Url;

/**
 * @var TrOrders[] $orders
 */

?>
<div class="white-block shadow-block margin-block-small ms-n15 me-n15">
    <div class="orders-list">
        <div class="orders-list-header row">
            <div class="col-3">Order #</div>
            <div class="col-3">Date</div>
            <div class="col-3">Status</div>
            <div class="col-3 text-end">Amount</div>
        </div>
        <div class="orders-list-body">
            <?php foreach ($orders as $order) { ?>
                <div class="orders-list-item row">
                    <div class="col-3">
                        <a href="<?= Url::to(['order/detail', 'orderNumber' => $order->order_number]) ?>">
                            <strong><?= $order->order_number ?></strong>
                        </a>
                    </div>
                    <div class="col-3"><?= $order->getCreatedAt()->format('M d,Y') ?></div>
                    <div class="col-3">
                        <span class="order-status order-status-<?= $order->getStatusClass() ?>">
                            <?= $order->getStatus() ?>
                        </span>
                    </div>
                    <div class="col-3 text-end">
                        <span class="cost">$ <?= number_format($order->getFullTotal(), 2, '.', '') ?></span>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
