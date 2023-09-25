<?= $this->render('info-over-conteiner')?>
<div class="calendar-slider-block">
	<div class="head">
		<h4><?= $model->name?> Availability: </h4>
	</div>
	<div class="week-wrap calendar-slider calendar-slider-in-description">
		<?php if ($this->context->scheduleIsShow) {?>
		<button onclick="return openPopupSchedule(this)" data-href="<?= Yii::$app->urlManager->createUrl(['shows/schedule', 'code'=>$model->code])?>" class="more-available-dates">
			<span class="title">More Available Dates</span>
		</button>
		<?php }?>
		<div class="ticket-price">
			<div class="title">TICKETS FROM</div>
			<div class="cost">$ <?=$model->min_rate?></div>
			<link itemprop="url" href="<?= Yii::$app->urlManager->createUrl(['shows/schedule', 'code'=>$model->code])?>">
			<div itemprop="potentialAction" itemscope="" itemtype="https://schema.org/BuyAction">
				<div><button onclick="return openPopupSchedule(this)" rel="nofollow" data-href="<?= Yii::$app->urlManager->createUrl(['shows/schedule', 'code'=>$model->code])?>" class="btn btn-primary" itemprop="target"><?= Yii::t('app','Buy now')?></button></div>
			</div>
		</div>
		<?= $this->render('frame', compact('model', 'prices', 'range'));?>
	</div>
</div>