<?php

use common\models\form\HotelReservationForm;
use common\models\TrPosHotelsPriceRoom;
use common\models\TrPosRoomTypes;
use yii\helpers\Url;

/**
 * @var HotelReservationForm $HotelReservationForm
 * @var TrPosRoomTypes[]     $rooms
 * @var TrPosHotelsPriceRoom $room
 */

$roomTypes = $HotelReservationForm->getRoomTypes()->all();
$roomsCount = 0;
?>
<?php if (!empty($roomTypes)) { ?>
    <div class="rooms-type">
        <?php foreach ($roomTypes as $roomType) { ?>
            <?php $cover = null; ?>
            <?php foreach ($roomType['photos'] as $photo) {
                if (false !== strpos($photo['tags'], 'cover')) {
                    $cover = $photo->preview->url;
                }
            }
            if (!$cover && !empty($roomType['photos'])) {
                $cover = $roomType['photos'][0]->preview->url;
            }
            $rooms = $roomType->getTrPosHotelsPriceRoomsByFilter($HotelReservationForm->searchHotel)->all();
            if (empty($rooms)) {
                continue;
            }

            $roomsCount++;
            ?>
            <?php foreach ($rooms as $room) { ?>
                <div class="it white-block shadow-block js-room" data-room-price="<?= $room->price ?>"
                     data-days-count="<?= $HotelReservationForm->getDaysCount() ?>">
                    <div class="row">
                        <div class="col-12 col-md-7 mb-3 mb-md-0">
                            <?php if (!empty($cover)) { ?>
                                <div class="img">
                                    <img src="<?= $cover ?>" alt="<?= $room->name ?>">
                                </div>
                            <?php } ?>
                            <div class="title"><?= $roomType['name'] ?> - <?= $room->name ?></div>
                            <span class="icon ib-users-alt"></span> x <?= $room->capacity ?>
                            <span class="tag tag-refundable">Refundable</span>
                        </div>
                        <div class="col-12 col-md-5">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="row">
                                        <div class="col-7 col-md-12 text-start text-md-end">Avg rate per night</div>
                                        <div class="col-5 col-md-12 text-end">
                                            <span class="cost">$ <?= $room->price ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <a href="#" class="btn btn-primary w-100 js-reservation-url reservation-url" data-url="<?=
                                    Url::to(
                                        [
                                            'hotel/reservation',
                                            'code' => $HotelReservationForm->model->code,
                                            'roomPriceExternalId' => $room->price_external_id,
                                            $HotelReservationForm->formName() => [
                                                'packageId' => $HotelReservationForm->packageId
                                            ],
                                            'isChange' => (bool)$HotelReservationForm->packageId,
                                            's' => Yii::$app->getRequest()->get('s')
                                        ]
                                    ) ?>">
                                        <?= $HotelReservationForm->packageId ? 'Modify' : 'Book now' ?>
                                    </a>
                                    <a class="js-reservation-cancel btn btn-link reservation-cancel">
                                        <span class="icon ib-x"></span> Cancel
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
