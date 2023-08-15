<?php

use common\models\TrAttractions;
use common\models\TrShows;
use yii\helpers\Html;
use yii\web\JqueryAsset;

/**
 * @var TrShows|TrAttractions $model
 */

$this->registerCssFile('/css/magnific-popup.css', ['depends' => [JqueryAsset::class]]);

?>

<div id="gallery" role="tabpanel" aria-labelledby="gallery-tab" class="tab-pane">
    <div class="fixed">
        <div class="gallery-panel">
            <div class="row">
                <div class="col-sm-12">
                    <div class="popup-gallery">
                        <?php foreach ($videos as $video) { ?>
                            <iframe src="<?= $video ?>" allowfullscreen="allowfullscreen"></iframe>
                        <?php } ?>
                        <?php foreach ($images as $img) { ?>
                            <a href="<?= $img->photo->url ?>" class="image" title="<?= $model->name ?>"><?=
                                Html::img ($img->preview->url) ?>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJsFile('/js/magnific-popup.min.js', ['depends' => [JqueryAsset::class]]); ?>
<?php $this->registerJsFile('/js/gallery.js', ['depends' => [JqueryAsset::class]]); ?>
