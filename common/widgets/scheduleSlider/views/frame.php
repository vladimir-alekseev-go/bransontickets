<?php
use yii\helpers\Json;
use yii\helpers\Url;

use common\models\OrderForm;

$thisClassName = $this->context->className();
$getOrderForm = Yii::$app->request->get(OrderForm::getFormName());

$controller = $model->getType();
if (!empty($this->context->controller)) {
    $controller = $this->context->controller;
}
?>
<div class="frame horizontal calendar-slider-items" id="basic">
	<ul>
	<?php foreach ($range as $dateTime) {?>
		<?php $hasTicket = false;?>
		<?php foreach ($prices as $name => $data) {?>
			<?php if (!empty($prices[$name]) && !empty($prices[$name]['list'][$dateTime->format('YMd')])) {$hasTicket = true;}?>
		<?php }?>
	<li class="it <?php if ($hasTicket) {?>has-ticket<?php }?> <?php if ($this->context->date->format('Y-m-d') == $dateTime->format('Y-m-d')) {?>act active<?php }?>">
		<div class="date"><?= $dateTime->format('M d')?></div>
		<div class="w"><?= $dateTime->format('D')?></div>
		<?php foreach ($prices as $name => $data) {?>
			<?php $cc = 0;?>
			<?php if (!empty($prices[$name]) && !empty($prices[$name]['list'][$dateTime->format('YMd')])) {?>
			    <?php
			    if (!empty($prices[$name]['list'][$dateTime->format('YMd')][$thisClassName::ANY_TIME_YES])) {
			        $cc++;
			        $hash = str_replace(' ','_',$name).'-'.$dateTime->format('Y-m-d');
			        $pricedata = [];
			        $special_rate = false;
			        foreach ($prices[$name]['list'][$dateTime->format('YMd')][$thisClassName::ANY_TIME_YES] as $d) {
			            $special_rate = !empty($d['special_rate']) ? true : $special_rate;
    			        $pricedata[] = [
                            'n'=> urlencode($d['name']),
                            'p'=> urlencode($d['retail_rate']),
                            's'=> urlencode($d['special_rate']),
                            'd'=> urlencode($d['description']),
    			        ];
			        }
			    ?>
					<a class="show-over-info time btn btn-timing <?php
                    if ($dateTime->format('Y-m-d') === $this->context->date->format('Y-m-d')
                        && (int)$prices[$name]['allotmentId'] === (int)Yii::$app->getRequest()->get('allotmentId'))
                        {?>act<?php }?>"
                       href="<?= Url::to([
					    $controller.'/tickets',
					    'code'=>$model->code,
					    OrderForm::getFormName() => $getOrderForm,
					    'date' => $dateTime->format('Y-m-d_00:00:00'),
                        'allotmentId'           => $prices[$name]['allotmentId'] ?? null,
					    '#'=>$hash])?>"
                       data-hash="<?= $hash?>"
                       data-date="<?= $dateTime->format('Y-m-d 00:00:00')?>"
                       data-data='<?= Json::encode($pricedata)?>'
                       data-allotment-id='<?= $prices[$name]['allotmentId'] ?? '' ?>'

                    ><?= $special_rate ? '<b class="special-rate">$</b>' : ''?>Any Time</a>
		        <?php }?>
			    <?php if (!empty($prices[$name]['list'][$dateTime->format('YMd')][$thisClassName::ANY_TIME_NO])) {?>
					<?php foreach ($prices[$name]['list'][$dateTime->format('YMd')][$thisClassName::ANY_TIME_NO] as $time => $d) {
					    $cc++;
					    $date = new \DateTime($d['start']);
					    $hash = str_replace(' ','_',$name).'-'.$date->format('Y-m-d_H:i:s');
					    $pricedata = [];
					    $special_rate = false;
					    foreach ($prices[$name]['list_by_time'][$dateTime->format('YMd')][$time] as $p) {
					        $special_rate = !empty($p['special_rate']) ? true : $special_rate;
    					    $pricedata[] = [
                                'n'=> urlencode($p['name']),
                                'p'=> urlencode($p['retail_rate']),
                                's'=> urlencode($p['special_rate']),
                                'd'=> urlencode($p['description']),
        			        ];
					    }
					    ?>
					<a class="show-over-info time btn btn-timing <?php if ($p['start'] ==
                        $this->context->date->format('Y-m-d H:i:s')) {?>act act-ite-m act-item-grandcountry<?php }?>"
                       href="<?= Url::to([
					    $controller.'/tickets',
					    'code'=>$model->code,
					    OrderForm::getFormName() => $getOrderForm,
					    'date' => $date->format('Y-m-d_H:i:s'),
                        'allotmentId'           => $prices[$name]['allotmentId'] ?? null,
					    '#'=>$hash])?>"
                       data-hash="<?= $hash?>"
                       data-date="<?= $date->format('Y-m-d H:i:s')?>"
                       data-data='<?= Json::encode($pricedata)?>'
                       data-allotment-id='<?= $prices[$name]['allotmentId'] ?? '' ?>'
                    ><?= $special_rate ? '<b class="special-rate">$</b>' : ''?><?= $time?></a>
    		        <?php }?>
		        <?php }?>
		    <?php }?>
			<?php if ($cc < $data['max_offers_by_day']) {?>
				<?= str_repeat('<div class="btn btn-timing-without">Not Available</div>', $data['max_offers_by_day']-$cc);?>
			<?php }?>
		<?php }?>
	</li>
    <?php }?>
	</ul>
</div>