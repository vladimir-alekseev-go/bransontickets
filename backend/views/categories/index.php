<?php

use backend\models\search\TrCategoriesSearch;
/*use common\models\TrAttractions;*/
use common\models\TrCategories;
/*use common\models\TrLunchs;
use common\models\TrPosHotels;*/
use common\models\TrShows;
use webvimark\extensions\GridPageSize\GridPageSize;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use yii\widgets\Pjax;

/**
 * @var TrCategoriesSearch  $searchModel
 * @var ActiveDataProvider $dataProvider
 */

$this->title = 'Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attractions-index">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'categories-grid-pjax']) ?>
                </div>
                <div class="clear"><br><br></div>
            </div>
            <?php Pjax::begin(
                [
                    'id' => 'categories-grid-pjax',
                ]
            ) ?>
            <?= GridView::widget(
                [
                    'id' => 'categories-grid',
                    'dataProvider' => $dataProvider,
                    'layout' => '{items}<div class="row"><div class="col-sm-8">{pager}</div><div class="col-sm-4 text-right">{summary}</div></div>',
                    'filterModel' => $searchModel,
                    'pager' => [
                        'options' => ['class' => 'pagination pagination-sm'],
                        'hideOnSinglePage' => true,
                        'lastPageLabel' => '>>',
                        'firstPageLabel' => '<<',
                    ],
                    'columns' => [
                        [
                            'class' => SerialColumn::class,
                            'options' => ['style' => 'width:10px'],
                        ],
                        [
                            'attribute' => 'id',
                            'value' => static function ($model) {
                                return Html::a($model->id, ['view', 'id' => $model->id], ['data-pjax' => 0]);
                            },
                            'format' => 'raw',
                        ],
                        'id_external',
                        [
                            'attribute' => 'type',
                            'filter' => TrCategoriesSearch::getTypes(),
                            'value' => static function ($model) {
                                /**
                                 * @var TrCategories $model
                                 */
                                $ar = [];
                                if ($model->getTrShowsCategories()->count()) {
                                    $ar[] = TrShows::NAME;
                                }
                                /*if ($model->getTrAttractionsCategories()->count()) {
                                    $ar[] = TrAttractions::NAME;
                                }
                                if ($model->getTrLunchsCategories()->count()) {
                                    $ar[] = TrLunchs::NAME;
                                }
                                if ($model->getTrPosHotelsCategories()->count()) {
                                    $ar[] = TrPosHotels::NAME;
                                }*/
                                return implode(', ', $ar);
                            },
                            'format' => 'raw',
                        ],
                        'name',
                        'sort_shows',
                        /*'sort_attractions',
                        'sort_hotels',
                        'sort_dining',*/
                        [
                            'class' => ActionColumn::class,
                            'contentOptions' => ['style' => 'white-space:nowrap;text-align:center;'],
                            'template' => '{view} {update} {delete}',
                        ],
                    ],
                ]
            ) ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>