<?php

use yii\bootstrap\BootstrapAsset;
use yii\web\JqueryAsset;

$this->registerCssFile('/css/bootstrap-datepicker.min.css', ['depends' => [BootstrapAsset::class]]);

?>

<div class="main-banner">
    <div class="main-logo">
        <img src="img/bransontickets-logo.png" alt="Branson Tickets logo">
    </div>
    <form action="#">
        <div class="search">
            <div class="field form-group input-search">
                <input type="text" id="search" class="form-control" name="Search[name]" value="" aria-required="true" aria-invalid="false" placeholder="I'm looking for">
            </div>
            <div class="field form-group input-group date">
                <input type="text" id="date" class="form-control" name="s[dateFrom]" autocomplete="off" placeholder="Event date">
                <div class="input-group-addon"></div>
            </div>
            <select id="shows-category" class="form-control" name="s[c][]">
                <option value="">Select categories</option>
                <option value="1">Category 1</option>
                <option value="2">Category 2</option>
                <option value="3">Category 3</option>
            </select>
            <button class="btn btn-search" value="search">Search</button>
        </div>
    </form>
    <div class="pros">
        <div class="pros-items">
            <div class="pros-item"><img src="img/check.svg" alt="check icon">Satisfaction guaranteed</div>
            <div class="pros-item"><img src="img/check.svg" alt="check icon">No additional requirements</div>
            <div class="pros-item"><img src="img/check.svg" alt="check icon">Best seat available</div>
        </div>
    </div>
</div>
<?php $this->registerJsFile('/js/bootstrap-datepicker.min.js', ['depends' => [JqueryAsset::class]]); ?>
<?php $this->registerJsFile('/js/datepicker.js', ['depends' => [JqueryAsset::class]]); ?>
