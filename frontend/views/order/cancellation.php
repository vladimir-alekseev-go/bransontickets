<?php

use common\models\TrAttractions;
use common\models\TrOrders;
use common\models\TrShows;
use yii\bootstrap\Alert;
use yii\helpers\Url;

/**
 * @var TrOrders $order
 * @var array    $cards
 * @var string   $packageNumber
 * @var string   $vacationPackageId
 */

$totalRefund = $order->getData()["fullTotal"];
$processingFee = 4 - $order->getData()["processingFee"];

if ($packageNumber && $package = $order->getPackage($packageNumber)) {
    $totalRefund = $package->getFullTotal();
}
if ($vacationPackageId && $uniqueVacationPackage = $order->getUniqueVacationPackageById($vacationPackageId)) {
    $totalRefund = $uniqueVacationPackage->getFullTotal();
}

?>

<div class="scrollbar-inner">

<h2>Cancellation in order <?= $order->order_number?></h2>
	<div id="popup-errors"></div>
	<?php if ($order->isCallUsToBook()) {?>
        <?php echo Alert::widget([
            'options' => ['class' => 'alert-warning'],
            'closeButton' => false,
            'body' => $order->messageCallUsToBookModification,
        ]);?>
    <?php } else {?>
	
<div class="rows">
<div class="row">
	<div class="col-12 col-md-7">

	<?php if ($processingFee) {?>
		<div class="alert alert-info alert-dismissible"><small><b>bransontickets.com collect transaction fee - $ <?=number_format($processingFee, 2, '.', '')?></b></small></div>
	<?php }?>
		<div class="row">
			<div class="col-4">
                <h5><strong>TOTAL AMOUNT TO REFUND</strong></h5>
			</div>
			<div class="col-8 text-end">
				<span class="cost">$ <?= number_format($totalRefund-$processingFee, 2, '.', '')?></span>
				<span class="refund">Refund to: <b><?php if ($cards) { foreach ($cards as $card) { echo $card."<br/>"; }}?></b></span>
			</div>
		</div>
		
		<p class="note mt-3">Please note:<br>You should receive your refund within 3-15 working days. If there is a
            delay, please check with your issuing bank or contact us for help.</p>

	</div>
	<div class="col-12 col-md-5">
		<div class="cancel-detail">
			<h5><strong>CANCELLATION DETAILS:</strong></h5>
			
			<?php if ($order->getUniqueVacationPackages() && empty($packageNumber)) {?>
				<?php foreach ($order->getUniqueVacationPackages() as $hash => $uniqueVacationPackage) {?>
				<?php if ($uniqueVacationPackage->cancelled ||
                        ($vacationPackageId && $hash !== $order->getGroupHashVacationPackageById($vacationPackageId))
                    ) {
    					continue;
    		    } ?>
				<?php foreach ($uniqueVacationPackage->getPackages() as $package) {?>
				
			<div class="it">
				<div class="row row-small-padding">
					<div class="col-8">
						<div class="title"><?= $package->name?></div>
						<div class="desc"><small><?= $package->getTicketsQty()?> ticket<?= $package->getTicketsQty() > 1 ? "s"
                                    : ""?> - 
							<?php if ($package->category === TrShows::TYPE) {?>
								<?= $package->getStartDataTime()->format('m/d/Y h:i A')?>
							<?php } else if ($package->category === TrAttractions::TYPE && $package->isAnyTime) {?>
                    			Avail dates <?= $package->getStartDataTime()->format('m/d/Y')?> Any Time - <?= $package->getEndDataTime()->format('m/d/Y')?> Any Time
                    		<?php } else if ($package->category === TrAttractions::TYPE && !$package->isAnyTime) {?>
                    			Tickets on <?= $package->getStartDataTime()->format("l, M d, h:i A")?>
                    		<?php } else {?>
                    			Avail dates <?= $package->getStartDataTime()->format('m/d/Y h:i A')?> - <?= $package->getEndDataTime()->format('m/d/Y h:i A')?>
                    		<?php }?>
						</small></div>
					</div>
					<div class="col-4 text-end"><span class="cost">$ <?= number_format($package->full_total,
                        2, '.', '')?></span></div>
				</div>
			</div>
				<?php }?>
				<?php }?>
			<?php }?>
			
			<?php if ($order->packages && empty($vacationPackageId)) {
			    foreach ($order->packages as $package) {
			        if (($packageNumber && $package->package_id !== $packageNumber) || $package->cancelled) {
    					continue;
    				}
			?>
			<div class="it">
				<div class="row row-small-padding">
					<div class="col-8">
						<div class="title"><?= $package->name?></div>
						<div class="description"><small><?= $package->ticketsQty?> ticket<?= $package->ticketsQty > 1 ? "s" : ""?> -
							<?php if ($package->category === TrShows::TYPE) {?>
								<?= $package->getStartDataTime()->format('m/d/Y h:i A')?>
							<?php } else if ($package->category === TrAttractions::TYPE && $package->isAnyTime) {?>
                    			Avail dates <?= $package->getStartDataTime()->format('m/d/Y')?> Any Time - <?= $package->getEndDataTime()->format('m/d/Y')?> Any Time
                    		<?php } else if ($package->category === TrAttractions::TYPE && !$package->isAnyTime) {?>
                    			Tickets on <?= $package->getStartDataTime()->format("l, M d, h:i A")?>
                    		<?php } else {?>
                    			Avail dates <?= $package->getStartDataTime()->format('m/d/Y h:i A')?> - <?= $package->getEndDataTime()->format('m/d/Y h:i A')?>
                    		<?php }?>
						</small></div>
					</div>
					<div class="col-4 text-end"><span class="cost">$ <?= number_format($package->full_total,
                                                                                                2, '.', '')?></span></div>
				</div>
			</div>
			<?php }?>
			<?php }?>
			
			<div class="row row-small-padding">
				<div class="col-7">PACKAGE TOTAL:</div>
				<div class="col-5 text-end"><span class="cost">$ <?= number_format($totalRefund, 2, '.', '')?></span></div>
			</div>
			
			<div class="total">
				<?php if ($processingFee) {?>
					<div class="row row-small-padding">
						<div class="col-7">TRANSACTION FEE:</div>
						<div class="col-5 text-end">- <span class="cost">$ <?= number_format($processingFee, 2, '.', '')?></span></div>
					</div>
				<?php }?>
				<div class="row row-small-padding">
					<div class="col-7"><b>TOTAL REFUND:</b></div>
					<div class="col-5 text-end"><span class="cost">$ <?= number_format($totalRefund-$processingFee, 2, '.', '')?></span></div>
				</div>
			</div>
		</div>
		<div class="btns text-center">
			<?php if ($vacationPackageId) {?>
    		<button onclick="cancellation.btn = $(this); cancellation.orderCancel('<?= Url::to(['order/cancel-vacation-package', 'orderNumber' => $order->order_number, 'vacationPackageId' => $vacationPackageId]) ?>');return false;" class="btn btn-primary btn-loading-need">Proceed</button>
    		<?php } else if ($packageNumber) {?>
    		<button onclick="cancellation.btn = $(this); cancellation.orderCancel('<?= Url::to(['order/cancel-package', 'orderNumber' => $order->order_number, 'packageNumber' => $packageNumber]) ?>');return false;" class="btn btn-primary btn-loading-need">Proceed</button>
    		<?php } else {?>
    		<button onclick="cancellation.btn = $(this); cancellation.orderCancel('<?= Url::to(['order/cancel', 'orderNumber' => $order->order_number]) ?>');return false;" class="btn btn-primary btn-loading-need">Proceed</button>
    		<?php }?>
    		<button onclick="$('#popup-cancel').modal('hide');return false;" class="btn btn-secondary ms-3">
                Cancel
            </button>
		</div>
	</div>
</div>
</div>


<?php }?>

</div>
<?php $this->registerJs("try{ $('.scrollbar-inner').scrollbar();} catch(e) {}");?>