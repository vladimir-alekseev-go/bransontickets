<?php

use common\models\Package;
use common\models\TrAttractions;
use common\models\TrShows;

/**
 * @var array   $result
 * @var Package $package
 * @var         $model
 */

?>
<div class="row row-small-padding">
	<div class="col-5">
	<?php if ($model->preview_id) {?>
		<img src="<?= $model->preview->url?>" />
	<?php }?>
	</div>
	<div class="col-7">
		<div class="title"><?= $model->name?></div>
		<div class="desc"><small>
			<span id="date-packepge">
			<?php if ($package->category === TrShows::TYPE) {?>
				<?= $package->getStartDataTime()->format('l, M d, h:i A')?>
			<?php } else if ($package->category === TrAttractions::TYPE && $package->isAnyTime) {?>
    			Avail dates <?= $package->getStartDataTime()->format('m/d/Y')?> Any Time - <?= $package->getEndDataTime()->format('m/d/Y')?> Any Time
    		<?php } else if ($package->category === TrAttractions::TYPE && !$package->isAnyTime) {?>
    			Tickets on <?= $package->getStartDataTime()->format("l, M d, h:i A")?>
    		<?php } else {?>
    			Avail dates <?= $package->getStartDataTime()->format('m/d/Y h:i A')?> - <?= $package->getEndDataTime()->format('m/d/Y h:i A')?>
    		<?php }?>
			</span>
		</small></div>
	</div>
	
</div>
<div class="row row-small-padding">
	<div class="col-12"><br/></div>
</div>
<div class="row row-small-padding">
	<div class="col-6"><span id="qty"><?= $package->getTicketsQty()?> ticket<?= $package->getTicketsQty() > 1 ? "s" :
        ""?></span></div>
	<div class="col-6 text-end"><span class="cost" id="package-total">$ <?= number_format($package->total,
        2, '.', '')?></span></div>
</div>

<div class="total mb-2">
	<div class="row row-small-padding">
		<div class="col-6"><b>ORDER TOTAL<br>(incl. taxes):</b></div>
		<div class="col-6 text-end"><span id="fullTotalOrderNew" class="cost">$ <?= number_format($result['fullTotalOrderNew'], 2, '.', '')?></span></div>
	</div>
	
	<div class="row row-small-padding">
		<div class="col-6"><b>PREVIOUS TRANSACTION:</b></div>
		<div class="col-6 text-end"><span class="cost" id="modified-total">$ <?= number_format($result['fullTotalOrderCurrent'], 2, '.', '')?></span></div>
	</div>
	<div class="row row-small-padding">
		<div class="col-6"><b>MODIFICATION AMOUNT:</b></div>
		<div class="col-6 text-end"><span id="modified-amound" class="cost red modification-amount">$ <?=
                number_format(abs($result['modifyAmount']), 2, '.', '')?></span></div>
	</div>
</div>
<?php if ($result['modifyAmount'] > 0) {?>
<button onclick="modification.btn = $(this); modification.pay();return false;" id="btn-pay"class="btn btn-primary
btn-green btn-loading-need">Proceed Modification</button>
<?php } else {?>
<button onclick="modification.btn = $(this); modification.confirm();return false;" id="btn-confirm" class="btn
btn-primary btn-green btn-loading-need">Proceed Modification</button>
<?php }?>