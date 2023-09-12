<?php 
use yii\bootstrap\Modal;
$Search = $this->context->search;
$rangePrice = $this->context->rangePrice;
?>

<div id="show-list" class="packages-list-new"> 
	<?= $this->render('items')?>
</div>
<?php $this->beginBlock('footer-end-body') ?>
	<?= isset($this->blocks['footer-end-body']) ? $this->blocks['footer-end-body'] : ''?>
	    <?php Modal::begin([
        'header' => '<h2 id="modalHeaderTitle" class="modalHeaderTitle"></h2>',
        'headerOptions' => ['id' => 'modalHeader'],
        'id' => 'modalVacationPackage',
        'size' => 'modal-lg',
        'clientOptions' => ['show' => false, 'backdrop' => 'static', 'keyboard' => false]
    ]);
    ?>
    <div id="modalContent"></div>
    <?php Modal::end();?>
<?php $this->endBlock() ?>
<?php $this->context->assetRegister();?>
<?php $this->registerJs("VacationPackageBuy.init();");?>