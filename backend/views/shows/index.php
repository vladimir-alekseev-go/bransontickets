<?php

use common\models\shows\ShowsSearch;
use yii\grid\SerialColumn;
use yii\grid\ActionColumn;
use webvimark\components\StatusColumn;
use webvimark\extensions\GridPageSize\GridPageSize;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/**
 * @var ShowsSearch $searchModel
 */

$this->title = 'Shows';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="shows-index">

    <div class="panel panel-default">
        <div class="panel-body">

            <div class="row">
                <div class="col-sm-12 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'shows-grid-pjax']) ?>
                </div>
                <div class="clear"><br><br></div>
            </div>

            <?php Pjax::begin(
                [
                    'id' => 'shows-grid-pjax',
                ]
            ) ?>

            <?= GridView::widget(
                [
                    'id' => 'shows-grid',
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
                        'code',
                        'name',
                        [
                            'label' => 'Items group order',
                            'value' => static function ($model) {
                                return /*$model->locationItem ? $model->locationItem->location_name : */null;
                            }
                        ],
                        [
                            'label' => 'Image',
                            'value' => static function ($model) {
                                return $model->image_id ? Html::img(
                                    $model->image->url,
                                    ["width" => 50, "height" => 50]
                                ) : null;
                            },
                            'format' => 'html',
                        ],
                        [
                            'class' => StatusColumn::class,
                            'attribute' => 'display_image',
                        ],
                        [
                            'class' => StatusColumn::class,
                            'attribute' => 'status',
                            'optionsArray' => [
                                [0, Yii::t('yii', $searchModel::getStatusValue(0)), 'warning'],
                                [1, Yii::t('yii', $searchModel::getStatusValue(1)), 'success'],
                            ],
                        ],
                        [
                            'class' => StatusColumn::class,
                            'attribute' => 'show_in_footer',
                        ],
                        'rank_level',
                        'marketing_level',
                        'cut_off',
                        'min_rate',
                        'min_rate_source',
                        'tags',
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
