<?php

use common\helpers\ActiveForm;
use common\models\form\SearchPosHotel;
use common\models\TrAttractions;
use common\models\TrPosHotels;
use common\models\TrShows;
use common\models\form\Search;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * @var array $rangePrice
 * @var array $categories
 * @var Search $Search
 */

$model = $Search->model ? $Search->model->className() : null;

?>
    <div class="filter-applied">
        <div class="open" id="list-filter-open">Edit Filters <i class="fa fa-filter"></i></div>
        <div class="title">Filter applied</div>
        <div class="list" id="filter-applied-list"></div>
    </div>
<?php $form = ActiveForm::begin(
    [
        'options' => ['class' => 'list-filter'],
        'id' => 'list-filter',
        'validateOnSubmit' => false,
    ]
); ?>
<div class="list-filter-header">
    <div class="list-filter-close" id="list-filter-close"><img src="/img/xmark.svg" alt="xmark icon"></div>
    <div class="list-filter-title">Edit Filter</div>
</div>
<div class="list-filter-body">
    <div class="scrollbar-inner">
        <div class="list-filter-body-hidden">
            <?= $form->field($Search, "fieldSort")->hiddenInput()->label(false) ?>
            <?= $form->field($Search, "display")->hiddenInput()->label(false) ?>
            <?= $form->field($Search, "priceFrom")->hiddenInput()->label(false) ?>
            <?= $form->field($Search, "priceTo")->hiddenInput()->label(false) ?>
            <?php if ($Search->model instanceof TrShows || $Search->model instanceof TrAttractions) { ?>
                <?= $form->field($Search, "timeFrom")->hiddenInput()->label(false) ?>
                <?= $form->field($Search, "timeTo")->hiddenInput()->label(false) ?>
            <?php } ?>
            <?= $form->field($Search, "title")->hiddenInput()->label(false) ?>
            <?php if ($Search->model) { ?>
    <div class="input-daterange row row-small-padding">
        <div class="it js-it col-xs-6">
            <label class="control-label"><?= $Search->model instanceof TrPosHotels
                    ? 'Check In' : 'Start Date'?></label>
            <?= $form->field(
                $Search,
                $Search->model instanceof TrPosHotels ? 'arrivalDate' : 'dateFrom',
                [
                    'template' => '{label}{input}{error}{hint}',
                    'inputOptions' => ['class' => 'form-control datepicker text-left', 'autocomplete' => 'off'],
                    'options' => ['class' => 'field field-datepicker input-calendar form-group']
                ]
            )->textInput(['placeholder' => 'Select start date'])->label(false) ?>
        </div>
        <div class="it js-it col-xs-6">
            <label class="control-label"><?= $Search->model instanceof TrPosHotels
                    ? 'Check Out' : 'End Date'?></label>
            <?= $form->field(
                $Search,
                $Search->model instanceof TrPosHotels ? 'departureDate' : 'dateTo',
                [
                    'template' => '{label}{input}{error}{hint}',
                    'inputOptions' => ['class' => 'form-control datepicker text-left', 'autocomplete' => 'off'],
                    'options' => ['class' => 'field field-datepicker input-calendar form-group']
                ]
            )->textInput(['placeholder' => 'Select start date'])->label(false) ?>
        </div>
    </div>
            <?php } ?>
<?php if ($Search->model && $Search->model instanceof TrShows) { ?>
    <div class="timerange it">
        <?= $model::name ?> times:
        <span class="time"><b id="time-from">11:00AM</b>-<b id="time-to">9:00PM</b></span>
        <div id="time-range" data-value-from="<?= $Search->timeFrom ?>" data-value-to="<?= $Search->timeTo ?>"
             data-min="8" data-max="23"></div>
        <div class="slider-range-grid">
            <div class="slider-mark-left">8:00 AM</div>
            <div>1:00 PM</div>
            <div>6:00 PM</div>
            <div class="slider-mark-right">11:00 PM</div>
        </div>
    </div>
<?php } ?>

            <?php if (!($Search instanceof SearchPosHotel)) { ?>
                <div class="it">
                    Price range: <span class="cost price-range-info">
                        $ <span id="range-from">0</span> - <span id="range-to">0</span>
                    </span>
                </div>
                <div class="it" id="container-slider-price-range">
                    <?= $this->render('slider-range', ['rangePrice' => $rangePrice]) ?>
                </div>
            <?php } ?>

    <div class="it">
        <?php if (!($Search->model instanceof TrPosHotels)) { ?>
            <?= $form->field($Search, 'alternativeRate', [])->checkbox(
                [
                    'template' => '{input}<label class="text-uppercase" for="s-alternativerate"><span class="tag tag-non-refundable">Non-refundable ticket</span></label>'
                ]
            ) ?>
        <?php } ?>
        <?php if ($Search->model && ($Search->model instanceof TrShows || $Search->model instanceof TrAttractions)) { ?>
            <?= $form->field($Search, 'tags', ['labelTag' => true])
                ->inline(true)
                ->label(false)
                ->checkboxList($model::getOriginalTagTitleList())
            ?>
        <?php } ?>
    </div>
