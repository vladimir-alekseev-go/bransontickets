<?php

use common\models\TrAttractions;
use yii\helpers\Html;

/**
 * @var array                     $priceAll
 * @var common\models\form\Search $Search
 * @var TrAttractions             $model
 */

if (!empty($priceAll[$model->id_external])) {
    foreach ($priceAll[$model->id_external] as $admission => $data) { ?>
        <div class="admission-name">
            <span><?= $admission ?>: $<?= $data['min'] ?>
                <?php if ($data['min'] !== $data['max']) { ?> - $<?= $data['max'] ?><?php } ?>
            </span>
        </div>
        <div class="time d-flex flex-row justify-content-between">
            <?php for ($i = 0; $i < 7; $i++) {
                $date = (new DateTime($Search->dateFrom))->add(new DateInterval('P' . $i . 'D'));
                $has = false;
                foreach ($data['list'] as $day => $types) {
                    if (!empty($types) && $date->format('Md') === $day) {
                        $has = true;
                    }
                }
                ?>
                <div class="tag-block"><?php
                    if ($has) {
                        if (!empty($data['list'][$date->format('Md')][1])) {
                            $hasSpecialPrice = false;
                            foreach ($data['list'][$date->format('Md')][1] as $time => $price) {
                                $hasSpecialPrice = !empty($price['special_rate']) ? true : $hasSpecialPrice;
                            }
                            echo Html::a(
                                'Any Time',
                                $model->getUrl(
                                    [
                                        'tickets-on-date' => $date->format('Y-m-d H:i:s'),
                                        'allotmentId'     => $data['id_external'],
                                        '#'               => 'availability'
                                    ]
                                ),
                                ['class' => 'btn btn-third btn-sm w-100 mb-1' . ($hasSpecialPrice ? ' tag-discount' : '')]
                            );
                        }
                        if (!empty($data['list'][$date->format('Md')][0])) {
                            foreach ($data['list'][$date->format('Md')][0] as $time => $price) {
                                $dt = new DateTime($price['start']);
                                echo Html::a(
                                    $time,
                                    $model->getUrl(
                                        [
                                            'tickets-on-date' => $dt->format('Y-m-d H:i:s'),
                                            'allotmentId'     => $data['id_external'],
                                            '#'               => 'availability',
                                        ]
                                    ),
                                    ['class' => 'btn btn-third btn-sm w-100 mb-1' . ($price['special_rate'] ? ' tag-discount' : '')]
                                );
                            }
                        }
                    } else {
                        ?><span class="btn btn-link btn-sm w-100 cursor-default mb-1">N/A</span>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
<?php } else { ?>
    <div class="time d-flex flex-row justify-content-between">
        <?php for ($i = 0; $i < 7; $i++) { ?>
            <div class="tag-block"><span class="btn btn-link btn-sm w-100 cursor-default mb-1">N/A</span></div>
        <?php } ?>
    </div>
<?php } ?>
