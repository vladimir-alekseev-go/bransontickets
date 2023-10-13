<?php

use common\models\PaymentForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/**
 * @var PaymentForm $model
 * @var array       $cards
 */
?>

<?php $form = ActiveForm::begin(
    ['id' => 'payment', 'method' => 'post', 'options' => ['autocomplete' => 'off']]
); ?>

<div class="rows form-data form-payment">
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'axia_id')->dropDownList(
                $cards,
                ['prompt' => '- Select Card -']
            )->label("Card number") ?>
        </div>
        <div class="col-sm-3 col-lg-2">
            <?= $form->field($model, 'cvv_code')->passwordInput(
                ['autocomplete' => 'new-password']
            ) ?>
        </div>
    </div>
    <div class="mb-3">
        <?= Html::checkbox('subscribe', true, ['id' => 'join-our-newsletter-2'])
        ?><label for="join-our-newsletter-2">Join Our Newsletter</label>
    </div>
    <div class="gray mb-3">
        Please note that Tripium will be displayed on your credit card statement as the billing entity.
    </div>
</div>
<button class="btn btn-primary btn-loading-need ps-5 pe-5">Pay</button>
<?php ActiveForm::end(); ?>

