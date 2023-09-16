<?php

use frontend\widgets\vacationPackagesList\VacationPackagesListWidget;
use yii\web\JqueryAsset;

/**
 * @var VacationPackagesListWidget $VPLWidget
 */

$this->context->layout = 'main-list';
$this->title = 'Vacation Packages';

$this->registerJsFile('/js/bootstrap-datepicker.min.js', ['depends' => [JqueryAsset::class]]);
$this->registerJsFile('/js/datepicker.js', ['depends' => [JqueryAsset::class]]);
$this->registerJsFile('/js/page-nav.js', ['depends' => [JqueryAsset::class]]);
$this->registerJsFile('/js/data-list.js', ['depends' => [JqueryAsset::class]]);

?>

<?= $VPLWidget->run() ?>
