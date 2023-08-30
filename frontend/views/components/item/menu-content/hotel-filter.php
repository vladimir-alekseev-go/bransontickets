<?php

use common\models\form\HotelReservationForm;
use common\models\TrPosHotels;
use frontend\models\SearchHotel;
use yii\bootstrap\ActiveForm;

/**
 * @var HotelReservationForm $ReservationForm
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

<?= $form->field($ReservationForm, 'packageId')->hiddenInput()->label(false) ?>
<div class="name"><?= $ReservationForm->model->name ?> Availability</div>
<div class="row">
    <div class="col-12 col-md-8 col-xl-6">
        <div class="row input-daterange default-range">
            <div class="col-12 col-lg-6 it js-it">
                <div class="row">
                    <div class="col-6 label mb-1">Check in</div>
                </div>
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
            <div class="col-12 col-lg-6 it js-it">
                <div class="row">
                    <div class="col-6 label mb-1">Check out</div>
                </div>
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
    <div class="col-12 col-lg-4 col-xl-4">
        <div class="detail-hotel-filter-rooms d-md-inline-block d-block pt-3">
            <?= $this->render('@app/views/components/filter-rooms', compact('Search')) ?>
        </div>


<?php /*?>
        <?= Html::beginTag('div', ['class' => 'filter-room', 'data-rooms' => $Search->room]) ?>

        <div class="body">
            <div class="layer">
                <div class="rooms">
                    <div class="rows scrollbar-inner filter-rooms js-rooms-list" id="filter-rooms"></div>
                </div>
                <?php if (!$ReservationForm->packageId) { ?>
                    <a href="#" id="filter-rooms-add" class="filter-rooms-add"><span
                            class="icon ibranson-fontawesome-webfont-9"></span> Add Room</a>
                <?php } ?>

            </div>
        </div>
        <?= Html::endTag('div') */?>
    </div>
    <div class="col-12 col-xl-2 text-end mb-3">
        <button class="search-room">Search <?= $SearchButtonName ?? TrPosHotels::NAME_PLURAL ?></button>
    </div>
</div>
<?php if (!$hasForm) {
    ActiveForm::end();
} ?>


