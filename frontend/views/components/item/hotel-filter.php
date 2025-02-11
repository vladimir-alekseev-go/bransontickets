<?php

use common\models\TrPosHotels;
use frontend\models\SearchHotel;
use yii\bootstrap\ActiveForm;

/**
 * @var SearchHotel          $Search
 */

$hasForm = isset($form);
$form = $form ?? ActiveForm::begin(
        [
            'options' => ['class' => 'hotel-filter'],
            'id' => 'hotel-filter',
            'validateOnSubmit' => false,
        ]
    ); ?>

<div class="row align-items-end">
    <div class="col-12 col-lg-6 col-xl-6">
        <div class="row input-daterange default-range align-items-end">
            <div class="col-12 col-sm-6 it js-it">
                <div class="label mb-1">Check in</div>
                <?= $form->field(
                    $Search,
                    "arrivalDate",
                    [
                        'template' => '{label}{input}{error}{hint}',
                        'inputOptions' => ['class' => 'form-control datepicker', 'autocomplete' => 'off'],
                        'options' => ['class' => 'field field-datepicker input-calendar form-group']
                    ]
                )->textInput(['placeholder' => 'Start date'])->label(false) ?>
            </div>
            <div class="col-12 col-sm-6 it js-it">
                <div class="label mb-1">Check out</div>
                <?= $form->field(
                    $Search,
                    "departureDate",
                    [
                        'template' => '{label}{input}{error}{hint}',
                        'inputOptions' => ['class' => 'form-control datepicker', 'autocomplete' => 'off'],
                        'options' => ['class' => 'field field-datepicker input-calendar form-group']
                    ]
                )->textInput(['placeholder' => 'Start date'])->label(false) ?>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6 col-xl-4 mt-3">
        <div class="detail-hotel-filter-rooms d-block pt-3">
            <?= $this->render('@app/views/components/filter-rooms', compact('Search')) ?>
        </div>
    </div>
    <div class="col-12 col-xl-2 mt-3">
        <button class="btn btn-primary w-100">Search <?= $SearchButtonName ?? TrPosHotels::NAME_PLURAL ?></button>
    </div>
</div>
<?php if (!$hasForm) {
    ActiveForm::end();
} ?>


