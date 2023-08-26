<?php

use common\helpers\General;
use common\models\TrAttractions;
use common\models\TrShows;
use common\models\TrPosHotels;
use common\models\TrPosPlHotels;
use yii\web\JqueryAsset;

/**
 * @var TrShows|TrAttractions $model
 */

$videos = !empty($model->videos) ? explode(";", $model->videos) : [];
$videos = General::handleVideoLink($videos);
$images = $model->relatedPhotos;
?>
<div class="menu-content margin-block">
    <div class="fixed">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation">
                <a href="#overview" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" role="tab" aria-controls="overview" aria-selected="true" class="active">Overview</a>
            </li>
            <li role="presentation">
                <a href="#description" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" role="tab" aria-controls="description" aria-selected="false" class="">Description</a>
            </li>
            <?php if ($model instanceof TrShows || $model instanceof TrAttractions) { ?>
                <li role="presentation">
                    <a href="#schedule" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule" role="tab" aria-controls="schedule" aria-selected="false" class="">Schedule</a>
                </li>
            <?php } ?>
            <?php if (count($images) + count($videos) > 1) { ?>
                <li role="presentation">
                    <a href="#gallery" id="gallery-tab" data-bs-toggle="tab" data-bs-target="#gallery" role="tab" aria-controls="gallery" aria-selected="false" class="">Gallery</a>
                </li>
            <?php } ?>
            <?php /* ?>
            <li role="presentation">
                <a href="#packages" id="packages-tab" data-bs-toggle="tab" data-bs-target="#packages" role="tab" aria-controls="packages" aria-selected="false" class="">Packages</a>
            </li>
            */ ?>
        </ul>
    </div>
    <div class="tab-content">
        <?php if ($model instanceof TrPosHotels || $model instanceof TrPosPlHotels) { ?>
            <?= $this->render('@app/views/components/item/menu-content/overview', compact('model', 'HotelReservationForm', 'showsRecommended', 'videos', 'images')) ?>
        <?php } else { ?>
            <?= $this->render('@app/views/components/item/menu-content/overview', compact('model', 'showsRecommended', 'videos', 'images')) ?>
        <?php } ?>
        <?= $this->render('@app/views/components/item/menu-content/description', compact('model')) ?>
        <?php if ($model instanceof TrShows || $model instanceof TrAttractions) { ?>
            <?= $this->render('@app/views/components/item/menu-content/schedule') ?>
        <?php } ?>
        <?php if (count($images) + count($videos) > 1) { ?>
            <?= $this->render('@app/views/components/item/menu-content/gallery', compact('model', 'videos', 'images')) ?>
        <?php } ?>
        <?php /* ?>
        <?= $this->render('@app/views/components/item/menu-content/packages') ?>
        */ ?>
    </div>
</div>

<?php $this->registerJsFile('/js/nav-tabs-slide.js', ['depends' => [JqueryAsset::class]]); ?>
