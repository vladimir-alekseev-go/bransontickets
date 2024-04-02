<?php

use common\models\OrderForm;
use common\models\TrShows;
use frontend\widgets\scheduleSlider\ScheduleSliderWidget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;

/**
 * @var TrShows              $model
 * @var DatePeriod           $range
 * @var array                $prices
 * @var ScheduleSliderWidget $ScheduleSliderWidget
 */

$this->registerJsFile('/js/sly.min.js', ['depends' => [JqueryAsset::class]]);

$ScheduleSliderWidget = $this->context;

$getOrderForm = Yii::$app->request->get(OrderForm::getFormName());

$controller = $model->getType();
if (!empty($this->context->controller)) {
    $controller = $this->context->controller;
}
?>
<div class="frame horizontal calendar-slider-items" id="basic">
	<ul>
        <?php foreach ($range as $dateTime) {
            $hasTicket = false;
            foreach ($prices as $name => $data) {
                if (!empty($prices[$name]) && !empty($prices[$name]['list'][$dateTime->format('YMd')])) {
                    $hasTicket = true;
                }
            } ?>
	<li class="it <?php if ($hasTicket) {?>has-ticket<?php }?> <?php if ($this->context->date->format('Y-m-d') ===
        $dateTime->format('Y-m-d')) {?>act active<?php }?>" data-date="<?= $dateTime->format('Y-m-d') ?>">
		<div class="date"><?= $dateTime->format('M d')?></div>
		<div class="w"><?= $dateTime->format('D')?></div>
		<?php foreach ($prices as $name => $data) {?>
            <?php if ($model->getType() !== TrShows::TYPE) {?>
                <div class="name-space"></div>
            <?php } ?>
			<?php $cc = 0;?>
			<?php if (!empty($prices[$name]) && !empty($prices[$name]['list'][$dateTime->format('YMd')])) {?>
			    <?php
			    if (!empty($prices[$name]['list'][$dateTime->format('YMd')][ScheduleSliderWidget::ANY_TIME_YES])) {
			        $cc++;
			        $hash = str_replace(' ','_',$name).'-'.$dateTime->format('Y-m-d');
			        $priceData = [];
			        $special_rate = false;
			        foreach ($prices[$name]['list'][$dateTime->format('YMd')][ScheduleSliderWidget::ANY_TIME_YES] as $d) {
			            $special_rate = !empty($d['special_rate']) ? true : $special_rate;
    			        $priceData[] = [
                            'n'=> urlencode($d['name']),
                            'p'=> urlencode($d['retail_rate']),
                            's'=> urlencode($d['special_rate']),
                            'd'=> urlencode($d['description']),
    			        ];
			        }

                    if ($ScheduleSliderWidget->package && $ScheduleSliderWidget->package->getOrder()) {
                        $url = Url::to(
                            [
                                'order/modification-form',
                                'orderNumber' => $ScheduleSliderWidget->package->getOrder()->order_number,
                                'packageNumber' => $ScheduleSliderWidget->package->package_id,
                                'date' => $dateTime->format('Y-m-d 00:00:00'),
                                'allotmentId' => $prices[$name]['allotmentId'] ?? null,
                            ]
                        );
                    } else {
                        $url = Url::to(
                            [
                                $controller . '/tickets',
                                'code'                   => $model->code,
                                OrderForm::getFormName() => $getOrderForm,
                                'date'                   => $dateTime->format('Y-m-d_00:00:00'),
                                'allotmentId'           => $prices[$name]['allotmentId'] ?? null,
                                '#'                      => $hash
                            ]
                        );
                    }
                    ?>
                    <?= Html::a(
                        'Any Time',
                        $url,
                        [
                            'class' => 'js-tag btn btn-third w-100 mb-1 ' . ($special_rate ? ' tag-discount' : ''),
                            'data-href' => $url,
                            'data-date' => $dateTime->format('Y-m-d 00:00:00'),
                            'data-allotment-id' => $prices[$name]['allotmentId'] ?? null,
                        ]
                    ) ?>
		        <?php }?>
			    <?php if (!empty($prices[$name]['list'][$dateTime->format('YMd')][ScheduleSliderWidget::ANY_TIME_NO])) {?>
					<?php foreach ($prices[$name]['list'][$dateTime->format('YMd')][ScheduleSliderWidget::ANY_TIME_NO] as $time => $d) {
					    $cc++;
					    $date = new DateTime($d['start']);
					    $hash = str_replace(' ','_',$name).'-'.$date->format('Y-m-d_H:i:s');
					    $priceData = [];
					    $special_rate = false;
					    foreach ($prices[$name]['list_by_time'][$dateTime->format('YMd')][$time] as $p) {
					        $special_rate = !empty($p['special_rate']) ? true : $special_rate;
    					    $priceData[] = [
                                'n'=> urlencode($p['name']),
                                'p'=> urlencode($p['retail_rate']),
                                's'=> urlencode($p['special_rate']),
                                'd'=> urlencode($p['description']),
        			        ];
					    }
                        if ($ScheduleSliderWidget->package && $ScheduleSliderWidget->package->getOrder()) {
                            $url = Url::to(
                                [
                                    'order/modification-form',
                                    'orderNumber' => $ScheduleSliderWidget->package->getOrder()->order_number,
                                    'packageNumber' => $ScheduleSliderWidget->package->package_id,
                                    'date' => $date->format('Y-m-d H:i:s'),
                                    'allotmentId' => $prices[$name]['allotmentId'] ?? null,
                                ]
                            );
                        } else {
                            $url = Url::to(
                                [
                                    $controller . '/tickets',
                                    'code'                   => $model->code,
                                    OrderForm::getFormName() => $getOrderForm,
                                    'date'                   => $date->format('Y-m-d_H:i:s'),
                                    'allotmentId'           => $prices[$name]['allotmentId'] ?? null,
                                    '#'                      => $hash
                                ]
                            );
                        }
					    ?>
                        <?= Html::a(
                            $time,
                            $url,
                            [
                                'class' => 'js-tag btn btn-third w-100 mb-1' . ($special_rate ? ' tag-discount' : ''),
                                'data-href' => $url,
                                'data-date' => $date->format('Y-m-d H:i:s'),
                                'data-allotment-id' => $prices[$name]['allotmentId'] ?? null,
                            ]
                        ) ?>
    		        <?php }?>
		        <?php }?>
		    <?php }?>
            <?php if ($cc < $data['max_offers_by_day']) { ?>
                <?= str_repeat(
                    '<div class="text-center"><span class="btn btn-link opacity-2 mb-1 cursor-default">N/A</span></div>',
                    $data['max_offers_by_day'] - $cc
                ) ?>
            <?php }?>
		<?php }?>
	</li>
    <?php }?>
	</ul>
</div>
