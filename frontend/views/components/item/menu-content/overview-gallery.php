<?php

use common\models\TrAttractions;
use common\models\TrShows;
use yii\helpers\Html;
use yii\web\JqueryAsset;

/**
 * @var TrShows|TrAttractions $model
 */

$this->registerJsFile('/js/masonry.pkgd.min.js', ['depends' => [JqueryAsset::class]]);
$this->registerJsFile('/js/lightbox.js', ['depends' => [JqueryAsset::class]]);
$this->registerCssFile('/css/lightbox.css', ['depends' => [JqueryAsset::class]]);
$this->registerJs(
    "
    $(document).ready(function () {
        $('#overview-tab').click();
    });
    
    $('#overview-tab').on('click', function () {
		setTimeout(function () {
            let masonryGrid = $('.masonry-grid').masonry({
                itemSelector: '.grid-item',
                columnWidth: '.grid-sizer',
                percentPosition: true
            });
            setTimeout(function() {
                masonryGrid.masonry('layout')
            }, 200);
            setTimeout(function() {
                masonryGrid.masonry('layout')
            }, 1000);
            setTimeout(function() {
                masonryGrid.masonry('layout')
            }, 3000);
        }, 1);    
	});
"
);

?>

<?php if (count($images) + count($videos) > 1) { ?>
    <div class="overview-gallery">
        <div class="fixed">
            <div class="title">Gallery</div>
        </div>
        <div class="line"></div>
        <div class="fixed">
            <div class="rows">
                <div class="masonry-grid" data-ma-sonry="{ 'itemSelector': '.grid-item'}">
                    <?php foreach ($videos as $video) { ?>
                        <div class="grid-item grid-item--width2">
                            <div>
                                <iframe src="<?= $video ?>" allowfullscreen="allowfullscreen"></iframe>
                            </div>
                        </div>
                    <?php } ?>
                    <?php foreach ($images as $img) { ?>
                        <div class="grid-item">
                            <div><a href="<?= $img->photo->url ?>" data-lightbox="image-1" data-title="<?= $model->name ?>"><?=
                                Html::img ($img->preview->url) ?></a></div>
                        </div>
                    <?php } ?>
                    <div class="grid-sizer"></div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
