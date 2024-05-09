<?php

use common\models\TrAttractions;

?>
<?= $this->render('info-over-conteiner')?>
<?php $controller = $model->getType() == TrAttractions::TYPE ? 'attractions' : 'lunchs';?>
<div class="calendar-slider-block">
	<div class="availability">
        <div class="head"><?= $model->name ?> Availability</div>
        <a href="#" class="more-available-dates-head" id="more-available">
            <b>More Available Dates</b> <i class="fa fa-angle-right fa-gradient"></i>
        </a>
    </div>
	<div class="week-wrap calendar-slider calendar-slider-in-order calendar-slider-in-order-attraction">
		<div class="admitions-list">
			<?php foreach($prices as $name => $data) {?>
				<div><?= $name?><br/><strong>$<?= $data['min']?> - $<?= $data['max']?></strong></div>
				<?= str_repeat('<div></div>', $data['max_offers_by_day']-1)?>
			<?php }?>
		</div>
		<?= $this->render('frame', compact('model', 'prices', 'range'))?>
	</div>
</div>
