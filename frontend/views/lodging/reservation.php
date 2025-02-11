<?php

use common\models\form\PlHotelReservationForm;
use common\tripium\Tripium;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;

/**
 * @var HotelReserveForm $HotelReserveForm
 * @var TripiumHotelPrice $room
 */

//$warning = Yii::$app->session->getFlash('warning');
?>

<div class="reservation">
    <?php $form = ActiveForm::begin(['id' => 'bookingHotel', 'method' => 'post', 'encodeErrorSummary' => false]); ?>

    <?= $form->errorSummary(
        $HotelReserveForm,
        [
            'header' => '<div class="alert alert-danger alert-dismissible">',
            'footer' => '</div>',
            'encodeErrorSummary' => false
        ]
    ) ?>

    <div class="reservation-rooms">
        <div class="it-room js-it-room">
            <div class="row">
                <div class="col-6 col-sm-5 col-lg-3"><?= $form->field(
                        $HotelReserveForm,
                        'firstName'
                    ) ?></div>
                <div class="col-6 col-sm-5 col-lg-3"><?= $form->field(
                        $HotelReserveForm,
                        'lastName'
                    ) ?></div>
            </div>

            <?php /**foreach ($extras as $extra) { ?>
            <div class="room-extra js-room-extra zero">
            <div class="row align-items-center">
            <div class="col-12 col-5 col-sm-6 col-md-7 col-lg-8">
            <div class="price-title"><?= $extra->name ?></div>
            </div>
            <div class="col-12 col-7 col-sm-6 col-md-5 col-lg-4">
            <div class="row align-items-center">
            <div class="col-4">
            <?php $price = number_format($extra->price, 2, '.', ''); ?>
            <span class="cost" itemprop="price">$ <?= $price ?></span>
            </div>
            <div class="col-4 input-count text-center with-input-field">
            <span class="icon js-input-factor ib-minus in-active" data-factor="-1"></span>
            <span class="icon js-input-factor ib-plus" data-factor="1"></span>
            <?= $form->field(
            $HotelReservationForm,
            $HotelReservationForm::attributeNameExtra(
            $extra->price_external_id,
            $k
            )
            )->label(false)->textInput(
            [
            'type' => 'number',
            'min' => 0,
            'max' => 9,
            'class' => 'js-room-extra-count text-center ps-0 pe-0',
            'price' => $price
            ]
            )
            ?></div>
            <div class="col-4 total-row text-right text-end">
            <span class="cost js-subtotal-cost subtotal-cost">$ 0.00</span>
            </div>
            </div>
            </div>
            </div>
            </div>
            <?php } **/ ?>
        </div>
    </div>

    <div class="hide-show-block">
        <a href="#" class="toggle">
            <i class="fa fa-minus up"></i>
            <i class="fa fa-plus down"></i>
        </a>
        <a href="#" class="title">Special/Accessibility requests</a>
        <div class="data">
            <div>
                <p><strong>Special requests</strong></p>
                <p>Special requests (e.g. Roll-away beds, late check-in) are not guaranteed, and additional charges may
                    apply. We recommend confirming your request with the hotel just to be safe.</p>
                <?= $form->field($HotelReserveForm, 'specialRequests')->textArea(['rows' => 5])->label(false) ?>
            </div>
        </div>
    </div>
    <?php foreach ($HotelReserveForm->selectedRoomPrice->getPolicy() as $policy) { ?>
        <div class="hide-show-block">
            <a href="#" class="toggle">
                <i class="fa fa-minus up"></i>
                <i class="fa fa-plus down"></i>
            </a>
            <a href="#" class="title"><?= $policy['title'] ?></a>
            <div class="data">
                <div>
                    <?php foreach ($policy['paragraph_data'] as $paragraphData) { ?>
                        <p><?= $paragraphData ?></p>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>

    <div class="rows">
        <div class="row">
            <div class="col-7 col-sm-9 col-lg-10 js-room-total text-end">
                <div>Total</div>
                <span class="cost">$ <?= number_format(
                        $HotelReserveForm->selectedRoomPrice->retailRate,
                        2,
                        '.',
                        ''
                    ) ?>
                </span>
            </div>
            <div class="col-5 col-sm-3 col-lg-2">
                <button class="btn btn-primary btn-loading-need w-100">Book Now</button>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php /*?>
<div class="reservation">
	<?php $form = ActiveForm::begin([
		'id' => 'bookingHotel',
		'method'=>'post',
	    'encodeErrorSummary' => false
	]); ?>


    <div class="reservation-rooms">
        <?php for ($k = 0, $kMax = count($HotelReservationForm->rooms); $k < $kMax; $k++) {
            $room = $HotelReservationForm->rooms[$k];
            ?>
            <div class="it-room js-it-room">
                <div class="name"><strong>Room <?= $k + 1 ?>:</strong> <?= $room['adult'] ?>
                    Adults, <?= isset($room['age']) ? count($room['age']) : 0 ?> Children
                </div>

                <div class="row">
                    <div class="col-6 col-sm-5 col-lg-3"><?= $form->field(
                            $HotelReservationForm,
                            PlHotelReservationForm::attributeFirstName($k)
                        ) ?></div>
                    <div class="col-6 col-sm-5 col-lg-3"><?= $form->field(
                            $HotelReservationForm,
                            PlHotelReservationForm::attributeLastName($k)
                        ) ?></div>
                    <div class="col-12 col-sm-10 col-lg-3">
                        <?= $form
                            ->field($HotelReservationForm, PlHotelReservationForm::attributeSmoking($k))
                            ->dropDownList(PlHotelReservationForm::getSmokingList()) ?>
                    </div>
                </div>

                <div>
                    <small>Bedding and Smoking requests cannot be guaranteed.<br/>
                        Please contact the hotel to confirm.
                    </small>
                </div>
            </div>
        <?php } ?>
    </div>

    <div class="hide-show-block">
        <a href="#" class="toggle">
            <i class="fa fa-minus up"></i>
            <i class="fa fa-plus down"></i>
        </a>
        <a href="#" class="title">Special/Accessibility requests</a>
        <div class="data">
            <div>
                <p><strong>Special requests</strong></p>
                <p>Special requests (e.g. Roll-away beds, late check-in) are not guaranteed, and additional charges may
                    apply. We recommend confirming your request with the hotel just to be safe.</p>
                <?= $form->field($HotelReservationForm, 'special_requests')->textArea(['rows' => 5])->label(false) ?>
            </div>
        </div>
    </div>
    <?php if (!empty($roomType['prices'][0]['policy'])) { ?>
        <?php foreach ($roomType['prices'][0]['policy'] as $policy) { ?>
            <div class="hide-show-block">
                <a href="#" class="toggle">
                    <i class="fa fa-minus up"></i>
                    <i class="fa fa-plus down"></i>
                </a>
                <a href="#" class="title"><?= $policy['title'] ?></a>
                <div class="data">
                    <div>
                        <?php foreach ($policy['paragraph_data'] as $paragraphData) { ?>
                            <p><?= $paragraphData ?></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    <?php } ?>

    <div class="rows">
        <div class="row">
            <div class="col-7 col-sm-9 col-lg-10 js-room-total text-end">
                <div><?= count($HotelReservationForm->rooms) ?> Room<?= count(
                        $HotelReservationForm->rooms
                    ) > 1 ? 's' : '' ?></div>

                <span class="cost">$ <?= number_format(
                        $HotelReservationForm->getPriceLine()->displaySubTotal,
                        2,
                        '.',
                        ''
                    ) ?></span>
            </div>
            <div class="col-5 col-sm-3 col-lg-2">
                <button class="btn btn-primary btn-loading-need w-100">Book Now</button>
            </div>
        </div>
    </div>
	<?php ActiveForm::end(); ?>
</div>
*/
