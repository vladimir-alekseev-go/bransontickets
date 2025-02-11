<?php

use common\models\TrPosHotels;
use common\models\TrShows;

/**
 * @var array                     $priceAll
 * @var common\models\form\Search $Search
 * @var yii\data\Pagination       $pagination
 * @var TrShows[]                 $items
 */
$displayListTitle = null;
$currentCity = null;
?>
<?php if (!empty($items)) { ?>
    <div class="container-list">
    <?php foreach ($items as $key => $model) {
        $displayListTitle = $model->status === $model::STATUS_INACTIVE && $displayListTitle !== false ? true : $displayListTitle;
        if ($displayListTitle) {
            $displayListTitle = false; ?>
            <?php if ($pagination->page === 0 && $key === 0) { ?>
                <div class="no-items-found">
                    Sorry, we can't find items on your request, try to change filter criteria.
                </div>
            <?php } ?>
            <div class="it it-empty"></div>
            <div class="it it-empty"></div>
            </div>
            <div class="line-title"><span><?= $model::NAME ?> without availability</span></div>
            <div class="container-list">
        <?php }
        if ($model instanceof TrPosHotels && $currentCity !== $model->city) {?>
        <?php $currentCity = $model->city?>
        <?php /* ?><div class="city-line-title line-title"><span><?= $model->city?></span></div><?php } */ ?>
    <?php }
        echo $this->render(
            '@app/views/components/list-item',
            [
                'model' => $model,
                'priceAll' => $priceAll,
                'Search' => $Search,
            ]
        );
    } ?>
    <?php if ($pagination !== null) { ?>
        <?= $this->render('@common/views/pagination-btn', ['pagination' => $pagination]) ?>
    <?php } ?>

    <div class="it it-empty"></div>
    <div class="it it-empty"></div>
    </div>
<?php } elseif ($this->context->id !== 'lodging' || Yii::$app->request->isAjax) { ?>
    <div class="no-items-found">
        <p>No items found.</p>
        <p>Sorry, we can't find items on your request, try to change filter criteria.</p>
    </div>
<?php } ?>
