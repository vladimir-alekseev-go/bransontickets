<?php

use common\models\Package;
use common\models\TrAttractions;
use common\models\TrPosHotels;
use common\models\TrShows;
use common\models\User;
use yii\helpers\Url;

/**
 * @var Package                           $package
 * @var TrShows|TrAttractions|TrPosHotels $item
 * @var bool                              $isOrderDetail
 */

$isOrderDetail = $isOrderDetail ?? false;
$item = $package->getItem();
$order = $package->getOrder();
$user = User::getCurrentUser();

?>

<div class="white-block shadow-block mb-3 cart-item">
    <div class="<?php if ($package->cancelled) { ?>status-cancelled<?php } ?> <?php if ($package->modified) { ?>status-modified<?php } ?>">
        <div class="up">
            <?= $this->render('@app/views/order/cart/item-up', compact('package', 'item'))?>
        </div>
        <div class="clear-both"></div>
        <div class="tickets">
            <?php $i = -1; ?>
            <?php foreach ($package->getTickets() as $key => $ticket) { ?>
                <?php $i++; ?>
                <div class="ticket">
                    <div class="row">
                        <div class="col-12 col-sm-5 mb-3 mb-sm-0">
                            <?php if ($item::TYPE === TrPosHotels::TYPE) { ?>
                                <div><small>Room <?= $i + 1 ?></small></div>
                                <div><?= $ticket->name ?></div>
                                <?php if ($package->isNonRefundable()) { ?>
                                    <div class="tag tag-non-refundable-lite">Non Refundable</div>
                                <?php } else { ?>
                                    <div class="tag tag-refundable">Refundable</div>
                                <?php } ?>
                            <?php } else { ?>
                                <div><small>Ticket type</small></div>
                                <?= $ticket->name ?> <?= $ticket->description ? '- ' . $ticket->description : '' ?><?=
                            $ticket->seats ? ', seats:' . $ticket->seats : '' ?>
                                <?php if ($package->isNonRefundable()) { ?>
                                    <div class="tag tag-non-refundable">Non refundable</div>
                                <?php } ?>
                            <?php } ?>
                        </div>

                        <?php if ($item::TYPE === TrPosHotels::TYPE) { ?>
                            <div class="col-8 col-sm-4">
                                <div><small>Guests</small></div>
                                <div><?= trim($ticket->first_name . ' ' . $ticket->last_name) ?></div>
                                <div><?= $ticket->qty ?> Adult<?= $ticket->qty > 1 ? 's' : '' ?>
                                    <?= $ticket->child_ages ? count($ticket->child_ages) . ', Children (' . implode(
                                            'y, ',
                                            $ticket->child_ages
                                        ) . 'y)' : '' ?></div>
                                <?php if ($ticket->getSmoking()) { ?>
                                    <div><?= $ticket->getSmoking() ?></div>
                                <?php } ?>
                            </div>
                        <div class="col-4 col-sm-3 text-end">
                        <?php } else { ?>
                            <div class="col-6 col-sm-4 text-sm-center">
                                <div><small>Quantity</small></div>
                                <?= $ticket->qty ?>
                            </div>
                        <div class="col-6 col-sm-3 text-end">
                        <?php } ?>
                            <div><small>
                                    Price / <?php if ($item::TYPE === TrPosHotels::TYPE) { ?>Room<?php } else {
                                        ?>Ticket<?php } ?>
                                </small></div>
                            <?php if ($ticket->retail_rate !== $ticket->result_rate) { ?>
                                <div>
                                <span class="tag tag-save white">$ <?= number_format(
                                        $ticket->getSaved(),
                                        2,
                                        '.',
                                        ''
                                    ) ?>&nbsp; Saved
                                </span>
                                    <span class="ms-2 text-nowrap">$ <?= $ticket->retail_rate ?></span>
                                </div>
                            <?php } ?>
                            <span class="cost text-nowrap">$ <?= $ticket->result_rate ?></span>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <?php if ($package->getComments()) { ?>
            <div class="comments mb-3">
                <div><small>Order Comments:</small></div>
                <div><?= $package->getComments() ?></div>
            </div>
        <?php } ?>
        <div class="row">
            <div class="col-8">
                <?php if ($isOrderDetail) { ?>
                    <?php if (!$package->cancelled) { ?>
                        <?php if ($package->category !== TrPosHotels::TYPE) { ?>
                            <?php if (!empty($package->voucher_link)) { ?>
                                <a class="me-3" href="<?= $package->voucher_link ?>" target="_blank">
                                    <strong>eTicket</strong>
                                </a>
                            <?php } else { ?>
                                <a class="me-3" href="<?= Url::to(
                                    [
                                        'order/voucher',
                                        'orderNumber' => $order->order_number,
                                        'id'          => $order->tripium_user_id,
                                        'packageId'   => $package->package_id
                                    ]
                                ) ?>" target="_blank">
                                    <span class="icon ib-print fs-6"></span> <strong>Print Voucher</strong>
                                </a>
                            <?php } ?>
                        <?php } ?>
                        <?php if (($package->canCancel() || $package->canModify(
                                )) && !Yii::$app->user->isGuest && $user['tripium_id'] === $order->getData(
                            )['customer']['id']) { ?>
                            <?php if ($package->canCancel()) { ?>
                                <a class="me-3" href="#" onclick="return cancellation.open('<?= Url::to(
                                    [
                                        'order/cancellation',
                                        'orderNumber'   => $order->order_number,
                                        'packageNumber' => $package->package_id
                                    ]
                                ) ?>');">
                                    <i class="fa fa-close"></i>
                                    <strong>Cancel item</strong>
                                </a>
                            <?php } ?>
                            <?php /*if ($package->canModify()) { ?>
                                <a class="me-3" href="#"
                                   onclick="return modification.open('<?= $order->order_number ?>', '<?=
                                   $package->package_id ?>', '<?= $package->getStartDataTime()->format(
                                       'Y-m-d H:i:s'
                                   ) ?>');">
                                    <i class="fa fa-edit"></i> <strong>Modify item</strong>
                                </a>
                            <?php }*/ ?>
                        <?php } ?>
                    <?php } ?>
                <?php } else { ?>
                    <a class="reade me-3 js-popup-cancellation-policy text-nowrap" href="#"
                       data-url="<?= Url::to(
                           ['order/cancellation-policy', 'packageId' => $package->package_id]
                       ) ?>">
                        <i class="fa fa-book"></i>&nbsp;<strong>Read policy</strong>
                    </a>
                    <?php if ($package->canModify()) { ?>
                    <a class="me-3 text-nowrap" href="<?= $package->getModifyUrl() ?>">
                        <i class="fa fa-edit"></i>&nbsp;<strong>Modify</strong>
                    </a>
                    <?php } ?>
                    <a class="me-3 text-nowrap" href="<?= Url::to(
                        [
                            'order/cart',
                            'remove_id' => $package->package_id,
                            'category'  => $item::TYPE
                        ]
                    ) ?>"
                       onclick="return confirm('Do you want to remove?')">
                       <i class="fa fa-trash"></i>&nbsp;<strong>Remove</strong>
                    </a>
                <?php } ?>
            </div>
            <div class="col-4 text-end">
                Total:
                <div class="total-info">
                    <div class="white-block shadow-block t-i-description p-3">
                        <?= $this->render('item-total-description', compact('package')) ?>
                    </div>
                    <i class="fa fa-info-circle"></i>
                </div>
                <span class="cost">$ <?=
                    number_format($package->getFullTotal(), 2, '.', '')
                    ?></span>
            </div>
        </div>
    </div>
</div>
