<?php

use common\models\TrAttractions;
use common\models\TrShows;

/**
 * @var TrShows|TrAttractions $model
 */

?>

<div id="overview" role="tabpanel" aria-labelledby="overview-tab" class="tab-pane active">
    <div class="fixed">
        <div class="overview-calendar-block">
            <div class="row">
                <div class="col-sm-4 col-md-3 col-lg-2">
                    <div class="ticket">
                        <div class="title">TICKETS FROM</div>
                        <div class="price">$ 46.39</div>
                        <a href="#" class="btn buy-btn">Buy now</a>
                    </div>
                </div>
                <div class="col-sm-8 col-md-9 col-lg-10">
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
                    <div class="title">Voucher Exchange</div>
                    <div class="description"><?= $model->voucher_procedure ?></div>
                    <div class="title">Cancellation policy</div>
                    <div class="description"><?= $model->getCancellationPolicyText() ?></div>
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
