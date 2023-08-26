<?php

use common\models\form\SearchHotelGeneral;
use yii\helpers\Html;

/**
 * @var SearchHotelGeneral $Search
 */

?>
    <div class="filter-rooms-description js-filter-rooms-description"></div>
    <a href="#" class="open-detail-room" id="open-detail-room">
        <i class="fa fa-angle-down"></i> Manage Rooms & Guests
    </a>
<?= Html::beginTag('div', ['class' => 'filter-room', 'data-rooms' => $Search->room]) ?>
    <div class="body">
        <div class="layer">
            <div class="rooms">
                <div class="rows scrollbar-inner filter-rooms js-rooms-list" id="filter-rooms"></div>
            </div>
            <a href="#" id="filter-rooms-add" class="filter-rooms-add">
                <i class="fa fa-plus"></i> Add Room
            </a>
        </div>
    </div>
<?= Html::endTag('div') ?>
