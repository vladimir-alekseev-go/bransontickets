<?php

use common\models\TrAttractions;
use common\models\TrLunchs;
use common\models\TrOrders;
use common\models\TrShows;
use common\models\VacationPackageOrder;
use yii\helpers\Url;

/**
 * @var string               $uniqueHash
 * @var VacationPackageOrder $vacationPackage
 * @var TrOrders             $Order
 */

?>
<div class="white-block shadow-block mb-3 cart-item cart-item-icon item-packages
<?php if ($vacationPackage->cancelled) {?> status-cancelled<?php }?>
<?php if ($vacationPackage->modified) {?> status-modified<?php }?>">
    <div class="vp-tickets-head">
        <a href="<?= Url::to(['packages/detail', 'code' => $vacationPackage->vacationPackage->code]) ?>"
           class="dark"><strong><?= $vacationPackage->name ?> Package</strong></a>
        <div>Package quantity:&nbsp;<?= $vacationPackage->count ?></div>
    </div>
    <div class="vp-tickets">
        <?php foreach ($vacationPackage->getPackages() as $package) { ?>
            <?php $item = $package->getItem(); ?>
            <div class="ticket">
                <div class="row">
                    <div class="col-12 col-lg-5">
                        <div class="float-end item-type item-<?= $item::TYPE ?> d-lg-none d-block"></div>
                        <?php if (!empty($item->preview_id)) { ?>
                            <a class="img" href="<?= $item->getUrl() ?>"><img src="<?= $item->preview->url ?>" alt=""/></a>
                        <?php } else { ?>
                            <a class="img img-empty" href="<?= $item->getUrl() ?>"></a>
                        <?php } ?>
                        <a href="<?= $item->getUrl() ?>" class="title"><?= $package->name ?></a>
                        <div>
                            <i class="fa fa-map-marker"></i>
                            <small><?= $package->type_name ?></small>
                        </div>
                        <div>
                            <i class="fa fa-calendar"></i>
                            <small>
                                <?php if ($item::TYPE === TrShows::TYPE) { ?>
                                    <?= $package->getStartDataTime()->format('m/d/Y h:i A') ?>
                                <?php } elseif (!$package->isAnyTime && in_array(
                                        $item::TYPE,
                                        [TrAttractions::TYPE],
                                        true
                                    )) { ?>
                                    <?= $package->getStartDataTime()->format(
                                        'l, M d, h:i A'
                                    ) ?>
                                <?php } else { ?>
                                    <?= $package->getStartDataTime()->format(
                                        'm/d/Y'
                                    ) ?> <?= $package::ANY_TIME ?> - <?= $package->getEndDataTime()->format(
                                        'm/d/Y'
                                    ) ?> <?= $package::ANY_TIME ?>
                                <?php } ?>
                            </small>
                        </div>
                    </div>
                    <div class="col-8 col-lg-4 mt-4">
                        <div class="row">
                            <div class="col-8"><small>Ticket type</small></div>
                            <div class="col-4 text-center">
                                <small class="d-none d-sm-block">Quantity</small>
                                <small class="d-block d-sm-none">Qty</small>
                            </div>
                            <?php foreach ($package->getTickets() as $key => $ticket) { ?>
                                <div class="col-8"><?= $ticket->name ?> <?= $ticket->description ? '- ' .
                                        $ticket->description : ''
                                    ?><?= $ticket->seats ? ', seats:' . $ticket->seats : '' ?></div>
                                <div class="col-4 text-center"><?= $ticket->qty ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-4 col-lg-3 text-center mt-4 mt-lg-0">
                        <div class="item-type item-<?= $item::TYPE ?> d-none d-lg-block"></div>
                        <?php if (isset($Order)) { ?>
                            <a class="get-more-tickets" target="_blank" href="<?= Url::to(
                                [
                                    'order/voucher',
                                    'orderNumber' => $Order->order_number,
                                    'packageId'   => $package->package_id,
                                    'id'          => $Order->tripium_user_id
                                ]
                            ) ?>">Print Voucher</a>
                        <?php } else { ?>
                            <a class="get-more-tickets" href="<?= $item->getUrl($package->getStartDataTime()) ?>">
                                Get more tickets
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="row">
        <div class="col-8">

            <?php if (isset($Order)) { ?>
                <?php if ($vacationPackage->canCancel()) { ?>
                    <a href="#" onclick="return cancellation.open('<?= Url::to(
                        [
                            'order/cancellation',
                            'orderNumber'       => $Order->order_number,
                            'vacationPackageId' => $vacationPackage->id
                        ]
                    ) ?>');"><span class="icon ib-x"></span>&nbsp;<strong>Cancel item</strong></a>
                <?php } ?>
            <?php } else { ?>

                <a class="reade me-3 js-popup-cancellation-policy" href="#"
                   data-url="<?= Url::to(
                       ['order/cancellation-policy-vacation-package', 'id' => $vacationPackage->id]
                   ) ?>">
                    <span class="icon ib-book-open"></span>&nbsp;<strong>Read policy</strong>
                </a>
                <?php if ($vacationPackage->vacationPackage) { ?>
                    <a class="me-3" href="<?= Url::to(
                        [
                            'packages/detail',
                            'code'            => $vacationPackage->vacationPackage->code,
                            'packageModifyId' => $vacationPackage->id
                        ]
                    ) ?>">
                        <span class="icon ib-edit"></span>&nbsp;<strong>Modify</strong>
                    </a>
                <?php } ?>
                <a class="me-3" href="<?= Url::to(['order/delete-vatation-package', 'uniqueHash' => $uniqueHash]) ?>"
                   onclick="return confirm('Do you want to remove?')">
                    <span class="icon ib-trash-alt"></span>&nbsp;<strong>Remove</strong>
                </a>
            <?php } ?>
        </div>
        <div class="col-4 text-end">
            Total:
            <div class="total-info">
                <div class="white-block shadow-block t-i-description p-3">
                    <?= $this->render('vp-package-item-total-description', compact('vacationPackage')) ?>
                </div>
                <i class="fa fa-info-circle"></i>
            </div>
            <span class="cost">$ <?=
                number_format($vacationPackage->getFullTotal(), 2, '.', '')
                ?></span>
        </div>
    </div>
</div>