<?php

use common\models\OrderModifyForm;
use common\models\Package;
use common\models\TrAttractions;
use common\models\TrOrders;
use common\models\TrShows;
use common\widgets\scheduleSlider\ScheduleSliderWidget;
use yii\bootstrap\ActiveForm;

/**
 * @var Package              $package
 * @var ScheduleSliderWidget $ScheduleSlider
 * @var TrOrders             $Order
 * @var OrderModifyForm      $OrderForm
 */
?>

<h3>Modification in order <?= $package->order ?> package <?= $package->package_id ?></h3>
<div id="popup-errors-modify"></div>
<div class="popup-modification-content">
    <div class="js-scrollbar-inner-small-screen">
        <div class="rows">
            <div class="row">
                <div class="col-md-8" id="order-form-left">
                    <div class="calendar-slider-in-order-attraction-modify">
                        <div class="js-scrollbar-inner-full-screen">
                            <?php echo $ScheduleSlider->run(); ?>
                        </div>
                    </div>

                </div>
                <div class="col-md-8 hide" id="order-form-left-confirm"></div>
                <div class="col-md-4">
                    <div class="js-scrollbar-inner-full-screen">
                        <div class="cancel-detail" id="order-modify-info">
                            <h5 class="mb-2"><strong>MODIFICATION ORDER DETAILS:</strong></h5>
                            <div class="it">
                                <div class="row row-small-padding">
                                    <div class="col-5">
                                        <?php if ($package->getItem() && $package->getItem()->preview_id) { ?>
                                            <img src="<?= $package->getItem()->preview->url ?>" alt=""/>
                                        <?php } ?>
                                    </div>
                                    <div class="col-7">
                                        <div class="title"><?= $package->name ?></div>
                                        <div class="desc"><small>
							<span id="date-packepge">
							<?php if ($package->category === TrShows::TYPE) { ?>
                                <?= $package->getStartDataTime()->format('l, M d, h:i A') ?>
                            <?php } elseif ($package->category === TrAttractions::TYPE && $package->isAnyTime) { ?>
                                Avail dates <?= $package->getStartDataTime()->format(
                                    'm/d/Y'
                                ) ?> Any Time - <?= $package->getEndDataTime()->format('m/d/Y') ?> Any Time
                            <?php } elseif ($package->category === TrAttractions::TYPE && !$package->isAnyTime) { ?>
                                Tickets on <?= $package->getStartDataTime()->format("l, M d, h:i A") ?>
                            <?php } else { ?>
                                Avail dates <?= $package->getStartDataTime()->format(
                                    'm/d/Y h:i A'
                                ) ?> - <?= $package->getEndDataTime()->format('m/d/Y h:i A') ?>
                            <?php } ?>
							</span>
                                            </small></div>
                                    </div>
                                </div>
                                <div class="row row-small-padding">
                                    <div class="col-12"><br/></div>
                                </div>
                                <div class="row row-small-padding">
                                    <div class="col-6"><span id="qty"><?= $package->getTicketsQty() ?> ticket<?=
                                            $package->getTicketsQty() > 1 ? "s" : "" ?></span></div>
                                    <div class="col-6 text-end"><span class="cost"
                                                                      id="package-total">$ <?= number_format(
                                                $package->total,
                                                2,
                                                '.',
                                                ''
                                            ) ?></span></div>
                                </div>
                            </div>

                            <div class="total">

                                <?php if (!empty($processingFee)) { ?>
                                    <div class="row row-small-padding">
                                        <div class="col-7">TRANSACTION FEE:</div>
                                        <div class="col-5 text-end">- <span class="cost">$ <?= number_format(
                                                    $processingFee,
                                                    2,
                                                    '.',
                                                    ''
                                                ) ?></span></div>
                                    </div>
                                <?php } ?>

                                <div class="row row-small-padding">
                                    <div class="col-7"><b>ORDER TOTAL<br>(incl. taxes):</b></div>
                                    <div class="col-5 text-end"><span id="fullTotalOrderNew" class="cost">$ <?=
                                            number_format(
                                                $Order->fullTotal + ($Order->getCoupon() ? $Order->getCoupon(
                                                )->discount : 0),
                                                2,
                                                '.',
                                                ''
                                            ) ?></span></div>
                                </div>
                                <div class="row row-small-padding">
                                    <div class="col-7"><b>DISCOUNT:</b></div>
                                    <div class="col-5 text-end"><span id="modified-discount"
                                                                      class="cost">$ <?= number_format(
                                                $Order->getCoupon() ? $Order->getCoupon()->discount : 0,
                                                2,
                                                '.',
                                                ''
                                            ) ?></span></div>
                                </div>

                                <div class="row row-small-padding" id="modified-amound-block">
                                    <div class="col-7"><b>MODIFICATION AMOUNT:</b></div>
                                    <div class="col-5 text-end">
                                        <span id="modified-amound" class="cost red modification-amount">$ 0.00</span>
                                    </div>
                                </div>
                            </div>

                            <?php $form = ActiveForm::begin(); ?>
                            <p><?= $form->field($OrderForm, 'coupon_code')->textInput(
                                    ['id' => 'modification-coupon-code']
                                ) ?></p>
                            <div class="alert alert-info hide" id="mess-amound-up">
                                In order to complete your modification you need to get a refund
                            </div>
                            <div class="alert alert-info hide" id="mess-amound-down">
                                In order to complete your modification you need to make a new payment
                            </div>
                            <div class="alert alert-info hide" id="mess-date-change">To finish modification click
                                Continue
                            </div>

                            <?php if (!$package->getItem()->call_us_to_book) { ?>
                                <button onclick="modification.btn = $(this); modification.proceed();return false;"
                                        id="btn-proceed"
                                        class="btn btn-primary btn-loading-need" disabled="disabled">Continue
                                </button>
                            <?php } ?>
                        </div>

                        <div id="cancel-detail-error"></div>

                        <div class="alert alert-warning alert-dismissible hide" id="mess-warning">Don’t forget to
                            Proceed
                            your modification otherwise we won’t save your changes
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
