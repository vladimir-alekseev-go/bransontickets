<?php

use common\models\StaticPage;

/**
 * @var yii\web\View $this
 * @var StaticPage   $model
 */

$this->title = 'Static page creation';
$this->params['breadcrumbs'][] = ['label' => 'Static pages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="text-page-create">
    <div class="panel panel-default">
        <div class="panel-body">
            <?= $this->render('_form', compact('model')) ?>
        </div>
    </div>
</div>
