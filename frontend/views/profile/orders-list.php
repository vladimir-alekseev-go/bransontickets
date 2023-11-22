<?php

use common\models\TrOrders;
use yii\helpers\Url;

/**
 * @var TrOrders[] $orders
 */

?>
<div class="white-block shadow-block margin-block-small ms-n15 me-n15">
    <div class="orders-list">
        <div class="orders-list-header">
            <div>Order #</div>
            <div>Date</div>
            <div>Status</div>
            <div class="text-end">Amount</div>
        </div>
        <div class="orders-list-body">
            <?php foreach ($orders as $order) { ?>
                <div class="orders-list-item">
                    <div>
                        <a href="<?= Url::to(['order/detail', 'orderNumber' => $order->order_number]) ?>">
                            <strong><?= $order->order_number ?></strong>
                        </a>
                    </div>
                    <div><?= $order->getCreatedAt()->format('M d,Y') ?></div>
                    <div class="text-center text-sm-start">
                        <span class="order-status order-status-<?= $order->getStatusClass() ?>">
                            <?= $order->getStatus() ?>
                        </span>
                    </div>
                    <div class="text-end">
                        <span class="cost">$ <?= number_format($order->getFullTotal(), 2, '.', '') ?></span>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
