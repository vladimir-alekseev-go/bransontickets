<?php
use common\models\TrOrders;
use common\models\TrPosHotels;
use common\models\TrShows;
use common\models\TrAttractions;

/**
 * @var TrOrders                                $Order
 * @var TrShows[]|TrPosHotels[]|TrAttractions[] $shows
 */

$this->title = 'Print Page';

$packagesByGroupData = $Order->getValidPackagesByGroupData();
$ticketCount = $Order->getValidTicketsCountByGroupData();
$tickets = $Order->getValidTicketsByGroupData();
?>

<div class="print-btn-block">
    <a href="#" class="print-exec" onclick="window.print();"><span class="icon ib-print fs-5"></span> Print Page</a>
</div>

<div class="print-customer-itinerary">
	<div class="print-header">
		<div class="row">
			<div class="col-9">
				<h1>Itinerary for <?= $Order->userFullName?></h1>
			</div>
			<div class="col-3 text-right">
                <img src="/img/bransontickets-logo.png" class="logo" alt="Branson Tickets" />
			</div>
		</div>
	</div>


	<div class="row">
		<div class="col-8">
		<p>
			Your Itinerary Reference is: <strong><?= $Order->getData()['orderNumber']?></strong><br/>
			Date Ordered: <strong><?= date('M d, Y', (int)($Order->getData()['created'] / 1000))?></strong>
		</p>
		<p><strong>Your vacation itinerary is as follows:</strong></p>

		<table class="table table-bordered table-header">

			<tbody>
			<?php foreach ($packagesByGroupData as $packages) {?>
					<?php $package = $packages[0];?>
					<?php $item = $package->item;?>
		       <tr>
                      <td class="padding">
                      	<h6><?= $package->name?></h6>
						<p>
							<?= $package->type_name?><br/>
							<?php if (isset($package->item->theatre)) {?>
			    				<?= (!empty($package->item->theatre->address1) ? $package->item->theatre->address1 : '') . (!empty($package->item->theatre->city) ? ', '.$package->item->theatre->city : '') . (!empty($package->item->theatre->state) ? ', '.$package->item->theatre->state : '') . (!empty($package->item->theatre->zip_code) ? ', '.$package->item->theatre->zip_code : '') ?><br/>
							<?php }?>

							<?php if ($package->comments) {?><div>Comments: <?= $package->comments?></div><?php }?>

							<?php if ($package->category === TrShows::TYPE) {?>
                    			<div class="date"><?= $package->item::NAME ?> date: <?= $package->getStartDataTime()
                    			->format('l m/d/Y h:iA')?></div>
                    		<?php } else if (!$package->isAnyTime && $package->getItem()::TYPE === TrAttractions::TYPE) {?>
                    			<div class="date">Tickets on <?= $package->getStartDataTime()->format('l, M d, h:i A')?></div>
                            <?php } elseif ($package->category === TrPosHotels::TYPE) {?>
                                <p>
                                    Check in: <?= $package->getStartDataTime()->format('m/d/Y')?> <?= $package->getItem()->getCheckIn() ?><br/>
                                    Check out: <?= $package->getEndDataTime()->format("m/d/Y")?> <?= $package->getItem()->getCheckOut() ?>
                                </p>
                    		<?php } else {?>
        						<div class="date"><?= $package->item::NAME ?> dates <?= $package->getStartDataTime()
                                        ->format('l m/d/Y')?> - <?= $package->getEndDataTime()->format('l m/d/Y')?></div>
        					<?php }?>

							<?php foreach ($tickets[$package->getHashData()] as $ticketHash => $ticket) {?>
							<?php if ($package->category === TrPosHotels::TYPE) {?>
							<?php $key = empty($key) ? 0 : $key;?>
								Room <?= $key+1?>: <?= $ticket->description?><br/>
								<?= trim($ticket->first_name.' '.$ticket->last_name)?>, <br/>
                				<?= $ticket->qty?> Adult<?= $ticket->qty > 1 ? 's' : ''?>,
                                    <?= $ticket->child_ages ? count($ticket->child_ages) . ' Children ' : ''?><?= $ticket->child_ages ? '('.implode('y, ', $ticket->child_ages).'y)' : ''?>

							<?php } else {?>
								<?= $ticket->name?>
								<?= $ticket->description?>
								<?= $ticketCount[$package->getHashData()][$ticketHash]?>
								<?= $ticketCount[$package->getHashData()][$ticketHash] === 1 ? 'ticket' : 'tickets' ?>
								<?= $ticket->seats ? ', seats:' .$ticket->seats : '' ?><br/>
							<?php }?>
							<?php }?>
						</p>
						<?php if ($package->confirmation) {?><p>Confirmation: <?= $package->confirmation?></p><?php }?>
						<p>Special requests are not guaranteed.</p>
                      </td>
                  </tr>

                 <?php }?>
			</tbody>
		</table>
		</div>
		<div class="col-4 text-center">
			<?php if ($shows) {?>
			<p><strong>You May Enjoy These As Well!</strong></p>
			<div class="list-images">
				<?php foreach($shows as $show) {?>
				<div class="it">
					<?php if (!empty($show->preview_id)) {?>
					<a href="<?= $show->getUrl()?>"><img src="<?= $show->preview->url?>" alt=""/></a>
					<?php }?>
					<div class="name"><?= $show->name?></div>
				</div>
				<?php }?>
			</div>
			<?php }?>
		</div>

	</div>
	<p>Remember, you will need to present a voucher and ID when checking into your accommodations or picking up show and attraction tickets.</p>
	<p>Thank you for choosing <?= $locationServices->name ?? 'us'?> for all your vacation planning needs.</p>
</div>
