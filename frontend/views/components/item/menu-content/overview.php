<?php

use common\models\form\HotelReservationForm;
use common\models\TrAttractions;
use common\models\TrShows;
use common\models\TrPosHotels;
use common\models\TrPosPlHotels;
use yii\web\JqueryAsset;

/**
 * @var TrShows|TrAttractions|TrPosHotels|TrPosPlHotels $model
 * @var HotelReservationForm                            $HotelReservationForm
 */

$this->registerJsFile('/js/order.js', ['depends' => [JqueryAsset::class]]);
$this->registerJs('order.init()');
?>

<div id="overview" role="tabpanel" aria-labelledby="overview-tab" class="tab-pane active">
    <div class="fixed">
        <div class="overview-calendar-block">
            <div class="row">
                <div class="col-sm-4 col-md-3 col-lg-2">
                    <div class="ticket">
                        <div class="title">
                            <?php if ($model instanceof TrPosHotels || $model instanceof TrPosPlHotels) { ?>
                                    Per Night
                                <?php } else { ?>
                                    Tickets from
                                <?php } ?>
                            </div>
                        <div class="price">$ <?= $model->min_rate ?></div>
                        <a href="#" class="btn buy-btn">
                            <?php if ($model instanceof TrPosHotels || $model instanceof TrPosPlHotels) { ?>
                                Book now
                            <?php } else { ?>
                                Buy now
                            <?php } ?>
                        </a>
                    </div>
                </div>
                <div class="col-sm-8 col-md-9 col-lg-10">
                    <?php if ($model instanceof TrPosHotels || $model instanceof TrPosPlHotels) { ?>
                        <?= $this->render(
                            '@app/views/components/item/menu-content/hotel-filter',
                            [
                                'Search' => $HotelReservationForm->searchHotel,
                                'SearchButtonName' => 'Room',
                                'ReservationForm' => $HotelReservationForm
                            ]
                        ) ?>
                        <?php $this->registerJsFile('/js/bootstrap-datepicker.min.js', ['depends' => [JqueryAsset::class]]); ?>
                        <?php $this->registerJsFile('/js/datepicker.js', ['depends' => [JqueryAsset::class]]); ?>
                        <?php $this->registerJsFile('/js/hotel-detail.js', ['depends' => [JqueryAsset::class]]); ?>
                        <?php $this->registerJsFile('/js/hotel.filter.js', ['depends' => [JqueryAsset::class]]); ?>
                        <?php $this->registerJs(
                            'hotelFilter.initHotel($("#show-list"), $("#panel-list"), $("#hotel-filter"), $(".filter-room"), "' .
                            $HotelReservationForm->model->code . '")'
                        ); ?>
                        <?php $this->registerJs('hotelDetail.init()'); ?>
                    <?php } else { ?>
                        <?= $ScheduleSlider->run() ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <?php if ($model instanceof TrPosHotels || $model instanceof TrPosPlHotels) { ?>
        <div class="fixed">
            <div id="show-list" class="rooms-type-list">
                <?php //$this->render('@app/views/components/item/menu-content/hotel-rooms', compact('HotelReservationForm')) ?>
            </div>
        </div>
    <?php } else { ?>
        <div class="fixed">
            <?= $this->render('@app/widgets/scheduleSlider/views/container', compact('model')) ?>
        </div>
    <?php } ?>
    <div class="fixed">
        <div class="overview-description">
            <div class="row">
                <div class="col-sm-6">
                    <div class="title">Description</div>
                    <div class="description"><?= $model->getDescriptionShort(300) ?></div>
                    <a href="#" class="view-full-description" id="full-description">View full Description <i class="fa fa-angle-right"></i></a>
                </div>
                <div class="col-sm-6">
                    <?php if (!empty($model->voucher_procedure)) { ?>
                        <div class="title">Voucher Exchange</div>
                        <div class="description"><?= $model->voucher_procedure ?></div>
                    <?php } ?>
                    <?php if (!empty($model->getCancellationPolicyText())) { ?>
                        <div class="title">Cancellation policy</div>
                        <div class="description"><?= $model->getCancellationPolicyText() ?></div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <?= $this->render('@app/views/components/item/menu-content/overview-gallery', compact('model', 'videos', 'images')) ?>

    <div class="overview-may-also-like">
        <div class="fixed">
            <div class="may-also-like-title">You may also like</div>
        </div>
        <div class="line"></div>
        <?= $this->render('@app/views/components/recommended', compact('showsRecommended')) ?>
    </div>
</div>
