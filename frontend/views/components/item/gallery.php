<?php

use common\models\TrAttractions;
use common\models\TrShows;
use yii\helpers\Html;
use yii\web\JqueryAsset;

/**
 * @var TrShows|TrAttractions $model
 * @var array $videos
 * @var array $images
 */

?>
<?php if (count($images) + count($videos) > 1) { ?>
<h2 class="text-center text-uppercase mb-4 fw-bold">Gallery</h2>
<div class="fixed">
    <div class="shadow-block margin-block white-block js-popup-gallery">
        <div class="popup-gallery">
            <?php foreach ($images as $img) { ?>
                <a href="<?= $img->photo->url ?>" class="image" title="<?= $model->name ?>"><?=
                    Html::img ($img->preview->url) ?>
                </a>
            <?php } ?>
        </div>
        <div class="video-gallery">
            <?php foreach ($videos as $video) { ?>
                <iframe src="<?= $video ?>" class="mt-3" allowfullscreen="allowfullscreen"></iframe>
            <?php } ?>
        </div>
        <div class="text-center mt-2 d-block d-md-none">
            <a href="#" onclick="$('.js-popup-gallery').toggleClass('popup-gallery-full'); return false;">
                <span class="view-more">View more <span class="icon br-t-points"></span></span>
                <span class="view-more-hide d-none">Hide <span class="icon br-t-points"></span></span>
            </a>
        </div>
    </div>
</div>
<?php $this->registerCssFile('/css/magnific-popup.css', ['depends' => [JqueryAsset::class]]); ?>
<?php $this->registerJsFile('/js/magnific-popup.min.js', ['depends' => [JqueryAsset::class]]); ?>
<?php $this->registerJsFile('/js/gallery.js', ['depends' => [JqueryAsset::class]]); ?>
<?php } ?>