<?php if ($Search instanceof SearchPosHotel) { ?>
    <div class="it it-filter-rooms">
        <?= $this->render('filter-rooms', compact('Search')) ?>
    </div>
    <div class="it">
        <?= $form->field($Search, 'starRating')
            ->inline(true)
            ->label('Hotel Class', ['class' => 'big', 'for' => null])
            ->checkboxList(
                $Search::getStarList(true),
                ['labelOptions' => ['class' => 'text-uppercase'], 'itemsDisplayCount' => 7]
            )
        ?>
    </div>
    <div class="it">
        Price range: <span class="cost price-range-info">
                        $ <span id="range-from">0</span> - <span id="range-to">0</span>
                    </span>
    </div>
    <div class="it" id="container-slider-price-range">
        <?= $this->render('slider-range', ['rangePrice' => $rangePrice]) ?>
    </div>
<?php } ?>
<?php if (!empty($categories)) { ?>
    <div class="it">
        <?= $form->field($Search, 'c')
            ->inline(true)
            ->label('Category', ['class' => 'big', 'for' => null])
            ->checkboxList(
                $categories,
                ['labelOptions' => ['class' => 'text-uppercase'], 'itemsDisplayCount' => 7]
            )
        ?>
    </div>
<?php } ?>

<?php if (!empty($locations)) { ?>
    <div class="it">
        <?= $form->field($Search, 'l')
            ->inline(true)
            ->label('Locations', ['class' => 'big', 'for' => null])
            ->checkboxList(
                ArrayHelper::map($locations, 'external_id', 'name'),
                ['labelOptions' => ['class' => 'text-uppercase'], 'itemsDisplayCount' => 7]
            )
        ?>
    </div>
<?php } ?>

<?php if (!empty($RestaurantCuisine)) { ?>
    <div class="it">
        <?= $form->field($Search, 'cuisine')
            ->inline(true)
            ->label('Cuisine', ['class' => 'big', 'for' => null])
            ->checkboxList(
                ArrayHelper::map($RestaurantCuisine, 'id', 'name'),
                ['labelOptions' => ['class' => 'text-uppercase'], 'itemsDisplayCount' => 7]
            )
        ?>
    </div>
<?php } ?>

<?php if (!empty($RestaurantCategory)) { ?>
    <div class="it">
        <?= $form->field($Search, 'cr')
            ->inline(true)
            ->label('Categories', ['class' => 'big', 'for' => null])
            ->checkboxList(
                ArrayHelper::map($RestaurantCategory, 'id', 'name'),
                ['labelOptions' => ['class' => 'text-uppercase'], 'itemsDisplayCount' => 7]
            )
        ?>
    </div>
<?php } ?>

<?php if (!empty($RestaurantAmenity)) { ?>
    <div class="it">
        <?= $form->field($Search, 'amenity')
            ->inline(true)
            ->label('Amenities', ['class' => 'big', 'for' => null])
            ->checkboxList(
                ArrayHelper::map($RestaurantAmenity, 'id', 'name'),
                ['labelOptions' => ['class' => 'text-uppercase'], 'itemsDisplayCount' => 7]
            )
        ?>
    </div>
<?php } ?>

<?php if (!empty($restMeals)) { ?>
    <div class="it">
        <?= $form->field($Search, 'meals')
            ->inline(true)
            ->label('Good for', ['class' => 'big', 'for' => null])
            ->checkboxList(
                $restMeals,
                ['labelOptions' => ['class' => 'text-uppercase'], 'itemsDisplayCount' => 7]
            )
        ?>
    </div>
<?php } ?>

<?php if ($Search instanceof SearchPosHotel) { ?>
    <div class="it">
        <?= $form->field($Search, 'city')
            ->inline(true)
            ->label('City', ['class' => 'big', 'for' => null])
            ->checkboxList(
                $Search::getCities(),
                ['labelOptions' => ['class' => 'text-uppercase'], 'itemsDisplayCount' => 7]
            )
        ?>
    </div>
    <div class="it">
        <?= $form->field($Search, 'amenities')
            ->inline(true)
            ->label('Amenities', ['class' => 'big', 'for' => null])
            ->checkboxList(
                $Search::getAmenities(),
                ['labelOptions' => ['class' => 'text-uppercase'], 'itemsDisplayCount' => 7]
            )
        ?>
    </div>
<?php } ?>
    <div class="it">
        <a href="<?= Url::to(['index']) ?>" class="btn btn-link"><i class="fa fa-rotate-left"></i> Clear filters</a>
    </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
