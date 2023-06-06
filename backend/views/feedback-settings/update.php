<?php

$this->title = 'Feedback Settings';
$this->params['breadcrumbs'][] = ['label' => 'Feedback Settings', 'url' => ['index']];
?>
<div class="user-update">

    <div class="panel panel-default">
        <div class="panel-body">
            <?= $this->render('_form', compact('model')) ?>
        </div>
    </div>

</div>
