<?php

use backend\models\search\RedirectsSearch;
use webvimark\extensions\GridPageSize\GridPageSize;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\widgets\Pjax;

/**
 * Using by @wlbackend
 *
 * @var RedirectsSearch    $searchModel
 * @var ActiveDataProvider $dataProvider
 */

$this->title = 'Redirects';
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
                        'id',
                        'item_id',
                        'category',
                        'status_code',
                        'old_url',
                        'created_at',
                    ],
                ]
            ) ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>
