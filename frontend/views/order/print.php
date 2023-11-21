<?php

use common\models\TrAttractions;
use common\models\TrOrders;
use common\models\TrPosHotels;
use common\models\TrPosPlHotels;
use common\models\TrShows;
use yii\helpers\ArrayHelper;

/**
 * @var TrOrders         $order
 */

$this->title = 'Print Page';
?>

<div class="print-btn-block">
    <a href="#" class="print-exec" onclick="window.print();">
        <span class="icon ib-print fs-5"></span> Print Page
    </a>
</div>

<div class="print-order-confirmation">
	<div class="print-header">
		<div class="row">
			<div class="col-9">
				<h1>Order Confirmation</h1>
			</div>
			<div class="col-3 text-end">
                <img src="/img/bransontickets-logo.png" class="logo" alt="Branson Tickets" />
			</div>
		</div>
	</div>

	<p>
		Dear <?= $order->userFullName?>.<br/>
		Thank you for your order with <?= $locationServices->name ?? ''?>, we know you will enjoy your vacation in Branson. Missouri.
	</p>
	<p>
		Your Itinerary Reference is: <strong><?= $order->getData()["orderNumber"]?></strong><br/>
		Date Ordered: <strong><?= date("M d, Y", (int)($order->getData()["created"] / 1000))?></strong>
	</p>
	<p>Your vacation itinerary is as follows:</p>

	<table class="table table-bordered table-header">
		<thead>
			<tr>
				<th>Description of Item</th>
				<th class="text-end">SubTotal</th>
				<th class="text-end">Taxes and Convenience Fees</th>
				<th class="text-end">Coupon</th>
				<th class="text-end">Total</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($order->getValidUniqueVacationPackages() as $vacationPackage) {?>
		<tr>
			<td>
				<h6><?= $vacationPackage->name?></h6>
				<p><i>Package Amount: <?= $vacationPackage->count?></i></p>
				<div class="padding">
					<?php foreach ($vacationPackage->getPackages() as $package) {?>
					<h6><?= $package->name?></h6>
					<p>
						<?= $package->type_name?><br/>
						<?php if (isset($package->getItem()->theatre)) {?>
		    				<?= (!empty($package->getItem()->theatre->address1) ? $package->getItem()->theatre->address1 : '')
                            . (!empty($package->getItem()->theatre->city) ? ', '.$package->getItem()->theatre->city : '')
                            . (!empty($package->getItem()->theatre->state) ? ', '.$package->getItem()->theatre->state : '')
                            . (!empty($package->getItem()->theatre->zip_code) ? ', '.$package->getItem()->theatre->zip_code : '') ?>
						<?php }?>
					</p>
					<p>
					<?php if ($package->category === TrShows::TYPE) {?>
            			<div class="date"><?= $package->getItem()::NAME?> date: <?= $package->getStartDataTime()->format('l m/d/Y h:iA')?></div>
            		<?php } else if (!$package->isAnyTime && $package->getItem()::TYPE === TrAttractions::TYPE) {?>
            			<div class="date">Tickets on <?= $package->getStartDataTime()->format('l, M d, h:i A')?></div>
                    <?php } elseif (in_array($package['category'], [TrPosPlHotels::TYPE, TrPosHotels::TYPE], true)) {?>
                        <div class="date">
                            Check in: <?= $package->getStartDataTime()->format('m/d/Y')?> <?= $package->getItem()->getCheckIn() ?><br/>
                            Check out: <?= $package->getEndDataTime()->format("m/d/Y")?> <?= $package->getItem()->getCheckOut() ?>
                        </div>
            		<?php } else {?>
						<div class="date"><?= $package->getItem()::NAME?> dates <?= $package->getStartDataTime()->format('l m/d/Y')?> - <?= $package->getEndDataTime()->format('l m/d/Y')?></div>
					<?php }?>
					<?php foreach ($package->getTickets() as $key => $ticket) {?>
						<?= $ticket->name?> <?= $ticket->description?> <?= $ticket->qty?>
                            <?= $ticket->qty === 1 ? 'ticket' : 'tickets' ?><?= $ticket->seats ? ', seats:'
                                .$ticket->seats : '' ?><br/><?php }?>
					</p>
					<?php if ($package->confirmation) {?><p>Confirmation: <?= $package->confirmation?></p><?php }?>
					<?php if ($package->itinerary_id) {?><p>Itinerary Id: <?= $package->itinerary_id?></p><?php }?>

					<?php }?>
					<p>Special requests are not guaranteed.</p>
				</div>
			</td>
			<td class="text-end"><span class="cost">$ <?= number_format($vacationPackage->getTotal(), 2, '.', '')
                    ?></span></td>
			<td class="text-end"><span class="cost">$ <?= number_format($vacationPackage->getTax(), 2, '.', '')
                    ?></span></td>
			<td class="text-end"><span class="cost">$ <?= number_format(0, 2, '.', '')?></span></td>
			<td class="text-end"><span class="cost">$ <?= number_format($vacationPackage->fullTotal, 2, '.', '')?></span></td>
		</tr>
		<?php }?>

		<?php foreach ($order->getValidPackages() as $package) {?>
		<?php $item = $package->getItem();?>
		<tr>
			<td>
				<h6><?= $package->name?></h6>
				<div class="padding">
					<p>
						<?= $package->type_name?><br/>
						<?php if (isset($package->getItem()->theatre)) {?>
		    				<?= (!empty($package->getItem()->theatre->address1) ? $package->getItem()->theatre->address1 : '')
                            . (!empty($package->getItem()->theatre->city) ? ', '.$package->getItem()->theatre->city : '')
                            . (!empty($package->getItem()->theatre->state) ? ', '.$package->getItem()->theatre->state : '')
                            . (!empty($package->getItem()->theatre->zip_code) ? ', '.$package->getItem()->theatre->zip_code : '') ?>
						<?php }?>
					</p>
					<p>
						<?php if ($package->getComments()){?><div>Comments: <?= $package->getComments()?></div><?php }?>
					</p>

					<?php if ($package->category === TrShows::TYPE) {?>
            			<p class="date"><?= $package->getItem()::NAME?> date: <?= $package->getStartDataTime()->format
                            ("l m/d/Y h:iA")?></p>
            		<?php } else if ($package->getItem()::TYPE === TrAttractions::TYPE && !$package->isAnyTime) {?>
            			<p class="date">Tickets on <?= $package->getStartDataTime()->format('l, M d, h:i A')?></p>
                    <?php } elseif ($package->category === TrPosPlHotels::TYPE) {?>
                        <p>Phone number: <?= $package->hotelPhone?></p>
                        <p>Reservation status: <?= $package->status?></p>
                        <p>
                            Check in: <?= $package->getStartDataTime()->format('m/d/Y')?> <?= $package->getItem()->getCheckIn() ?><br/>
                            Check out: <?= $package->getEndDataTime()->format("m/d/Y")?> <?= $package->getItem()->getCheckOut() ?>
                        </p>
                    <?php } elseif ($package->category === TrPosHotels::TYPE) {?>
                        <p>
                            Check in: <?= $package->getStartDataTime()->format('m/d/Y')?> <?= $package->getItem()->getCheckIn() ?><br/>
                            Check out: <?= $package->getEndDataTime()->format("m/d/Y")?> <?= $package->getItem()->getCheckOut() ?>
                        </p>
            		<?php } else { ?>
						<p class="date">
                            <?= $package->getItem()::NAME?> dates <?= $package->getStartDataTime()->format("l m/d/Y")?> -
                            <?= $package->getEndDataTime()->format("l m/d/Y")?>
                        </p>
					<?php }?>
                    <?php if ($package->tripId) {?><p>Trip Id: <?= $package->tripId?></p><?php }?>
                    <?php if ($package->confirmation) {?><p>Confirmation: <?= $package->confirmation?></p><?php }?>
                    <?php if ($package->itinerary_id) {?><p>Itinerary Id: <?= $package->itinerary_id?></p><?php }?>
					<?php foreach ($package->getTickets() as $key => $ticket) {?>
                        <?php if ($package->category === TrPosPlHotels::TYPE) {?>
                            <?php if ($package->category === TrPosPlHotels::TYPE) {?>
                                <strong>Room <?= $key+1?></strong>: <?= $ticket->name?><br/>
                            <?php }?>

                        <p>Guest Name: <?=trim($ticket->first_name.' '.$ticket->last_name)?>,
							<?= $ticket->qty?> Adult<?= $ticket->qty > 1 ? 's' : ''?>
                            <?= $ticket->child_ages ? count($ticket->child_ages) . ', Children ('.implode('y, ', $ticket->child_ages).'y)' : ''?>
							<?= $ticket->smoking_preference ? ', '.TrPosPlHotels::getSmokingValue($ticket->smoking_preference) : ''?>
							</p>
                            <p>Room number: <?= $ticket->confirmation?></p>
						<?php } else {?>
							<?= $ticket->name?> <?= $ticket->description?> <?= $ticket->qty?> <?= $ticket->qty == 1 ? "ticket" : "tickets"?><?= $ticket->seats ? ", seats:".$ticket->seats : ""?><br/>
                        <?php }?>
                    <?php }?>
					<p>Special requests are not guaranteed.</p>
				</div>
			</td>
                <td class="text-end"><span class="cost">$ <?= number_format($package->total, 2, '.', '')?></span></td>
                <?php if ($package->category === TrPosPlHotels::TYPE) {?>
                    <td class="text-end"><span class="cost">$ <?= number_format($package->tax, 2, '.', '')?></span></td>
                <?php } else {?>
                    <td class="text-end"><span class="cost">$ <?= number_format($package->tax + $package->cancellation_tax + $package->serviceFee, 2, '.', '')?></span></td>
                <?php }?>
                <td class="text-end"><span class="cost">$ <?= number_format($package->coupon, 2, '.', '')?></span></td>
                <td class="text-end"><span class="cost">$ <?= number_format($package->full_total, 2, '.', '')?></span></td>

		</tr>
        <?php if ($package->category === TrPosPlHotels::TYPE && !empty($package->priceLine)) {?>
    <tr>
        <td class="padding text-end line-height" colspan="4">
            <div class="total">
                <p class="it-total">The avg. nightly rate:</p>
                <p class="it-total">Night:</p>
                <p class="it-total">Rooms:</p>
                <p class="it-total">Item Subtotal:</p>
                <p class="it-total">Taxes and Fees:</p>
                <div class="it-total"></div>
                <p class="it-total">Item total:</p>
                <p class="it-total">Insurance Fee:</p>
                <p class="it-total">Processing Fee:</p>
            </div>
        </td>
        <td class="padding text-end line-height">
            <div class="total">
                <p class="it-total"><span class="cost">$&nbsp;<?= number_format($package->priceLine->price, 2, '.', '')?></span></p>
                <p class="it-total"><?= $package->nights?></p>
                <p class="it-total"><?= $package->rooms?></p>
                <p class="it-total"><span class="cost">$&nbsp;<?= number_format($package->total, 2, '.', '')?></span></p>
                <p class="it-total"><span class="cost">$&nbsp;<?= number_format($package->tax, 2, '.', '')?></span></p>
                <p class="it-total"></p>
                <p class="it-total"><span class="cost">$&nbsp;<?= number_format($package->full_total, 2, '.', '')?></span></p>
                <p class="it-total"><span class="cost">$&nbsp;<?= number_format($package->priceLine->displayInsuranceFee, 2, '.', '')?></span></p>
                <p class="it-total"><span class="cost">$&nbsp;<?= number_format($package->priceLine->displayProcessingFee, 2, '.', '')?></span></p>
            </div>
        </td>
    </tr>
		<?php }?>
		<?php }?>
		<tr>
			<th class="padding text-end line-height" colspan="4">
			<div class="total">
				<p class="it-total">SubTotal:</p>
				<p class="it-total">Taxes and Convenience Fees Total:</p>
				<?php if ($order->fullCancellationFee > 0) {?><p class="it-total">Cancellation Fee:</p><?php }?>
				<?php if ($order->processingFee > 0) {?><p class="it-total">Processing Fee:</p><?php }?>
				<?php if ($order->fullDiscount > 0) {?><p class="it-total">Discount:</p><?php }?>
			</div>
			</th>
			<th class="padding text-end line-height">
			<div class="total">
				<p class="it-total"><span class="cost">$&nbsp;<?= number_format($order->validSubTotal, 2, '.', '')?></span></p>
				<p class="it-total"><span class="cost">$&nbsp;<?= number_format($order->fullTax+$order->serviceFee, 2, '.', '')?></span></p>
				<?php if ($order->fullCancellationFee > 0) {?><p class="it-total"><span class="cost">$ <?= number_format($order->fullCancellationFee, 2, '.', '')?></span></p><?php }?>
				<?php if ($order->processingFee > 0) {?><p class="it-total"><span class="cost">$ <?= number_format($order->processingFee, 2, '.', '')?></span></p><?php }?>
				<?php if ($order->fullDiscount > 0) {?><p class="it-total"><span class="cost">$ <?= number_format($order->fullDiscount, 2, '.', '')?></span></p><?php }?>
			</div>
			</th>
		</tr>
		<tr>
			<th colspan="4" class="text-end line-height"><b>Total</b><br><small>(including taxes & fees)</small></th>
			<th class="text-end line-height"><span class="cost">$&nbsp;<?= number_format($order->fullTotal, 2, '.', '')?></span></th>
		</tr>
        <?php if ($order->getResortFee()) {?>
		<tr>
			<th colspan="4" class="text-end line-height">Due at hotel (Resort Fee):</th>
			<th class="text-end line-height"><span class="cost">$&nbsp;<?= number_format($order->getResortFee(), 2, '.', '')?></span></th>
		</tr>
        <?php }?>
		</tbody>
	</table>
