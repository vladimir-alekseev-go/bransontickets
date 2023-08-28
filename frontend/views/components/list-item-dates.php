<?php

use common\models\TrAttractions;
use common\models\TrShows;

/**
 * @var array                  $priceAll
 * @var common\models\form\Search $Search
 * @var TrShows|TrAttractions  $model
 */
?>
<div class="dates d-flex flex-row justify-content-between">
    <?php
    $has = [];
    for ($i = 0; $i < 7; $i++) {
        $date = (new DateTime($Search->dateFrom))->add(new DateInterval('P' . $i . 'D'));
        $has[$i] = false;
    }

    if ($model instanceof TrShows) {
        for ($i = 0; $i < 7; $i++) {
            $date = (new DateTime($Search->dateFrom))->add(new DateInterval('P' . $i . 'D'));
            $has[$i] = isset($priceAll[$model->id_external][$date->format('Md')]);
        }
    } elseif (!empty($priceAll[$model->id_external])) {
        foreach ($priceAll[$model->id_external] as $type => $data) {
            for ($i = 0; $i < 7; $i++) {
                $date = (new DateTime($Search->dateFrom))->add(new DateInterval('P' . $i . 'D'));
                foreach ($data['list'] as $day => $price) {
                    if (!empty($price) && $date->format('Md') === $day) {
                        $has[$i] = true;
                    }
                }
            }
        }
    }
    ?>
    <?php for ($i = 0; $i < 7; $i++) {
        $date = (new DateTime($Search->dateFrom))->add(new DateInterval('P' . $i . 'D')); ?>
        <div class="date-block <?php if ($has[$i]) { ?>act<?php } ?>">
            <b><?= $date->format('M d') ?></b>
            <span><?= $date->format('D') ?></span>
        </div>
    <?php } ?>
</div>
