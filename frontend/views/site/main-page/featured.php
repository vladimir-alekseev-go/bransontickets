<?php

use common\models\ShowsPhotoJoin;
use common\models\TrShows;
use yii\helpers\Html;
use yii\web\JqueryAsset;

/**
 * @var TrShows[] $showsFeatured
 */
?>

<?php if ($showsFeatured) { ?>
    <div class="fixed">
        <div class="featured">Featured</div>
        <div class="featured-items" id="featured-items">
            <div class="week-wrap">
                <div class="frame horizontal">
                    <ul class="clearfix">
                        <?php foreach ($showsFeatured as $show) { ?>
                            <li class="it">
                                <?php
                                    /**
                                     * @var ShowsPhotoJoin $photo
                                     */
                                    $photo = $show->getRelatedPhotos()->orderBy('rand()')->one();
                                ?>
                                <div class="image">
                                    <?php if (!empty($photo->preview)) { ?>
                                        <?= Html::img($photo->preview->url, ['alt' => $show->name]) ?>
                                    <?php } else { ?>
                                        <img src="img/bransontickets-noimage.png">
                                    <?php } ?>
                                </div>
                                <div class="about">
                                    <div class="title"><?= $show->name ?></div>
                                    <div class="location">
                                        <img src="img/location.svg" alt="location icon">
                                        <span><?= $show->theatre->name ?? '' ?></span>
                                    </div>
                                    <div class="featured-line"></div>
                                    <div class="description"><?= strip_tags((strlen($show->description) > 230) ? substr($show->description, 0, 230) . '...' : $show->description) ?></div>
                                </div>
                                <div class="more">
                                    <div class="category">
                                        <img src="img/category.svg" alt="category icon">
                                        <span><?= implode(', ', array_column($show->categories, 'name')) ?? '' ?></span>
                                    </div>
                                    <a href="#" class="item-btn">Book now</a>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="featured-items-nav">
        <div class="fixed-nav">
            <a href="#" class="featured-left"><i class="fa fa-angle-left"></i></a>
            <a href="#" class="featured-right"><i class="fa fa-angle-right"></i></a>
        </div>
    </div>
    <?php $this->registerJsFile('/js/sly.min.js', ['depends' => [JqueryAsset::class]]); ?>
    <?php $this->registerJsFile('/js/featured-items.js', ['depends' => JqueryAsset::class]); ?>
    <?php $this->registerJs('featuredItems.init()'); ?>
<?php } ?>
