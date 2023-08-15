<?php

use yii\web\JqueryAsset;

$this->registerCssFile('/css/fullcalendar.min.css', ['depends' => [JqueryAsset::class]]);
?>

<div id="schedule" role="tabpanel" aria-labelledby="schedule-tab" class="tab-pane">
    <div class="fixed">
        <div id="calendar"></div>
    </div>
</div>

<?php $this->registerJsFile('/js/fullcalendar.min.js', ['depends' => [JqueryAsset::class]]); ?>
<?php $this->registerJsFile('/js/schedule.js', ['depends' => [JqueryAsset::class]]); ?>
