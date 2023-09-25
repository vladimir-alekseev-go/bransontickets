<?php

use common\models\CartForm;
use common\models\TrBasket;
use yii\bootstrap\ActiveForm;

/**
 * @var TrBasket $Basket
 * @var CartForm $CartForm
 */

?>
<div class="white-block shadow-block mb-4 order-resume">
    <?php $form = ActiveForm::begin(['id' => 'checkout', 'method' => 'post']); ?>
    <div class="rows">
        <?php if ($Basket->getSaved()) { ?>
            <div class="row row-small-padding">
                <div class="col-7 col-lg-8">
                    <small>Total Savings:</small>
                </div>
                <div class="col-5 col-lg-4 text-end">
                    <span class="cost">$ <?= number_format(
                            $Basket->getSaved(),
                            2,
                            '.',
                            ''
                        ) ?></span>
                </div>
            </div>
        <?php } ?>
        <?php if ($Basket->getCoupon()) { ?>
            <div class="row">
                <div class="col-7 col-lg-8">
                    <small>Discount:</small>
                </div>
                <div class="col-5 col-lg-4 text-end">
                    <span class="cost">$ <?= number_format(
                            $Basket->getCoupon()->getDiscount(),
                            2,
                            '.',
                            ''
                        ) ?></span>
                </div>
            </div>
        <?php } ?>
        <div class="row">
            <div class="col-7 col-lg-8">
                <small>Taxes and Conv. Fees Total:</small>
            </div>
            <div class="col-5 col-lg-4 text-end">
                <?php $sum = $Basket->getTax() + $Basket->getServiceFee(); ?>
                <span class="cost">$ <?=
                    number_format(
                        $Basket->getTax() + $Basket->getServiceFee(),
                        2,
                        '.',
                        ''
                    ) ?></span>
            </div>
        </div>
        <div class="row">
            <div class="col-7 col-lg-8">
                <small>Due at hotel (Resort Fee):</small>
            </div>
            <div class="col-5 col-lg-4 text-end">
                <span class="cost">$ <?=
                    number_format(
                        $Basket->getResortFee(),
                        2,
                        '.',
                        ''
                    ) ?></span>
            </div>
        </div>
        <div class="row">
            <div class="col-7 col-lg-8">
                <strong>Cart Total:</strong>
            </div>
            <div class="col-5 col-lg-4 text-end">
                <span class="cost">$ <?= number_format(
                        $Basket->getFullTotal(),
                        2,
                        '.',
                        ''
                    ) ?></span>
            </div>
        </div>
    </div>
    <p class="mt-3 mb-3">
        <small>
            Please be aware there is a $4.00 processing fee charged in the result of any
            cancellation associated with this order.
        </small>
    </p>

    <?= $form->errorSummary(
        $CartForm,
        ['header' => '<div class="alert alert-danger alert-dismissible">', 'footer' => '</div>']
    ) ?>
    <?php $label = $this->render('terms-and-policy-links', compact('Basket')); ?>
    <?php $label = trim($label); ?>
    <p><small>
            <?= $form->field($CartForm, 'agree', [])->checkbox(
                [
                    'template' => '{input}{beginLabel}{labelTitle}{endLabel}{hint}',
                    'value' => 1,
                    'label' => $label,
                    'uncheck' => '0'
                ]
            ) ?>
        </small>
    </p>
    <button class="btn buy-btn btn-loading-need w-100">Proceed to Checkout</button>

    <?php ActiveForm::end(); ?>
</div>