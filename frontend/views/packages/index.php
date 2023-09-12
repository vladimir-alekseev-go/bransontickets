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

<?php $this->registerJs(
    "
    $('.view-more').click(function () {
        $('.more').css('display', 'flex');
        $('.view').css('display', 'none');
    });

    $('.package-1-show').click(function () {
        $('.js-package-1').css('display', 'block');
        $('.package-1-show').css('display', 'none');
        $('.package-1-hide').css('display', 'block');
    });

    $('.package-1-hide').click(function () {
        $('.js-package-1').css('display', 'none');
        $('.package-1-hide').css('display', 'none');
        $('.package-1-show').css('display', 'block');
    });

    $('.package-2-show').click(function () {
        $('.js-package-2').css('display', 'block');
        $('.package-2-show').css('display', 'none');
        $('.package-2-hide').css('display', 'block');
    });

    $('.package-2-hide').click(function () {
        $('.js-package-2').css('display', 'none');
        $('.package-2-hide').css('display', 'none');
        $('.package-2-show').css('display', 'block');
    });
    "
);
?>
