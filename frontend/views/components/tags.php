<?php

use common\models\TrAttractions;
use common\models\TrPosHotels;
use common\models\TrPosPlHotels;
use common\models\TrShows;

/**
 * @var TrShows|TrAttractions|TrPosHotels|TrPosPlHotels $model
 * @var string                                          $class
 * @var array|null                                      $priceAll
 */
$discount = false;
$priceAll = $priceAll ?? [];
foreach ($priceAll as $days) {
    foreach ($days as $time) {
        if (!empty($time['list'])) {
            foreach ($time['list'] as $list) {
                foreach ($list as $types) {
                    foreach ($types as $modelArray) {
                        if ((int)$modelArray['main_id_external'] === (int)$model->id_external &&
                            $modelArray['special_rate'] !== null &&
                            $modelArray['special_rate'] !== $modelArray['retail_rate']) {
                            $discount = true;
                        }
                    }
                }
            }
        } else {
            foreach ($time as $modelArray) {
                if ((int)$modelArray['main_id_external'] === (int)$model->id_external &&
                    $modelArray['special_rate'] !== null &&
                    $modelArray['special_rate'] !== $modelArray['retail_rate']) {
                    $discount = true;
                }
            }
        }
    }
}

$class = $class ?? 'tags';
$tags = $model->tags;
$inWishList = isset($model->wishUser) ? $model->wishUser->status : false;
?>

<div class="<?= $class ?>">
    <?php if (!Yii::$app->user->isGuest) { ?>
        <a data-toggle="tooltip" href="#" class="wishlist-toggle"
           title="<?= $inWishList ? 'Remove from wishlist' : 'Add to wishlist' ?>"
           data-item-type="<?= $model::TYPE ?>" data-item-id="<?= $model->id ?>">
            <span class="<?= $inWishList ? 'icon ib-heart-solid' : 'icon ib-heart' ?>"></span>
        </a>
    <?php } ?>
    <?php if (!empty($tags)) { ?>
        <?php foreach (explode(";", $tags) as $tag) {
            if (!empty(trim($tag))) {
                $tagClass = strtolower(str_replace(' ', '-', $tag));
                if (TrShows::TAG_ORIGINAL_FEATURED === $tag) {
                    ?><span title="<?= $tag ?>" class="tag text-uppercase tag-<?= $tagClass ?>"><?=
                TrShows::TAG_ORIGINAL_FEATURED ?></span><?php
                } else {
                    ?><span title="<?= $tag ?>" class="tag tag-empty-label tag-<?= $tagClass ?>"></span><?php
                }
            }
        } ?>
    <?php } ?>
    <?php if ($model->min_rate !== $model->min_rate_source || $discount) { ?>
        <span title="On Sale" class="tag tag-empty-label text-uppercase tag-on-sale"></span>
    <?php } ?>
</div>
