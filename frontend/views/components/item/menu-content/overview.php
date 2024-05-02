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
    <div class="row align-items-end">
        <div class="col-lg-2 order-2 d-none d-lg-block">
            <div class="ticket text-center pt-3">
                <?php if ($model instanceof TrPosHotels || $model instanceof TrPosPlHotels) { ?>
                    <div class="title">
                        Avg rate Per Night
                    </div>
                    <div class="price">$ <?= $model->min_rate ?></div>
                <?php } else { ?>
                    <div class="title">
                        Tickets from
                    </div>
                    <div class="price">$ <?= $model->min_rate ?></div>
                    <a href="#availability" onclick="$('.js-tag').eq(0).trigger('click')" class="btn btn-primary w-100">
                        Buy now
                    </a>
                <?php } ?>
            </div>
        </div>
        <div class="col-lg-10 order-1">
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
    <?php if (!($model instanceof TrPosHotels || $model instanceof TrPosPlHotels)) { ?>
        <div class="text-center">
            <a href="#schedule" id="more-available">
                More Available Dates <span class="icon br-t-points"></span>
            </a>
        </div>
    <?php } ?>
</div>
<?php if ($model instanceof TrPosHotels || $model instanceof TrPosPlHotels) { ?>
    <div id="show-list" class="rooms-type-list">
        <?php //$this->render('@app/views/components/item/menu-content/hotel-rooms', compact('HotelReservationForm')) ?>
    </div>
<?php } else { ?>
    <?= $this->render('@app/widgets/scheduleSlider/views/container', compact('model')) ?>
<?php } ?>

