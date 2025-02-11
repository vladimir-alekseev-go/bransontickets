<?php

use common\models\TrAttractions;
use common\models\TrShows;
use frontend\widgets\scheduleSlider\ScheduleSliderWidget;
use yii\web\JqueryAsset;

/**
 * @var TrShows|TrAttractions $model
 * @var ScheduleSliderWidget  $ScheduleSlider
 */

$this->registerJsFile('/js/order.js', ['depends' => [JqueryAsset::class]]);
$this->registerJs('order.init()');
?>

<div class="overview-calendar-block">
    <div class="row align-items-end">
        <div class="col-lg-2 order-2 d-none d-lg-block">
            <div class="ticket text-center pt-3">
                <div class="title">
                    Tickets from
                </div>
                <div class="price">$ <?= $model->min_rate ?></div>
                <a href="#availability" onclick="$('.js-tag').eq(0).trigger('click')" class="btn btn-primary w-100">
                    Buy now
                </a>
            </div>
        </div>
        <div class="col-lg-10 order-1">
            <?= $ScheduleSlider->run() ?>
        </div>
    </div>
    <div class="text-center">
        <a href="#schedule" id="more-available">
            <b>More Available Dates</b> <span class="icon br-t-points"></span>
        </a>
    </div>
</div>

<?= $this->render('@app/widgets/scheduleSlider/views/container', compact('model')) ?>

