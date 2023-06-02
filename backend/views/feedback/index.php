<?php

use backend\models\search\FeedbackSearch;
use common\models\Feedback;
use common\models\FeedbackSubject;
use webvimark\extensions\GridPageSize\GridPageSize;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\Pjax;

/**
 * @var FeedbackSearch     $searchModel
 * @var ActiveDataProvider $dataProvider
 */

$this->title = 'Feedback Messages';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-visit-log-index">

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                    <p>
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
                            'value'     => static function (Feedback $model) {
                                return Html::a($model->id, ['view', 'id' => $model->id], ['data-pjax' => 0]);
                            },
                            'format'    => 'raw',
                        ],
                        [
                            'attribute' => 'subject_id',
                            'label'     => 'Subject',
                            'value'     => static function (Feedback $model) {
                                return $model->subject ? Html::a(
                                    $model->subject->name,
                                    [
                                        'feedback-subject/view',
                                        'id' => $model->subject->id
                                    ],
                                    ['data-pjax' => 0]
                                ) : null;
                            },
                            'format'    => 'raw',
                            'filter'    => ArrayHelper::map(FeedbackSubject::find()->asArray()->all(), 'id', 'name'),
                        ],
                        'name',
                        'email',
                        'message',
                        'created_at',
                        [
                            'class'          => ActionColumn::class,
                            'contentOptions' => ['style' => 'width:70px; text-align:center;'],
                            'template'       => '{view}',
                        ],
                    ],
                ]
            ) ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>
