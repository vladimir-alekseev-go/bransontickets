<?php

use common\models\FeedbackSubject;
use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View    $this
 * @var FeedbackSubject $model
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Feedback Subjects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="user-visit-log-view">

    <div class="panel panel-default">
        <div class="panel-body">

            <p>
                <?= Html::a('Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-sm btn-primary']) ?>
                <?= Html::a('Create', ['create'], ['class' => 'btn btn-sm btn-success']) ?>

                <?= Html::a(
                    'Delete',
                    ['delete', 'id' => $model->id],
                    [
                        'class' => 'btn btn-sm btn-danger pull-right',
                        'data'  => [
                            'confirm' => 'Are you sure you want to delete this user?',
                            'method'  => 'post',
                        ],
                    ]
                ) ?>
            </p>
            <?= DetailView::widget(
                [
                    'model'      => $model,
                    'attributes' => [
                        'id',
                        'name',
                        'email',
                    ],
                ]
            ) ?>

        </div>
    </div>
</div>
