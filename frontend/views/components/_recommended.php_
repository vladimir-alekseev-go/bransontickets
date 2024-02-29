<?php

use common\models\AttractionsPhotoJoin;
use common\models\ShowsPhotoJoin;
use common\models\TrAttractions;
use common\models\TrShows;
use yii\helpers\Html;
use yii\web\JqueryAsset;

/**
 * @var TrShows[]|TrAttractions[] $showsRecommended
 * @var boolean                   $recommended
 */
?>

<?php if ($showsRecommended) { ?>
    <div class="fixed mb-5">
        <?php if (isset($recommended)) { ?>`
            <div class="slider-name mb-3">Recommended</div>
        <?php } ?>
        <div class="recommended-items" id="recommended-items">
            <div class="week-wrap">
                <div class="frame horizontal">
                    <ul class="clearfix">
                        <?php foreach ($showsRecommended as $show) { ?>
                            <li class="it">
                                <div class="image">
                                    <?php if (!empty($show->preview)) { ?>
                                        <div class="img-crop" style="background-image:url(<?= $show->preview->url ?>)">
                                            <?= Html::img($show->preview->url, ['alt' => $show->name]) ?>
                                        </div>
                                    <?php } else { ?>
                                        <div class="img-crop img-crop-no-image"></div>
                                    <?php } ?>
                                </div>
                                <div class="about">
                                    <div class="title"><?= $show->name ?></div>
                                    <div class="location">
                                        <span class="icon br-t-location"></span>
                                        <span><?= $show->theatre->name ?? '' ?></span>
                                    </div>
                                    <div class="featured-line"></div>
                                    <div class="description"><?= $show->getDescriptionShort(120) ?></div>
                                </div>
                                <div class="more">
                                    <div class="category">
                                        <?php foreach (array_slice(array_column($show->categories, 'name'), 0, 4) as $name)
                                        { ?>
                                            <span class="btn btn-third cursor-default text-nowrap"><?= $name ?></span>
                                        <?php } ?>
                                    </div>
                                    <a href="<?= $show->getUrl() ?>" class="btn btn-primary w-100">Book now</a>
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
