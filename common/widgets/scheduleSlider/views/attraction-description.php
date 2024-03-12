<?= $this->render('info-over-conteiner')?>
<div class="calendar-slider-block">
	<div class="head">
		<h4><?=$model->name?> availability: </h4>
	</div>
	<div class="week-wrap calendar-slider calendar-slider-in-description calendar-slider-in-attraction">
	    <?php if ($this->context->scheduleIsShow) {?>
		<button onclick="return openPopupSchedule(this)" data-href="<?= Yii::$app->urlManager->createUrl([$controller.'/schedule', 'code'=>$model->code])?>" class="more-available-dates">
			<div class="title">More Available Dates</div>
		</button>
		<?php }?>
		<div class="ticket-price">
			<div class="title">TICKETS FROM</div>
			<div class="cost">$ <?=$model->min_rate?></div>
			<link itemprop="url" href="<?= Yii::$app->urlManager->createUrl([$controller.'/schedule', 'code'=>$model->code])?>">
			<span itemprop="potentialAction" itemscope="" itemtype="https://schema.org/BuyAction">
				<div><button onclick="return openPopupSchedule(this)" rel="nofollow" data-href="<?= Yii::$app->urlManager->createUrl([$controller.'/schedule', 'code'=>$model->code])?>" class="btn btn-primary" itemprop="target">Buy now</button></div>
			</span>
		</div>
		<div class="admitions-list">
			<?php foreach($prices as $name => $data) {?>
				<div><?= $name?><br/><strong>$<?= $data['min']?> - $<?= $data['max']?></strong></div>
				<?= str_repeat('<div></div>', $data['max_offers_by_day']-1);?>
			<?php }?>
		</div>
		<?= $this->render('frame', compact('model', 'prices', 'range'));?>
	</div>
</div>
