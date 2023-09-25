<?php

use common\models\OrderForm;
use common\models\TrAttractionsPrices;
use common\models\TrLunchs;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

/**
 * @var OrderForm $OrderForm
 * @var ActiveForm $form
 */

?>
<div class="order-fields">
<?php
$prices = ArrayHelper::map($OrderForm->prices, 'id', static function($el){return $el;}, 'id_external');
$allotments = ArrayHelper::index($OrderForm->allotments, 'id_external');
$allotment_current = '';

foreach ($prices as $allotmentId => $allotmentPrices) {

    foreach ($allotmentPrices as $p) {
        $start = new DateTime($p['start']);
        if (!empty($allotments[$p['id_external']])) {
            $allotmentHashName = $allotments[$p['id_external']]['name'].' - '.$start->format($p['any_time'] ? 'Y-m-d' : 'Y-m-d H:i:s');
            if ($allotments && $allotment_current !== $allotmentHashName) {
                $allotment_current = $allotmentHashName;
                $dateStart = new DateTime($allotments[$p['id_external']]['start']);
                $dateEnd = new DateTime($allotments[$p['id_external']]['end']);
                ?>
                </div>

                <?php if ($p instanceof TrAttractionsPrices) {?>
                    <?php if (!empty($p->allotments->inclusions)) {?>
                    <?php $id = 'inclusions-' . $p->allotments->id;?>
                        <div class="collapse-block">
                            <a class="collapse-open" onclick="$('#<?= $id ?>').toggle('slow');return false;">
                                <strong>
                                    <small>See inclusions</small>
                                </strong>
                            </a>
                            <div id="<?= $id?>" class="collapse text-collapse">
                                <?= strip_tags($p->allotments->inclusions, '<strong><i><b><ul><li><ol><div>')?>
                            </div>
                            <div class="icons">
                                <i class="fa fa-angle-up"></i>
                                <i class="fa fa-angle-down"></i>
                            </div>
                        </div>
                    <?php }?>
                    <?php if (!empty($p->allotments->exclusions)) {?>
                        <?php $id = 'exclusions-' . $p->allotments->id;?>
                        <div class="collapse-block">
                            <a class="collapse-open" onclick="$('#<?= $id ?>').toggle('slow');return false;">
                                <strong>
                                    <small>See exclusions</small>
                                </strong>
                            </a>
                            <div id="<?= $id?>" class="collapse text-collapse">
                                <?= strip_tags($p->allotments->exclusions, '<strong><i><b><ul><li><ol><div>')?>
                            </div>
                            <div class="icons">
                                <i class="fa fa-angle-up"></i>
                                <i class="fa fa-angle-down"></i>
                            </div>
                        </div>
                    <?php }?>
                <?php }?>
                <div class="order-fields">
            <?php }} ?>
            <?= $this->render('order-field', compact('OrderForm', 'form', 'p')) ?>
            <?php if ($p->alternative_rate) { ?>
                <?= $this->render('order-field', ['OrderForm' => $OrderForm, 'form' => $form, 'p' => $p, 'alternativeRate' => true]) ?>
            <?php } ?>
    <?php }?>
<?php }?>
</div>