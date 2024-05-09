<?= $this->render('info-over-conteiner')?>
<div class="calendar-slider-block">
	<div class="availability">
        <div class="head"><?= $model->name ?> Availability</div>
        <a href="#" class="more-available-dates-head" id="more-available">
            <b>More Available Dates</b> <i class="fa fa-angle-right fa-gradient"></i>
        </b>
    </div>
	<div class="week-wrap calendar-slider calendar-slider-in-order">
		<?= $this->render('frame', compact('model', 'prices', 'range'));?>
	</div>
</div>
