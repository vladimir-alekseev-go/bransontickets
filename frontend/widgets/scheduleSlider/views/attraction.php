<?php

use common\models\TrAttractions;
use frontend\widgets\scheduleSlider\ScheduleSliderWidget;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var TrAttractions $model
 * @var array                  $prices
 * @var ScheduleSliderWidget   $scheduleSliderWidget
 */

$scheduleSliderWidget = $this->context;
?>

<div class="calendar-slider-block">
    <div class="week-wrap calendar-slider calendar-slider-in-order calendar-slider-in-order-attraction">
        <div class="admissions-list">
            <?php foreach ($prices as $name => $data) { ?>
                <div class="name"><span><?= $name ?>: $<?= $data['min'] ?>
                        <?php if ($data['min'] !== $data['max']) {?> - $<?= $data['max'] ?><?php } ?>
                    </span></div>
                <?= str_repeat('<div class="admission-space"></div>', $data['max_offers_by_day']) ?>
            <?php } ?>
        </div>
        <?= $this->render('frame', compact('model', 'prices', 'range')) ?>
    </div>
</div>
