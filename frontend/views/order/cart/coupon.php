<?php

use common\helpers\Modal;
use common\models\form\CartCouponForm;
use common\models\TrBasket;
use yii\bootstrap\Alert;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var TrBasket $Basket
 * @var CartCouponForm $CartCouponForm
 */

?>
<?php if ($coupon = $Basket->getCoupon()) {?>

    <?php if (Yii::$app->request->get('changed') == 1 && $Basket->getOriginal() && $Basket->getDiscountByPrice() && $coupon->discounted_total + $coupon->discount != $Basket->getOriginal()['total']) {?>
        <?php Modal::begin(
            [
                'header' => 'Discount code applied ' . $coupon->code,
                'size' => 'modal-dialog-centered popup-discount-code-applied',
                'id' => 'popup-discount-code-applied',
                'clientOptions' => ['show' => true, 'keyboard' => false],
            ]
        ); ?>
        <p>You must choose to purchase the items at the special rate displayed or to use the regular rate and apply the discount code.</p>
        <p class="text-center">Click <?= Html::a('Apply promo code', ['order/cart'], ['class'=>'cancel-code']) ?> to apply the discount code <b>$<?= $coupon->discount?></b>.
            <br/><b>or</b><br/>
            Click <?= Html::a('Use special price', ['order/cart'], ['class'=>'cancel-code','data-method'=>'post', 'data-params' => ['CartCouponForm[coupon]'=>'']]) ?> to keep the special rate displayed <b>$<?= $Basket->getDiscountByPrice()?></b>.
        </p>
        <?php Modal::end();?>
        <?php $this->registerJs("$('#popup-discount-code-applied').modal('show');"); ?>
    <?php }?>

    <?php Alert::begin(
        [
            'options' => [
                'class' => 'alert alert-success alert-success-border promo-code show',
            ],
            'closeButton' => false
        ]
    ); ?>

    <?php if ($Basket->getOriginal() && $Basket->getDiscountByPrice() && $coupon->discounted_total + $coupon->discount != $Basket->getOriginal()['total']) {?>
        You have applied Discount Code "<strong><?= $coupon->code?></strong>" for a savings of <strong>$<?= number_format
        ($coupon->discount, 2, '.', '')?></strong>.
        <br/>This has removed the "on sale" price from your items.
        <br/>If you would like to keep the "on sale" price you must remove the&nbsp;Discount&nbsp;Code.
    <?php } else {?>
        Discount code applied "<?= $coupon->code?>"
        <br/>Discount amount is $ <?= number_format($coupon->discount, 2, '.', '')?>
    <?php }?>
    <div class="text-end">
        <?= Html::a(
            '<span class="icon ib-x"></span> Remove Code',
            ['order/cart'],
            [
                'class' => 'cancel-code',
                'data-method' => 'post',
                'data-params' => ['CartCouponForm[coupon]' => '']
            ]
        ) ?>
    </div>
    <?php Alert::end();?>
<?php } else {?>
    <div class="white-block shadow-block mb-4 p-3 kiosk-promo-code">
        <h5 class="mb-2"><strong>Apply a promo code</strong></h5>
        <?php $form = ActiveForm::begin(
            ['id' => 'coupon', 'method' => 'post', 'action' => ['order/cart']]
        ); ?>
        <div class="row row-small-padding">
            <div class="col-7">
                <?= $form->field($CartCouponForm, 'coupon', ['template' => '{input}'])
                    ->textInput()->label(false) ?>
            </div>
            <div class="col-5">
                <button class="buy-btn w-100" type="submit">Apply!</button>
            </div>
        </div>
        <?= $form->errorSummary(
            $CartCouponForm,
            [
                'header' => '<div class="alert alert-warning alert-dismissible mt-2">',
                'footer' => '</div>'
            ]
        ) ?>
        <?php ActiveForm::end(); ?>
    </div>
<?php }?>