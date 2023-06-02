<?php

/**
 * @var FeedbackSubjectSearch $searchModel
 * @var ActiveDataProvider    $dataProvider
 */

use backend\models\search\FeedbackSubjectSearch;
use webvimark\extensions\GridPageSize\GridPageSize;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

$this->title = 'Feedback Subjects';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-visit-log-index">

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <p>
                        <?= Html::a(
                            'Create',
                            ['create'],
                            ['class' => 'btn btn-success']
                        ) ?>
                    </p>
                </div>

                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'app-grid-pjax']) ?>
                </div>
            </div>
            <?php Pjax::begin(
                [
                    'id' => 'app-grid-pjax',
                ]
            ) ?>

            <?= GridView::widget(
                [
                    'id'           => 'app-grid',
                    'dataProvider' => $dataProvider,
                    'layout'       => '{items}<div class="row"><div class="col-sm-8">{pager}</div><div class="col-sm-4 text-right">{summary}</div></div>',
                    'filterModel'  => $searchModel,
                    'pager'        => [
                        'options'          => ['class' => 'pagination pagination-sm'],
                        'hideOnSinglePage' => true,
                        'lastPageLabel'    => '>>',
                        'firstPageLabel'   => '<<',
                    ],
                    'columns'      => [
                        [
                            'attribute' => 'id',
                            'value'     => static function ($model) {
                                return Html::a($model->id, ['view', 'id' => $model->id], ['data-pjax' => 0]);
                            },
                            'format'    => 'raw',
                        ],
                        'name',
                        'email',
                        [
                            'class'          => ActionColumn::class,
                            'contentOptions' => ['style' => 'width:70px; text-align:center;'],
                            'template'       => '{view} {update} {delete}',
                        ],

                    ],
                ]
            ) ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>
