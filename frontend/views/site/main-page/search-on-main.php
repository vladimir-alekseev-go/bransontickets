<?php

use frontend\models\SearchOnMainForm;
use yii\bootstrap\ActiveForm;

$model = new SearchOnMainForm();

$this->registerJS(
    "
    $('.js-select-type').on('change', function() {
        $('.js-search-on-main form').attr('action', $(this).val());
        $('.js-submit').attr('disabled', false);
    });
    $('#search-on-main').submit(function(){
        $('.js-submit').attr('disabled', true);
        return true;
    });
"
);
?>
<div class="search-on-main js-search-on-main">
    <?php $form = ActiveForm::begin(
        [
            'class'            => 'form-search-on-main',
            'id'               => 'search-on-main',
            'validateOnBlur'   => true,
            'validateOnSubmit' => true,
            'method' => 'get'
        ]
    ); ?>
    <div class="row form-in">
        <div class="it col-md-4 input-icon-search">
            <?= $form->field($model, 'title')->label(false)
                ->textInput(['placeholder' => "I’m looking for", 'class' => 'form-control']) ?>
        </div>
        <div class="it col-sm-6 col-md-3 input-icon-angle">
            <?= $form->field($model, 'searchType')->dropDownList(
                SearchOnMainForm::types(),
                [
                    'prompt' => 'Select categories',
                    'class'  => 'form-control js-select-type',
                ]
            )->label(false) ?>
        </div>
        <div class="it col-sm-6 col-md-3 input-icon-calendar">
            <?= $form->field(
                $model,
                'dateFrom',
                [
                    'template'     => '{label}{input}{error}{hint}',
                    'inputOptions' => ['class' => 'form-control datepicker text-left', 'autocomplete' => 'off'],
                    'options'      => ['class' => 'field field-datepicker input-calendar form-group']
                ]
            )->textInput(['placeholder' => 'Choose date'])->label(false) ?>
        </div>
        <div class="it col-md-2">
            <button class="btn btn-primary w-100 js-submit">Search</button>
            <p class="help-block help-block-error"></p>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
