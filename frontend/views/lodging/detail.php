<?php

use common\models\form\SearchPosHotel;
use common\models\TrPosHotels;
use frontend\controllers\BaseController;
use yii\web\JqueryAsset;

/**
 * @var TrPosHotels $model
 * @var SearchPosHotel $searchHotel
 */

$this->context->layout = BaseController::LAYOUT_ITEM_DETAIL;
Yii::$app->view->params['model'] = $model;

$this->title = $model->name;

?>

<div class="fixed">
    <div class="menu-content margin-block white-block shadow-block">
        <div class="overview-calendar-block">
            <?= $this->render(
                '@app/views/components/item/hotel-filter',
                [
                    'Search'           => $searchHotel,
                    'SearchButtonName' => 'Room',
                ]
            ) ?>
            <?php $this->registerJsFile(
                '/js/bootstrap-datepicker.min.js',
                ['depends' => [JqueryAsset::class]]
            ); ?>
            <?php $this->registerJsFile('/js/datepicker.js', ['depends' => [JqueryAsset::class]]); ?>
            <?php $this->registerJsFile('/js/hotel-detail.js', ['depends' => [JqueryAsset::class]]); ?>
            <?php $this->registerJsFile('/js/hotel.filter.js', ['depends' => [JqueryAsset::class]]); ?>
            <?php $this->registerJs(
                'hotelFilter.initHotel($("#show-list"), $("#panel-list"), $("#hotel-filter"), $(".filter-room"), "' .
                $model->code . '")'
            ); ?>
            <?php $this->registerJs('hotelDetail.init()'); ?>
        </div>
        <div id="show-list" class="rooms-type-list">
            <?php //$this->render('@app/views/components/item/menu-content/hotel-rooms', compact('HotelReservationForm')) ?>
        </div>
        <?php /**  if (!empty($model->vacationPackages)) { ?>
            <div id="packages" role="tabpanel" aria-labelledby="packages-tab" class="tab-pane">
                <?= $this->render('@app/views/components/item/menu-content/packages', compact('VPLWidget')) ?>
            </div>
        <?php }*/ ?>
    </div>
</div>
<?php
$this->registerJsFile('/js/lightbox.js', ['depends' => [JqueryAsset::class]]);
$this->registerCssFile('/css/lightbox.css', ['depends' => [JqueryAsset::class]]);
?>
<div class="fixed">
    <h2 class="text-center">
        Gallery
    </h2>
    <div class="white-block margin-block-small gallery-detail">
        <?php foreach ($model->relatedPhotos as $relatedPhoto) { ?>
            <a href="<?= $relatedPhoto->preview->getUrl() ?>"  data-lightbox="image-1" data-title="<?= $model->name?>">
            <img src="<?= $relatedPhoto->preview->getUrl() ?>" >
            </a>
        <?php } ?>
    </div>
</div>

