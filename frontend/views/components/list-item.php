<?php

use common\models\TrAttractions;
use common\models\TrPosHotels;
use common\models\TrShows;
use yii\helpers\Html;

/**
 * @var array                             $priceAll
 * @var common\models\form\Search         $Search
 * @var yii\data\Pagination               $pagination
 * @var TrShows|TrAttractions|TrPosHotels $model
 */

?>
<div class="it type-<?= $model::TYPE ?><?= $model->isFeatured ? " featured" : "" ?><?= $model->isOnSale ? " on-sale" :
    "" ?><?= $model->status === $model::STATUS_INACTIVE ? ' without-price' : '' ?>">
    <a href="#" class="compare-remove js-compare-remove" data-id-external="<?= $model->id_external?>" data-type="<?=
    $model::TYPE?>"><img src="/img/xmark-blue.svg" alt="xmark icon"></a>
    <?= $this->render('@app/views/components/tags', ['model' => $model, 'class' => 'tags', 'priceAll' => $priceAll]) ?>
    <div class="left">
        <a class="img" href="<?= $model->getUrl() ?>">
            <?php if ($model->preview) { ?>
                <?= Html::img($model->preview->url, ['alt' => $model->name]) ?>
            <?php } else { ?>
                <?= Html::img("/img/bransontickets-noimage.png", ['alt' => $model->name]) ?>
            <?php } ?>
        </a>
    </div>
    <?php if ($model instanceof TrPosHotels) { ?>
        <?php if (!empty($model->rating)) { ?>
            <?= $this->render('@app/views/components/star-rating', ['rating' => floor($model->rating)]) ?>
        <?php } ?>
    <?php } ?>
    <div class="data">
        <div class="texts">
            <div class="title">
                <a href="<?= $model->getUrl() ?>"><?= $model->name ?></a>
            </div>
            <?php if ($model instanceof TrPosHotels) { ?>
                <div class="place"><?= $model->address() ?>, <?= $model->city ?></div>
            <?php } else { ?>
                <?php $theatre = $model->theatre; ?>
                <div class="place"><?= !empty($theatre)
                        ? $theatre->name . ', ' . $theatre->city . ', ' . $theatre->state . ' ' . $theatre->zip_code
                        : ''
                    ?></div>
            <?php } ?>
            <div class="description mb-3 mb-md-0">
                <?= $model->getDescriptionShort(120) ?>
            </div>
        </div>
        <div class="links mb-3">
            <?php if ($model instanceof TrPosHotels) { ?>
                <div class="rows">
                    <div class="row">
                        <div class="col-7 col-sm-5 pt-1 compare-block">
                            <span class="compare-add">
                                <input id="it-<?= $model->id_external ?>" type="checkbox" name="compare[]"
                                       value="<?= $model->id_external ?>" data-type="<?= $model::TYPE ?>"/>
                                <label class="compare-add" for="it-<?= $model->id_external ?>">Add Comparison</label>
                            </span>
                        </div>
                        <div class="col-5 col-sm-4 review-rating">
                            <small class="blue"><i class="fa fa-thumbs-up"></i> Review Rating</small>
                            <div><small class="ms-3 blue">
                                <?= $model->rating ?>
                            </small></div>
                        </div>
                        <div class="col-12 d-none d-sm-block col-sm-3 text-sm-end pt-1 more-detail">
                            <a href="<?= $model->getUrl() ?>">
                                More Detail <i class="icon br-t-points"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <a href="<?= $model->getUrl() ?>" class="description-more">
                    More Detail <i class="icon br-t-points"></i>
                </a>
                <a href="<?= $model->getUrl() ?>" class="me-3 d-md-inline d-none full-schedule">
                <i class="icon br-t-calendar"></i> Full Schedule
                </a>
                <span class="compare-add">
                    <input id="it-<?= $model->id_external ?>" type="checkbox" name="compare[]"
                           value="<?= $model->id_external ?>" data-type="<?= $model::TYPE ?>"/>
                    <label class="compare-add" for="it-<?= $model->id_external ?>">Add Comparison</label>
                </span>
            <?php } ?>
        </div>
    </div>

    <div class="clear-both"></div>

    <div class="left pay-block">
        <?php if ($model->status) { ?>
            <div class="row row-small-padding">
                <div class="col-7 col-md-12">
                    <?php if ($model instanceof TrShows) { ?>
                        <div class="name-ticket">Adult tickets from</div>
                    <?php } elseif ($model instanceof TrAttractions) { ?>
                        <div class="name-ticket">Tickets from</div>
                    <?php } elseif ($model instanceof TrPosHotels) { ?>
                        <div class="name-ticket">Rate per Night</div>
                    <?php } ?>
                    <div class="row row-small-padding mb-1">
                        <div class="col-6">
                            <div class="cost">$ <?= number_format($model->min_rate, '2', '.', '') ?></div>
                        </div>
                        <?php if ($model->min_rate !== $model->min_rate_source) { ?>
                            <div class="col-6 text-end">
                                <div class="cost cost-old">$ <?= number_format(
                                        $model->min_rate_source,
                                        '2',
                                        '.',
                                        ''
                                    ) ?></div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-5 col-md-12">
                    <?php if (!empty($model->getBuyNowUrl())) { ?>
                        <a href="<?= $model->getBuyNowUrl() ?>" class="btn btn-primary w-100">Buy now</a>
                    <?php } elseif ($model instanceof TrPosHotels) { ?>
                        <a href="<?= $model->getUrl() ?>" class="btn btn-primary w-100">Book now</a>
                    <?php } else { ?>
                        <a href="<?= $model->getUrl() ?>" class="btn btn-primary w-100">More</a>
                    <?php } ?>
                </div>
            </div>
        <?php } else { ?>
            <div class="price-one">
                <a href="<?= $model->getUrl() ?>" class="btn btn-secondary w-100"><?= $model::NAME ?> details</a>
            </div>
        <?php } ?>
    </div>
    <div class="data">
        <div class="days">
            <?php if ($model instanceof TrShows || $model instanceof TrAttractions) {
                echo $this->render(
                    '@app/views/components/list-item-dates',
                    [
                        'model' => $model,
                        'priceAll' => $priceAll,
                        'Search' => $Search,
                    ]
                );
            }
            if ($model instanceof TrShows) {
                echo $this->render(
                    '@app/views/components/list-item-times',
                    [
                        'model' => $model,
                        'priceAll' => $priceAll,
                        'Search' => $Search,
                    ]
                );
            } elseif ($model instanceof TrAttractions) {
                echo $this->render(
                    '@app/views/components/list-item-times-and-admissions',
                    [
                        'model' => $model,
                        'priceAll' => $priceAll,
                        'Search' => $Search,
                    ]
                );
            } elseif ($model instanceof TrPosHotels) {
                echo $this->render('@app/views/components/list-item-hotel', compact('model'));
            } ?>
        </div>
    </div>

    <div class="clear"></div>
</div>
