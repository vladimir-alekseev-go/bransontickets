<?php

use common\models\TrShows;
use yii\helpers\Html;

/**
 * @var array                     $priceAll
 * @var common\models\form\Search $Search
 * @var TrShows                   $model
 */
?>
<div class="time d-flex flex-row justify-content-between">
    <?php for ($i = 0; $i < 7; $i++) {
        $date = (new DateTime($Search->dateFrom))->add(new DateInterval('P' . $i . 'D'));

        $times = isset($priceAll[$model->id_external][$date->format('Md')])
            ? (array)$priceAll[$model->id_external][$date->format('Md')] : [];
        ?>
        <div class="tag-block">
            <?php foreach ($times as $time => $t) { ?>
                <?= Html::a(
                    $time,
                    $model->getUrl(['tickets-on-date' => $t["start"], '#' => 'availability']),
                    ['class' => 'tag' . ($t['special_rate'] ? ' tag-discount' : '')]
                ) ?>
            <?php } ?>
            <?php if (empty($times)) { ?><span class="tag tag-n-a">N/A</span><?php } ?>
        </div>
    <?php } ?>
</div>
