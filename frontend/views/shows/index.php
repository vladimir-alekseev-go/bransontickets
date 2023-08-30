<?php

use common\models\form\Search;
use common\models\TrPosHotels;
use common\models\TrPosPlHotels;
use common\models\TrShows;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\widgets\LinkPager;

/**
 * @var Search     $Search
 * @var TrShows[]  $items
 * @var array      $priceAll
 * @var Pagination $pagination
 * @var int        $itemCount
 * @var array      $categories
 */


$this->context->layout = 'main-list';
$this->title = constant(get_class($Search->model) . '::NAME_PLURAL');

$this->registerJsFile('/js/page-nav.js', ['depends' => [JqueryAsset::class]]);
$this->registerJsFile('/js/bootstrap-datepicker.min.js', ['depends' => [JqueryAsset::class]]);
$this->registerJsFile('/js/datepicker.js', ['depends' => [JqueryAsset::class]]);
$this->registerJsFile('/js/data-list.js', ['depends' => [JqueryAsset::class]]);
if ($Search->model instanceof TrPosHotels || $Search->model instanceof TrPosPlHotels) {
    $this->registerJsFile('/js/hotel.filter.js', ['depends' => [JqueryAsset::class]]);
    $this->registerJs('hotelFilter.init($("#show-list"), $("#panel-list"), $("#list-filter"), $(".filter-room"))');
} else {
    $this->registerJs('dataList.init($("#show-list"), $("#panel-list"), $(".list-filter"))');
}
?>

<div id="items-list" class="fixed">
    <div class="row">
        <div class="col-lg-3">
            <?php $categories = ArrayHelper::map($categories, 'id_external', 'name'); ?>
            <?= $this->render(
                '@app/views/components/filter',
                compact(
                    'Search',
                    'categories',
                    'rangePrice'
                )
            ) ?>
        </div>
        <div class="col-lg-9">
            <?= $this->render('@app/views/components/panel-sorting', compact('itemCount', 'Search')) ?>
            <div id="show-list" class="show-list margin-block-small show-list-<?= $Search->display ?>">
                <?= $this->render(
                    '@app/views/shows/items',
                    compact(
                        'items',
                        'Search',
                        'priceAll',
                        'pagination',
                    )
                ) ?>
            </div>
            <?php LinkPager::widget(['pagination' => $pagination]); ?>
            <?= $this->render('@common/views/pagination-btn', ['pagination' => $pagination]) ?>
        </div>
    </div>
</div>

<?= $this->render(
    '@app/views/components/popup-compare',
    [
        'types' => $Search->model instanceof TrPosPlHotels
            ? [TrPosPlHotels::TYPE, TrPosHotels::TYPE] : [$Search->model::TYPE],
        'url'   => Url::to([$this->context->id . '/popup-compare'])
    ]
) ?>
