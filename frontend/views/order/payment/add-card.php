<?php

use common\models\PaymentFormAddCard;
use common\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var PaymentFormAddCard $modelAddCard
 * @var User               $user
 */

?>

<?php $form = ActiveForm::begin(
    ['id' => 'payment-add-card', 'method' => 'post', 'options' => ['autocomplete' => 'off']]
); ?>
<h5 class="mb-3"><strong>Card information</strong></h5>
<div class="rows form-data form-payment">
    <div class="row">
        <div class="col-sm-8 col-lg-6">
            <?= $form->field($modelAddCard, 'card_number')->textInput(
                [
                    "class" => "form-control card-number",
                    'autocomplete' => 'new-password',
                    'placeholder' => 'XXXX  XXXX  XXXX  XXXX',
                    "maxlength" => 19,
                ]
            ) ?>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-8 col-sm-5 col-lg-4">
            <?= $form->field($modelAddCard, 'expiry_date')->textInput(
                ["maxlength" => 7, 'autocomplete' => 'new-password', 'placeholder' => 'MM / YY']
            )->label("Expiry date (MM/YY)") ?>
        </div>
        <div class="col-4 col-sm-3 col-lg-2">
            <?= $form->field($modelAddCard, 'cvv_code')->textInput(
                ['autocomplete' => 'new-password', 'placeholder' => 'XYZ']
            )->passwordInput() ?>
        </div>
        <div class="col-sm-8 col-lg-6">
            <?= $form->field($modelAddCard, 'name_card')->textInput(
                ['autocomplete' => 'new-password']
            ) ?>
        </div>
    </div>

    <h5 class="mb-3"><strong>Address</strong></h5>
    <?php if (!Yii::$app->user->isGuest && $user["address"]) { ?>
        <div class="mb-3">
            <?= $form->field($modelAddCard, 'same_as_billing')
                ->checkbox(
                    [
                        'template' => '{input}{beginLabel}{labelTitle}{endLabel}{hint}',
                        'value' => 1,
                        'uncheck' => '0'
                    ]
                )->label('Same as Customer Address') ?>
        </div>
    <?php } ?>
    <div class="is-billing">
        <?= $form->field($modelAddCard, 'street_address_1') ?>
    </div>
    <div class="is-billing">
        <?= $form->field($modelAddCard, 'street_address_2') ?>
    </div>
    <div class="row is-billing">
        <div class="col-2">
            <?= $form->field($modelAddCard, 'zip')->textInput(['type' => 'number']) ?>
        </div>
        <div class="col-4">
            <?= $form->field($modelAddCard, 'state') ?>
        </div>
        <div class="col-6">
            <?= $form->field($modelAddCard, 'city') ?>
        </div>
        <div class="col-12">
            <?= $form->field($modelAddCard, 'country')->dropDownList(
                $modelAddCard->getCountryList(),
                ['prompt' => '- Select Country -']
            ) ?>
        </div>
    </div>

    <div class="mb-3">
        <?= Html::checkbox('subscribe', true, ['id' => 'join-our-newsletter-1'])
        ?><label for="join-our-newsletter-1">Join Our Newsletter</label>
    </div>
    <div class="gray mb-3">
        Please note that Tripium will be displayed on your credit card statement as the billing entity.
    </div>
</div>

<button class="btn btn-primary btn-loading-need ps-5 pe-5">Pay</button>
<?php ActiveForm::end(); ?>