<?php $categories = ArrayHelper::getColumn($order->getPackages(), 'category');
if (in_array(TrShows::TYPE, $categories, true)) {?>
        <?php $phone = '1-877-368-3782';?>
    <p>Show dates, times and performances are subject to change without notice.<br>
        If this schedule does not meet your approval or if you would like any additional items added to your itinerary
        , please contact us at <?= $phone ?>.</p>
<?php }?>

<?php
$cancelPolicyText = [];
$checkInInstructions = [];
$specialCheckInInstructions = [];

foreach ($order->getValidPackages() as $package) {
    $policy = !empty($package->getItem()->getCancelPolicyText())
        ? $package->getItem()->getCancelPolicyText()
        : $package->cancellation_policy;
    if (!empty($package->cancelPolicies)) {
        $ar = [];
        foreach ($package->cancelPolicies as $cancelPolicy) {
            $ar[] = $cancelPolicy['names'] . '<br/>' . strip_tags($cancelPolicy['text']);
        }
        $policy = $ar;
    }
    $_data = [
        'name' => $package->getItem()->name,
        'text' => !empty($order->cancellationTextOfVacationPackagesByPackage($package)) ?
            $order->cancellationTextOfVacationPackagesByPackage($package) :
            $policy
    ];
    $cancelPolicyText[md5(serialize($_data))] = $_data;

    if (!empty($package->check_in_instructions)) {
        $checkInInstructions[] = [
            'name' => $package->getItem()->name,
            'text' => $package->check_in_instructions,
        ];
    }

    if (!empty($package->special_check_in_instructions)) {
        $specialCheckInInstructions[] = [
            'name' => $package->getItem()->name,
            'text' => $package->special_check_in_instructions,
        ];
    }
}
foreach ($order->getValidUniqueVacationPackages() as $vacationPackage) {
    if (!empty($vacationPackage->cancellation_text)) {
        $cancelPolicyText[md5($vacationPackage->cancellation_text)] = [
            'name' => $vacationPackage->name,
            'text' => $vacationPackage->cancellation_text,
        ];
    }
}

