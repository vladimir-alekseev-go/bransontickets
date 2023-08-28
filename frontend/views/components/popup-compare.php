<?php

use common\helpers\Modal;
use yii\helpers\Json;
use yii\web\JqueryAsset;

/**
 * @var array  $types
 * @var string $url
 */

$this->registerJsFile('/js/jquery.cookie.js', ['depends' => [JqueryAsset::class]]);
$this->registerJsFile('/js/compare.js', ['depends' => [JqueryAsset::class]]);

?>
<?php Modal::begin(
    [
        'header' => '<h2>Compare</h2><a href="#" class="compare-clear float-right btn btn-link js-compare-clear"
        ><img src="/img/xmark-blue.svg" alt="xmark icon"> Clear all items</a>',
        'id' => 'popup-compare',
        'size' => 'modal-dialog-centered modal-lg popup-compare',
        'clientOptions' => ['show' => true, 'keyboard' => false],
    ]
);
?>
    <div id="js-data-container-compare"></div>
<?php Modal::end(); ?>
<?php $this->registerJs('compare.init("' . $url . '", ' . Json::encode($types) . ');'); ?>
