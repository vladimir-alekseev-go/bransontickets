<?php

use common\models\FeedbackSubject;

/**
 * @var FeedbackSubject $model
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Feedback Subjects', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Editing';
?>
<div class="user-update">

    <div class="panel panel-default">
        <div class="panel-body">
            <?= $this->render('_form', compact('model')) ?>
        </div>
    </div>

</div>
