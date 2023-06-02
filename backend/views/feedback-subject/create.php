<?php

use webvimark\modules\UserManagement\models\User;
use yii\web\View;

/**
 * @var View $this
 * @var User $model
 */

$this->title = 'Feedback Subject Creation';
$this->params['breadcrumbs'][] = ['label' => 'Feedback Subjects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <div class="panel panel-default">
        <div class="panel-body">
            <?= $this->render('_form', compact('model')) ?>
        </div>
    </div>

</div>
