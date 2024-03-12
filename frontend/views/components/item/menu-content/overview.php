<?php

use common\models\form\HotelReservationForm;
use common\models\TrAttractions;
use common\models\TrShows;
use common\models\TrPosHotels;
use common\models\TrPosPlHotels;
use frontend\widgets\scheduleSlider\ScheduleSliderWidget;
use yii\web\JqueryAsset;

/**
 * @var TrShows|TrAttractions|TrPosHotels|TrPosPlHotels $model
 * @var HotelReservationForm                            $HotelReservationForm
 * @var ScheduleSliderWidget                            $ScheduleSlider
 */

$this->registerJsFile('/js/order.js', ['depends' => [JqueryAsset::class]]);
$this->registerJs('order.init()');
?>

<div class="overview-calendar-block">
    <div class="row">
        <div class="col-sm-4 col-md-3 col-lg-2">
            <div class="ticket text-center pt-3">
                <div class="title">
                    <?php if ($model instanceof TrPosHotels || $model instanceof TrPosPlHotels) { ?>
                        Per Night
                    <?php } else { ?>
                        Tickets from
                    <?php } ?>
                </div>
                <div class="price">$ <?= $model->min_rate ?></div>
                <a href="#" class="btn btn-primary w-100">
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
                        'Search'           => $HotelReservationForm->searchHotel,
                        'SearchButtonName' => 'Room',
                        'ReservationForm'  => $HotelReservationForm
                    ]
                ) ?>
                <?php $this->registerJsFile(
                    '/js/bootstrap-datepicker.min.js',
                    ['depends' => [JqueryAsset::class]]
                ); ?>
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
<?php if ($model instanceof TrPosHotels || $model instanceof TrPosPlHotels) { ?>
    <div id="show-list" class="rooms-type-list">
        <?php //$this->render('@app/views/components/item/menu-content/hotel-rooms', compact('HotelReservationForm')) ?>
    </div>
<?php } else { ?>
    <?= $this->render('@app/widgets/scheduleSlider/views/container', compact('model')) ?>
<?php } ?>

