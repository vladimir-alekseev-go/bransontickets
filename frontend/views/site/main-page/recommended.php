<?php

use common\models\ShowsPhotoJoin;
use common\models\TrShows;
use yii\helpers\Html;
use yii\web\JqueryAsset;

/**
 * @var TrShows[] $showsRecommended
 */
?>

<?php if ($showsRecommended) { ?>
    <div class="fixed">
        <div class="recommended">Recommended</div>
        <div class="recommended-items" id="recommended-items">
            <div class="week-wrap">
                <div class="frame horizontal">
                    <ul class="clearfix">
                        <?php foreach ($showsRecommended as $show) { ?>
                            <li class="it">
                                <?php
                                    /**
                                     * @var ShowsPhotoJoin $photo
                                     */
                                    $photo = $show->getRelatedPhotos()->orderBy('rand()')->one();
                                ?>
                                <?php if (!empty($photo->preview)) { ?>
                                    <?= Html::img($photo->preview->url, ['alt' => $show->name]) ?>
                                <?php } else { ?>
                                    <img src="img/bransontickets-noimage.png">
                                <?php } ?>
                                <div class="about">
                                    <div class="title"><?= $show->name ?></div>
                                    <div class="location">
                                        <img src="img/location.svg" alt="location icon">
                                        <span><?= $show->theatre->name ?? '' ?></span>
                                    </div>
                                    <div class="recommended-line"></div>
                                </div>
                                <div class="more">
                                    <div class="category">
                                        <img src="img/category.svg" alt="category icon">
                                        <span><?= implode(', ', array_column($show->categories, 'name')) ?? '' ?></span>
                                    </div>
                                    <a href="#" class="item-btn">See details</a>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="recommended-items-nav">
        <div class="fixed-nav">
            <a href="#" class="recommended-left"><i class="fa fa-angle-left"></i></a>
            <a href="#" class="recommended-right"><i class="fa fa-angle-right"></i></a>
        </div>
    </div>

    <div class="fixed">
        <div class="find-more margin-block">
            <a href="#">Find more</a>
        </div>
    </div>
    <?php $this->registerJsFile('/js/sly.min.js', ['depends' => [JqueryAsset::class]]); ?>
    <?php $this->registerJsFile('/js/recommended-items.js', ['depends' => JqueryAsset::class]); ?>
    <?php $this->registerJs('recommendedItems.init()'); ?>
<?php } ?>