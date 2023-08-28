<?php

use yii\widgets\ActiveForm;

$Search = $this->params['view']['search'];
?>
<?php
$form = ActiveForm::begin(
    [
        'options' => ['class' => 'list-filter-up'],
        'id' => 'list-filter-up',
        'validateOnSubmit' => false,
    ]
); ?>
<?= $form
    ->field($Search, "title")
    ->textInput(['placeholder' => 'Start typing ' . $Search->searchName() . ' name', 'id' => 'filter-by-name'])
    ->label(false) ?>
<?php ActiveForm::end(); ?>
