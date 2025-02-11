<?php

use common\models\TrOrders;
use common\models\TrPosHotels;
use yii\helpers\Url;

/**
 * @var TrOrders $order
 */

$phone = '1-877-368-3782';

$packageIds = [];

foreach ($order->getValidPackages() as $package) {
    if ($package->category !== TrPosHotels::TYPE && empty($package->voucher_link)) {
        $packageIds[] = $package->package_id;
    }
}

?>

<div class="order-short-description white-block shadow-block">
    <div>Order number</div>
    <div class="mb-3">
        <strong><?= $order->order_number ?></strong>
        <span class="float-end order-status order-status-<?= $order->getStatusClass() ?>"><?= $order->getStatus()
            ?></span>
    </div>
    <div>Order date</div>
    <div class="mb-3"><strong><?= $order->getCreatedDate()->format('M d,Y') ?></strong></div>

    <?= $this->render(
        'order-short-cost',
        [
            'name' => 'Taxes and Conv. Fees Total',
            'cost' => $order->getFullTax() + $order->getServiceFee()
        ]
    ) ?>
    <?= $this->render(
        'order-short-cost',
        [
            'name' => 'Cancellation Fee',
            'cost' => $order->getData()["fullCancellationFee"]
        ]
    ) ?>
    <?= $this->render(
        'order-short-cost',
        [
            'name' => 'Processing Fee',
            'cost' => $order->getData()["processingFee"]
        ]
    ) ?>
    <?= $this->render(
        'order-short-cost',
        [
            'name' => 'Discount',
            'cost' => $order->getFullDiscount()
        ]
    ) ?>
    <?= $this->render(
        'order-short-cost',
        [
            'name' => 'Coupon',
            'cost' => $order->getData()["fullCoupon"]
        ]
    ) ?>
    <?= $this->render(
        'order-short-cost',
        [
            'name' => 'Order Total',
            'cost' => $order->getFullTotal()
        ]
    ) ?>
    <?= $this->render(
        'order-short-cost',
        [
            'name' => 'Due at hotel (Resort Fee)',
            'cost' => $order->getResortFee()
        ]
    ) ?>

    <div class="line"></div>
    <div class="mb-2"><a href="<?= Url::to(
            ['order/print', 'orderNumber' => $order->order_number, 'id' => $order->tripium_user_id]
        ) ?>" target="_blank">
            <span class="icon ib-print fs-6"></span> <strong>Print Order Confirmation</strong>
        </a>
    </div>
    <?php if ($order->getValidPackages() || $order->getValidUniqueVacationPackages()) { ?>
        <div class="mb-2">
            <a href="<?= Url::to(
                ['order/print-itinerary', 'orderNumber' => $order->order_number, 'id' => $order->tripium_user_id]
            ) ?>" target="_blank"><span class="icon ib-print fs-6"></span> <strong>Print Itinerary</strong>
            </a>
        </div>
        <?php if (!empty($packageIds)) { ?>
            <div class="mb-2">
                <a href="<?= Url::to(
                    ['order/voucher', 'orderNumber' => $order->order_number, 'id' => $order->tripium_user_id]
                ) ?>" target="_blank"><span class="icon ib-print fs-6"></span> <strong>Print All Vouchers</strong>
                </a>
            </div>
        <?php } ?>
        <?php foreach ($order->getValidPackages() as $package) { ?>
            <?php if (!empty($package->voucher_link)) { ?>
                <div class="mb-2">
                    <a href="<?= $package->voucher_link ?>">
                        <strong>eTicket <?= $package->name ?></strong>
                    </a>
                </div>
            <?php } ?>
        <?php } ?>
    <?php } ?>

    <?php if (!Yii::$app->user->isGuest && $order->canCancel()) { ?>
    <div class="mb-2">
        <a href="#" onclick="return cancellation.open('<?= Url::to(
            ['order/cancellation', 'orderNumber' => $order->order_number, 'id' => $order->tripium_user_id]
        ) ?>');"><span class="icon ib-x fs-6"></span> <strong>Cancel order</strong></a>
    </div>
    <?php } ?>
    <div class="line"></div>
    <div class="text-center">
        <div class="mb-2">
            <small>If you want to cancel or modify the order, please call</small>
        </div>
        <div class="phone fs-5">
            <strong><span class="icon br-t-smartphone fs-4"></span> <?= $phone ?></strong>
        </div>
    </div>
</div>
