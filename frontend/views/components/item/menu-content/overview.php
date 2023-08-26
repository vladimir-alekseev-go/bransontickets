<?php

use common\models\form\HotelReservationForm;
use common\models\TrAttractions;
use common\models\TrShows;
use common\models\TrPosHotels;
use common\models\TrPosPlHotels;
use yii\web\JqueryAsset;

/**
 * @var TrShows|TrAttractions|TrPosHotels|TrPosPlHotels $model
 * @var HotelReservationForm                            $HotelReservationForm
 */

?>

<div id="overview" role="tabpanel" aria-labelledby="overview-tab" class="tab-pane active">
    <div class="fixed">
        <div class="overview-calendar-block">
            <div class="row">
                <div class="col-sm-4 col-md-3 col-lg-2">
                    <div class="ticket">
                        <div class="title">
                            <?php if ($model instanceof TrPosHotels || $model instanceof TrPosPlHotels) { ?>
                                    Per Night
                                <?php } else { ?>
                                    Tickets from
                                <?php } ?>
                            </div>
                        <div class="price">$ 46.39</div>
                        <a href="#" class="btn buy-btn">
                            <?php if ($model instanceof TrPosHotels || $model instanceof TrPosPlHotels) { ?>
                                Book now
                            <?php } else { ?>
                                Buy now
                            <?php } ?>
                        </a>
                    </div>
                </div>
                <div class="col-sm-8 col-md-9 col-lg-10">
                    <?php if ($model instanceof TrPosHotels || $model instanceof TrPosPlHotels) { ?>
                        <?= $this->render(
                            '@app/views/components/item/menu-content/hotel-filter',
                            [
                                'Search' => $HotelReservationForm->searchHotel,
                                'SearchButtonName' => 'Room',
                                'ReservationForm' => $HotelReservationForm
                            ]
                        ) ?>
                        <?php $this->registerJsFile('/js/bootstrap-datepicker.min.js', ['depends' => [JqueryAsset::class]]); ?>
                        <?php $this->registerJsFile('/js/datepicker.js', ['depends' => [JqueryAsset::class]]); ?>
                        <?php $this->registerJsFile('/js/hotel-detail.js', ['depends' => [JqueryAsset::class]]); ?>
                        <?php $this->registerJsFile('/js/hotel.filter.js', ['depends' => [JqueryAsset::class]]); ?>
                        <?php $this->registerJs(
                            'hotelFilter.initHotel($("#show-list"), $("#panel-list"), $("#hotel-filter"), $(".filter-room"), "' .
                            $HotelReservationForm->model->code . '")'
                        ); ?>
                        <?php $this->registerJs('hotelDetail.init()'); ?>

                        <div id="show-list" class="rooms-type-list">
                            <?= $this->render('@app/views/components/item/menu-content/hotel-rooms', compact('HotelReservationForm')) ?>
                        </div>
                    <?php } else { ?>
                        <div class="availability">
                            <div class="head">Availability</div>
                            <a href="#" class="more-available-dates-head">More Available Dates <i class="fa fa-angle-right fa-gradient"></i></a>
                        </div>
                        <div class="week-wrap calendar-slider calendar-slider-in-order">
                            <div class="frame horizontal calendar-slider-items" id="basic">
                                <ul>
                                    <li class="it has-ticket act active" data-date="2023-04-26">
                                        <div class="date">Apr 26</div>
                                        <div class="w">Wed</div>
                                        <a class="show-over-info tag tag-discount" href="#" data-href="#" data-date="2023-04-26 19:30:00">07:30 pm</a>
                                    </li>
                                    <li class="it" data-date="2023-04-27">
                                        <div class="date">Apr 27</div>
                                        <div class="w">Thu</div>
                                        <div class="tag na">N/A</div>
                                    </li>
                                    <li class="it has-ticket" data-date="2023-04-28">
                                        <div class="date">Apr 28</div>
                                        <div class="w">Fri</div>
                                        <a class="show-over-info tag tag-discount" href="#" data-href="#" data-date="2023-04-28 19:30:00">07:30 pm</a>
                                    </li>
                                    <li class="it" data-date="2023-04-29">
                                        <div class="date">Apr 29</div>
                                        <div class="w">Sat</div>
                                        <div class="tag na">N/A</div>
                                    </li>
                                    <li class="it has-ticket" data-date="2023-04-30">
                                        <div class="date">Apr 30</div>
                                        <div class="w">Sun</div>
                                        <a class="show-over-info tag tag-discount" href="#" data-href="#" data-date="2023-04-30 19:30:00">07:30 pm</a>
                                    </li>
                                    <li class="it" data-date="2023-05-01">
                                        <div class="date">May 01</div>
                                        <div class="w">Mon</div>
                                        <div class="tag na">N/A</div>
                                    </li>
                                    <li class="it has-ticket" data-date="2023-05-02">
                                        <div class="date">May 02</div>
                                        <div class="w">Tue</div>
                                        <a class="show-over-info tag tag-discount" href="#" data-href="#" data-date="2023-05-02 19:30:00">07:30 pm</a>
                                    </li>
                                    <li class="it" data-date="2023-05-03">
                                        <div class="date">May 03</div>
                                        <div class="w">Wed</div>
                                        <div class="tag na">N/A</div>
                                    </li>
                                    <li class="it has-ticket" data-date="2023-05-04">
                                        <div class="date">May 04</div>
                                        <div class="w">Thu</div>
                                        <a class="show-over-info tag tag-discount" href="#" data-href="#" data-date="2023-05-04 19:30:00">07:30 pm</a>
                                    </li>
                                    <li class="it" data-date="2023-05-05">
                                        <div class="date">May 05</div>
                                        <div class="w">Fri</div>
                                        <div class="tag na">N/A</div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="fixed">
        <div class="overview-description">
            <div class="row">
                <div class="col-sm-6">
                    <div class="title">Description</div>
                    <div class="description"><?= $model->getDescriptionShort(300) ?></div>
                    <a href="#" class="view-full-description">View full Description <i class="fa fa-angle-right"></i></a>
                </div>
                <div class="col-sm-6">
                    <?php if (!empty($model->voucher_procedure)) { ?>
                        <div class="title">Voucher Exchange</div>
                        <div class="description"><?= $model->voucher_procedure ?></div>
                    <?php } ?>
                    <?php if (!empty($model->getCancellationPolicyText())) { ?>
                        <div class="title">Cancellation policy</div>
                        <div class="description"><?= $model->getCancellationPolicyText() ?></div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <?= $this->render('@app/views/components/item/menu-content/overview-gallery', compact('model', 'videos', 'images')) ?>

    <div class="overview-may-also-like">
        <div class="fixed">
            <div class="may-also-like-title">You may also like</div>
        </div>
        <div class="line"></div>
        <?= $this->render('@app/views/components/recommended', compact('showsRecommended')) ?>
    </div>
</div>
