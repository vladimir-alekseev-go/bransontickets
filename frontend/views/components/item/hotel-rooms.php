<?php

use common\models\TrPosHotels;
use common\tripium\TripiumHotelPrice;
use yii\helpers\Url;

/**
 * @var TripiumHotelPrice[]  $rooms
 * @var TrPosHotels          $model
 */

$roomsCount = 0;
?>
<?php if (!empty($rooms)) { ?>
    <div class="rooms-type">
        <?php foreach ($rooms as $room) { ?>
            <div class="it white-block shadow-block js-room" data-room-price="<?= $room->retailRate ?>"
                 data-days-count="1">
                <div class="row">
                    <div class="col-12 col-md-7 mb-3 mb-md-0">
                        <?php if (!empty($room->cover)) { ?>
                            <div class="img">
                                <img src="<?= $room->cover ?>" alt="<?= $room->name ?>">
                            </div>
                        <?php } ?>
                        <div class="title"><?= $room->name ?></div>
                        <i class="fa fa-users"></i> x <?= $room->capacity ?>
                        (Adults: <?= $room->getAge() ?>, Children: <?= count($room->getChildren()) ?>)
                        <?php if ($room->nonRefundable) { ?>
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
                                        <span class="cost">$ <?= $room->retailRate ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <a href="#" class="btn btn-primary w-100 js-reservation-url reservation-url" data-url="<?=
                                Url::to(
                                    [
                                        'lodging/reservation',
                                        'code'   => $model->code,
                                        'hashId' => $room->getHash()
                                    ]
                                ) ?>">
                                    Book now
                                </a>
                                <a class="btn btn-secondary w-100 js-go-to-cart hide" href="<?= Url::to(['order/cart']) ?>">
                                    Go To Cart
                                </a>
                                <a class="js-reservation-cancel btn btn-link reservation-cancel">
                                    <i class="fa fa-xmark"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="reservation-form js-reservation-form"></div>
            </div>
        <?php } ?>
    </div>
<?php } ?>
