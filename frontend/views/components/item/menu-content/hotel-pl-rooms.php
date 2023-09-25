<?php

use common\models\form\PlHotelReservationForm;
use yii\helpers\Url;

/**
 * @var PlHotelReservationForm $ReservationForm
 */

$model = $ReservationForm->model;
?>

<?php if (!empty($ReservationForm->getRoomTypes())) { ?>
    <div class="rooms-type">
        <?php foreach ($ReservationForm->getRoomTypes() as $roomType) {
            foreach ($roomType['prices'] as $key => $price) { ?>
                <div class="it white-block shadow-block js-room" data-room-price="<?= $price['retailRate'] ?>"
                     data-days-count="<?= $ReservationForm->getDaysCount()  ?>">
                    <div class="row">
                        <div class="col-12 col-md-7 mb-3 mb-md-0">
                            <?php if (!empty($model->preview->url)) { ?>
                                <div class="img">
                                    <img src="<?= $model->preview->url ?>" alt="<?= $roomType['name'] ?>">
                                </div>
                            <?php } ?>
                            <div class="title"><?= $roomType['name'] ?> - <?= $roomType['name'] ?></div>
                            <i class="fa fa-users"></i> x <?= $roomType['capacity'] ?>
                            <?php if ($price['nonRefundable']) { ?>
                                <span class="tag tag-non-refundable">Non Refundable</span>
                            <?php } else { ?>
                                <span class="tag tag-refundable">Refundable</span>
                            <?php } ?>
                        </div>
                        <div class="col-12 col-md-5">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="row">
                                        <div class="col-7 col-md-12 text-start text-md-end">Avg rate per night</div>
                                        <div class="col-5 col-md-12 text-end">

                                            <span class="cost">$ <?= number_format(
                                                    $price['retailRate'],
                                                    2,
                                                    '.',
                                                    ''
                                                ) ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <a href="#" class="btn buy-btn w-100 js-reservation-url reservation-url <?=
                                    $ReservationForm->basket->hasHotel() && $ReservationForm->inBasket($roomType)
                                        ? 'reservation-in-basket' : ''?>"
                                       data-url="<?= Url::to(
                                           [
                                               'pl-hotel/reservation',
                                               'code' => $model->code,
                                               'id' => $roomType['id'],
                                               'ppnBundle' => $price['ppnBundle'],
                                               $ReservationForm->formName() => [
                                                   'packageId' => $ReservationForm->packageId,
                                               ]
                                           ]
                                       ) ?>">
                                        <?php if ($ReservationForm->basket->hasHotel()) { ?>
                                            <?= $ReservationForm->inBasket($roomType) ? 'Modify' : 'Change room' ?>
                                        <?php } else { ?>
                                            Book now
                                        <?php } ?>
                                    </a>
                                    <a class="js-reservation-cancel btn btn-link reservation-cancel">
                                        <img src="/img/xmark-blue.svg" alt="xmark icon"></span> Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="reservation-form js-reservation-form"></div>
                </div>
            <?php } ?>

        <?php } ?>
    </div>
<?php } ?>
