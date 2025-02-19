<?php

use common\models\TrAttractions;
use common\models\TrShows;
use yii\web\JqueryAsset;

/**
 * @var TrShows|TrAttractions $model
 */
$videos = [];
?><?php
if ($model->relatedPhotos) {
    $this->registerJsFile('/js/lightbox.js', ['depends' => [JqueryAsset::class]]);
    $this->registerCssFile('/css/lightbox.css', ['depends' => [JqueryAsset::class]]);
    ?>
    <div class="fixed">
        <h2 class="text-center">
            Gallery
        </h2>
        <div class="white-block margin-block-small">
            <div class="video-gallery">
                <?php foreach ($videos as $video) { ?>
                    <iframe src="<?= $video ?>" class="mt-3" allowfullscreen="allowfullscreen"></iframe>
                <?php } ?>
            </div>
            <div class="gallery-detail">
                <?php foreach ($model->relatedPhotos as $relatedPhoto) { ?>
                    <a href="<?= $relatedPhoto->photo->getUrl() ?>" data-lightbox="image-1"
                       data-title="<?= $model->name ?>">
                        <img src="<?= $relatedPhoto->preview->getUrl() ?>">
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>
