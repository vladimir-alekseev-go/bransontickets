<?php

use common\models\form\PlHotelReservationForm;
use common\tripium\Tripium;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;

/**
 * @var PlHotelReservationForm $HotelReservationForm
 */

$model = $HotelReservationForm->model;
$roomType = $HotelReservationForm->getRoomType();

$warning = Yii::$app->session->getFlash('warning');
?>

<div class="reservation">
	<?php $form = ActiveForm::begin([
		'id' => 'bookingHotel',
		'method'=>'post',
	    'encodeErrorSummary' => false
	]); ?>
    <?php if ($HotelReservationForm->basket
        && $HotelReservationForm->basket->tripium
        && (int)$HotelReservationForm->basket->tripium->errorCode === Tripium::STATUS_ONE_HOTEL_PER_ORDER) { ?>
		<?php Modal::begin(['clientOptions' => ['show' => true, 'backdrop' => 'static', 'keyboard' => false]]);?>
        <div class="alert alert-warnin-g"><?= $HotelReservationForm->basket->getErrorSummary(false)[0]?></div>
        <div class="row">
        	<div class="col-xs-6 text-right"><a href="#" onclick="$('[name=\'<?= $HotelReservationForm->formName()?>[agreeOverwriteOrder]\']').prop('checked', true); $('.modal.in').modal('hide'); $('#btn-booking-hotel').trigger('click'); $(window).scrollTop($(body).height()); return false;" class="btn btn-green">OK</a></div>
        	<div class="col-xs-6"><a href="#" class="btn btn-link" data-dismiss="modal">Cancel</a></div>
        </div>
        <?php Modal::end();?>
    <?php } else { ?>
        <?= $form->errorSummary(
            $HotelReservationForm,
            [
                'header' => '<div class="alert alert-danger alert-dismissible">',
                'footer' => '</div>',
                'encodeErrorSummary' => false
            ]
        ) ?>
	<?php }?>
	<div class="agree-overwrite-order">
        <?= $form->field($HotelReservationForm, 'agreeOverwriteOrder', [])
            ->checkbox(
                [
                    'template' => '{input}{beginLabel}{labelTitle}{endLabel}{hint}',
                    'value' => 1,
                    'label' => 'agree overwrite order',
                    'uncheck' => '0'
                ]
            ) ?>
    </div>

    <?= $form->field($HotelReservationForm, 'ppnBundle')->hiddenInput()->label(false)?>

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
