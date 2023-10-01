<?php

use frontend\widgets\search\SearchWidget;
use yii\bootstrap\BootstrapAsset;
use yii\web\JqueryAsset;

?>

<div class="main-banner">
    <div class="main-logo">
        <img src="img/bransontickets-logo.png" alt="Branson Tickets logo">
    </div>
    <div class="search">
        <?= SearchWidget::widget() ?>
    </div>
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
