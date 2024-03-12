<?php

use yii\web\JqueryAsset;

$this->registerCssFile('/css/fullcalendar.min.css', ['depends' => [JqueryAsset::class]]);
?>

<div id="calendar"></div>

<?php $this->registerJsFile('/js/fullcalendar.min.js', ['depends' => [JqueryAsset::class]]); ?>
<?php $this->registerJsFile('/js/schedule.js', ['depends' => [JqueryAsset::class]]); ?>
