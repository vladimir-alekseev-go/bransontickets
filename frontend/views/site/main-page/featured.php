<?php

use common\models\TrAttractions;
use common\models\TrShows;
use yii\helpers\Html;
use yii\web\JqueryAsset;

/**
 * @var TrShows[]|TrAttractions[] $showsFeatured
 * @var string                    $id
 * @var bool                      $showDescription
 */
?>

<?php if ($showsFeatured) { ?>
    <div class="fixed mb-5">
        <div class="featured-items js-featured-items" id="<?= $id ?>">
            <div class="week-wrap">
                <div class="frame horizontal">
                    <ul class="clearfix">
                        <?php foreach ($showsFeatured as $show) { ?>
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
                                    <?php if ($showDescription) { ?>
                                        <div class="description"><?= $show->getDescriptionShort(120) ?></div>
                                    <?php } ?>
                                </div>
                                <div class="more">
                                    <div class="category">
                                        <?php foreach (array_slice(array_column($show->categories, 'name'), 0, 4) as $name)
                                        { ?>
                                            <span class="btn btn-third btn-sm cursor-default text-nowrap"><?= $name
                                                ?></span>
                                        <?php } ?>
                                    </div>
                                    <a href="<?= $show->getUrl() ?>" class="btn btn-primary w-100">Book now</a>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="featured-items-nav">
                    <div class="fixed-nav">
                        <a href="#" class="featured-left"><i class="fa fa-angle-left"></i></a>
                        <a href="#" class="featured-right"><i class="fa fa-angle-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $this->registerJsFile('/js/sly.min.js', ['depends' => [JqueryAsset::class]]); ?>
    <?php $this->registerJsFile('/js/featured-items.js', ['depends' => JqueryAsset::class]); ?>
    <?php $this->registerJs('featuredItemsCreate.create("' . $id . '")'); ?>
<?php } ?>
