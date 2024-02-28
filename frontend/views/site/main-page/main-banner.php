<?php

use frontend\widgets\search\SearchWidget;
use yii\web\JqueryAsset;

?>

<div class="main-banner mb-5">
    <div class="bg">
        <div class="fixed">
            <div class="title mb-3 mb-lg-5">
                Choose an event and make your weekend bright and exciting
            </div>
            <div class="search">
                <?= $this->render('search-on-main') ?>
            </div>
        </div>
    </div>
</div>
<div class="fixed">
    <div class="row pros">
        <div class="col-md-4 col-12 mb-3">
            <div class="pros-item p-md-4 p-3">
                <span class="icon br-t-check-circle"></span> Satisfaction guaranteed
            </div>
        </div>
        <div class="col-md-4 col-12 mb-3">
            <div class="pros-item p-md-4 p-3">
                <span class="icon br-t-slash-circle"></span> No additional requirements
            </div>
        </div>
        <div class="col-md-4 col-12 mb-3">
            <div class="pros-item p-md-4 p-3">
                <span class="icon br-t-star"></span> Best seat available
            </div>
        </div>
    </div>
</div>
<?php $this->registerJsFile('/js/bootstrap-datepicker.min.js', ['depends' => [JqueryAsset::class]]); ?>
<?php $this->registerJsFile('/js/datepicker.js', ['depends' => [JqueryAsset::class]]); ?>