if (!empty($cancelPolicyText)) {
    ?>
    <div><b>Cancellation policies:</b></div>
    <div class="quote"><?php
    foreach ($cancelPolicyText as $data) {
        ?><p><h4><strong><?= $data['name']?></strong></h4><div><?= is_array($data['text']) ? implode('<br/>',$data['text']) : $data['text']?></div></p><?php
    }
    ?></div><?php
}

if (!empty($checkInInstructions)) {
    ?><div><b>Check-In Instructions:</b></div>
    <div class="qu-ote"><?php
    foreach ($checkInInstructions as $data) {
        ?><div><?= is_array($data['text']) ? implode('<br/>',$data['text']) : $data['text']?></div><?php
    }
    ?></div><?php
}

if (!empty($specialCheckInInstructions)) {
    ?><div><b>Special Check-In Instructions:</b></div>
    <div class="qu-ote"><?php
    foreach ($specialCheckInInstructions as $data) {
        ?><div><?= is_array($data['text']) ? implode('<br/>',$data['text']) : $data['text']?></div><?php
    }
    ?></div><?php
}
?>

    <p>Remember, you will need to present a voucher and ID when checking into your accommodations or picking up show and
        attraction tickets.</p>
    <p>Please note that Tripium will be displayed on your credit card statement as the billing entity.</p>
    <p>Thank you for choosing us for all your vacation planning needs.</p>

</div>
