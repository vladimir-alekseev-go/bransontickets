<?php

use common\models\OrderForm;
use common\models\TrAttractionsPrices;
use common\models\TrPrices;
use yii\bootstrap\ActiveForm;
/**
 * @var OrderForm                    $OrderForm
 * @var ActiveForm                   $form
 * @var TrAttractionsPrices|TrPrices $p
 * @var bool                         $alternativeRate
 */

$alternativeRate = $alternativeRate ?? false;

?>
<div class="flex-table row order-container-row<?= !empty($allotments) ? ' allotments' : ''?>" role="rowgroup"
     data-show-id="<?= $OrderForm->model->id?>" data-price-id="<?= $p["id"]?>"
     data-price-id_external="<?= $p["id_external"]?>"
     data-hash="<?= $p["hash"]?>">
    <div class="flex-row first" role="cell">
        <div class="row">
            <div class="col-12 col-lg-7">
                <div class="price-title">
                    <?= $p->name?> <?php if ($alternativeRate && !empty($p->alternative_rate)) {?>
                        <br/><small class="non-refundable-ticket yellow-dark">Non-refundable</small>
                    <?php }?>
                </div>
                <div class="price-description-type"><?= $p->description?></div>
            </div>
            <div class="col-12 col-lg-5 valid-date-value">
                <?php if ($p instanceof TrAttractionsPrices && $p->any_time) {?>
                    <small>
                        <span class="d-inline-block d-lg-none">Valid date:</span>
                        <?= $p->getStartDate()->format('m/d/Y D')?> - <?= $p->getEndDate()->format('m/d/Y D') ?>
                    </small>
                <?php }?>
            </div>
        </div>
    </div>
    <div class="flex-row text-end" role="cell" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
        <?php if ($alternativeRate && !empty($p->alternative_rate)) {?>
            <div>
                <span class="tag tag-save">
                    $<?= number_format($p->getSaved(true), 2, '.', '') ?> saved
                </span>
                <span class="cost">$ <?= $p->retail_rate ?></span>
            </div>
            <span class="cost" itemprop="price"
                  content="<?= number_format($p->alternative_rate, 2, '.', '') ?>">
                        $ <?= $p->alternative_rate ?>
                    </span>
        <?php } else {?>
            <?php if ($p->retail_rate !== $p->price) {?>
                <div>
                    <span class="btn btn-third btn-sm red">
                        $<?= number_format($p->getSaved(),2, '.', '') ?>&nbsp;saved
                    </span>
                    <span class="cost cost-old">$ <?= $p->retail_rate?></span>
                </div>
            <?php }?>
            <span class="cost" itemprop="price" content="<?= number_format($p->price, 2, '.', '')?>">$ <?= $p->price?></span>
        <?php }?>
        <span itemprop="availability" content="<?= $OrderForm->getAttributeOption($p, $alternativeRate)['data-max'] != '' ? $OrderForm->getAttributeOption($p, $alternativeRate)['data-max'] : 99?>"></span>
        <span itemprop="validFrom" content="<?= (new DateTime())->format('Y-m-d H:i:s')?>"></span>
    </div>

    <div class="flex-row with-input-field" role="cell">
        <i class="js-input-factor fa fa-minus in-active" data-factor="-1"></i>
        <i class="js-input-factor fa fa-plus" data-factor="1"></i>
        <?php $activeField = $form->field($OrderForm, OrderForm::getAttributeName($p, $alternativeRate), ['template'=>'{input}']);?>
        <?= $activeField->textInput($OrderForm->getAttributeOption($p, $alternativeRate))->label($p->name) ?>
    </div>
    <div class="flex-row" role="cell"><span class="cost subtotal-cost js-subtotal-cost">$ 0.00</span></div>
</div>
<?php if ($p["name"] === TrPrices::PRICE_TYPE_FAMILY_PASS) {?>
    <div class="flex-table row order-container-fp<?php
    if ((int)$OrderForm->{OrderForm::getAttributeName($p, $alternativeRate)} === 0){?> hide<?php }?>">
        <div class="flex-row">
            For FAMILY PASS order you must total number of seats
        </div>
        <div class="flex-row" role="cell"></div>
        <div class="flex-row with-input-field" role="cell">
            <i class="js-input-factor fa fa-minus in-active" data-factor="-1"></i>
            <i class="js-input-factor fa fa-plus" data-factor="1"></i>
            <?php $activeField = $form->field($OrderForm, OrderForm::getAttributeName($p, $alternativeRate, OrderForm::SEATS_FIELD_SUB_NAME), ['template'=>'{input}']);?>
            <?= $activeField->textInput($OrderForm->getAttributeSeatOption($p, $alternativeRate)) ?>
        </div>
    </div>
<?php }?>
<?php if ($p["name"] === TrPrices::PRICE_TYPE_FAMILY_PASS_4_PACK) {?>
    <div class="flex-table row order-container-fp<?php
    if ((int)$OrderForm->{OrderForm::getAttributeName($p, $alternativeRate)} === 0){?> hide<?php }?>">
        <div class="flex-row">
            For FAMILY PASS 4 PACK order you must put total number of seats (3 or 4 seats)
        </div>
        <div class="flex-row" role="cell"></div>
        <div class="flex-row with-input-field" role="cell">
            <i class="js-input-factor fa fa-minus in-active" data-factor="-1"></i>
            <i class="js-input-factor fa fa-plus" data-factor="1"></i>
            <?php $activeField = $form->field(
                $OrderForm,
                OrderForm::getAttributeName($p, $alternativeRate, OrderForm::SEATS_4_FIELD_SUB_NAME),
                ['template' => '{input}']
            ); ?>
            <?= $activeField->textInput(
                    array_merge(
                        $OrderForm->getAttributeSeatOption($p, $alternativeRate),
                        ['max' => 4, 'data-max' => 4, 'min' => 3, 'data-min' => 3]
                    )
            ) ?>
        </div>
    </div>
<?php }?>
<?php if ($p["name"] === TrPrices::PRICE_TYPE_FAMILY_PASS_8_PACK) {?>
    <div class="flex-table row order-container-fp<?php
    if ((int)$OrderForm->{OrderForm::getAttributeName($p, $alternativeRate)} === 0){?> hide<?php }?>">
        <div class="flex-row">
            For FAMILY PASS 8 PACK order you must put total number of seats (from 5 to 8 seats)
        </div>
        <div class="flex-row" role="cell"></div>
        <div class="flex-row with-input-field" role="cell">
            <i class="js-input-factor fa fa-minus in-active" data-factor="-1"></i>
            <i class="js-input-factor fa fa-plus" data-factor="1"></i>
            <?php $activeField = $form->field(
                $OrderForm,
                OrderForm::getAttributeName($p, $alternativeRate, OrderForm::SEATS_8_FIELD_SUB_NAME),
                ['template' => '{input}']
            ); ?>
            <?= $activeField->textInput(
                array_merge(
                    $OrderForm->getAttributeSeatOption($p, $alternativeRate),
                    ['min' => 5, 'data-min' => 5, 'max' => 8, 'data-max' => 8]
                )
            ) ?>
        </div>
    </div>
<?php }?>
