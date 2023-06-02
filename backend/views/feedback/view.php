<?php

use common\models\Feedback;
use yii\widgets\DetailView;

/**
 * @var Feedback $model
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Feedback Messages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="user-visit-log-view">

    <div class="panel panel-default">
        <div class="panel-body">

            <?= DetailView::widget(
                [
                    'model'      => $model,
                    'attributes' => [
                        'id',
                        'name',
                        'email',
                        'message',
                        [
                            'attribute' => 'subject_id',
                            'value' => $model->subject->name ?? null,
                        ],
                        'created_at',
                    ],
                ]
            ) ?>

        </div>
    </div>
</div>
