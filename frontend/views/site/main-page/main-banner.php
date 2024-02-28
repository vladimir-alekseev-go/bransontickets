<?php

use frontend\widgets\search\SearchWidget;
use yii\web\JqueryAsset;

?>

<div class="main-banner mb-5">
    <div class="bg">
        <div class="fixed">
            <div class="title">
                Choose an event and make your weekend bright and exciting
            </div>
            <div class="search">
                <?= SearchWidget::widget() ?>
            </div>
        </div>
    </div>
</div>
<div class="fixed">
    <div class="row pros">
        <div class="col-md-4 col-12 mb-3">
            <div class="pros-item p-md-4 p-3">
                <img src="img/check-circle-2.svg" alt="check icon">Satisfaction guaranteed
            </div>
        </div>
        <div class="col-md-4 col-12 mb-3">
            <div class="pros-item p-md-4 p-3">
                <img src="img/circle-slash.svg" alt="check icon">No additional requirements
            </div>
        </div>
        <div class="col-md-4 col-12 mb-3">
            <div class="pros-item p-md-4 p-3">
                <img src="img/star.svg" alt="check icon">Best seat available
            </div>
        </div>
    </div>
</div>
<?php $this->registerJsFile('/js/bootstrap-datepicker.min.js', ['depends' => [JqueryAsset::class]]); ?>
<?php $this->registerJsFile('/js/datepicker.js', ['depends' => [JqueryAsset::class]]); ?>
