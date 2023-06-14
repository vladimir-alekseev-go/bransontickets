<?php

/**
 * This file uses in other places
 */

use yii\bootstrap\Alert;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

use webvimark\extensions\GridPageSize\GridPageSize;
use webvimark\modules\UserManagement\components\GhostHtml;
use webvimark\modules\UserManagement\UserManagementModule;

$this->title = 'Locations';
?>
<?php if (Yii::$app->session->hasFlash('geocode')) {?>
<?php echo Alert::widget([
    'options' => [
        'class' => 'alert-warning',
    ],
    'body' => Yii::$app->session->getFlash('geocode'),
]);?>
<?php }?>
<div class="attractions-index">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-3">
                	<p><?= Html::a(
			'Update locations coordinate',
	        '',
			['class' => 'btn btn-primary', 'data-method' => 'post']
		) ?></p>
                </div>
                <div class="col-sm-6 text-center">
                <small>Active locations:</small> 
                <span class="label label-primary">has coordinates - <?= $locations-$locationsNotFinded-$locationsEmpty?></span>
                <span class="label label-danger">didn't find coordinates - <?= $locationsNotFinded?></span>
                <span class="label label-warning">in progress - <?= $locationsEmpty?></span>
                </div>
                <div class="col-sm-3 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'attractions-grid-pjax']) ?>
                </div>
                <div class="clear"><br><br></div>
            </div>
            <?php Pjax::begin([
                'id' => 'locations-grid-pjax',
            ]) ?>
            <?= GridView::widget([
                'id' => 'locations-grid',
                'dataProvider' => $dataProvider,
                'layout' => '{items}<div class="row"><div class="col-sm-8">{pager}</div><div class="col-sm-4 text-right">{summary}</div></div>',
                'filterModel' => $searchModel,
                'pager' => [
                    'options' => ['class'=>'pagination pagination-sm'],
                    'hideOnSinglePage' => true,
                    'lastPageLabel' => '>>',
                    'firstPageLabel' => '<<',
                ],
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'options' => ['style' => 'width:10px'],
                    ],
                    'id',
                    'id_external',
                    'name',
                    'address1',
                    'address2',
                    'city',
                    'state',
                    'zip_code',
                    [
                        'class' => 'webvimark\components\StatusColumn',
                        'attribute' => 'status',
                        'optionsArray' => [
                            [0, Yii::t('yii', $searchModel::getStatusValue(0)), 'warning'],
                            [1, Yii::t('yii', $searchModel::getStatusValue(1)), 'success'],
                        ],
                    ],
                    [
                        'class' => 'webvimark\components\StatusColumn',
                        'attribute' => 'location_lat',
                        'optionsArray' => [
                            ['0', "didn't find", 'danger'],
                            [null, 'empty', 'warning'],
                        ],
                    ],
                    [
                        'class' => 'webvimark\components\StatusColumn',
                        'attribute' => 'location_lng',
                        'optionsArray' => [
                            ['0', "didn't find", 'danger'],
                            [null, 'empty', 'warning'],
                        ],
                    ],
                    'location_updated_at'
                ],
            ]) ?>
            
            <?php Pjax::end() ?>
        </div>
    </div>
</div>