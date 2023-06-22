<?php

use common\models\TrCategories;
use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var TrCategories $model
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attractions-view">
    <div class="panel panel-default">
        <div class="panel-body">
            <p>
                <?= Html::a(
                    'Edit',
                    ['update', 'id' => $model->id],
                    ['class' => 'btn btn-sm btn-primary']
                ) ?>
            </p>
            <?= DetailView::widget(
                [
                    'model' => $model,
                    'attributes' => [
                        'id',
                        'id_external',
                        'name',
                        'sort_shows',
                        /*'sort_attractions',
                        'sort_hotels',
                        'sort_dining',*/
                    ],
                ]
            ) ?>
        </div>
    </div>
</div>