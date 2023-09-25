<?php

use common\models\PaymentModifyForm;
use common\models\PaymentModifyFormAddCard;
use yii\bootstrap\ActiveForm;
use yii\web\JqueryAsset;

/**
 * Used by @wlfrontend
 *
 * @var array                    $cards
 * @var                          $user
 * @var PaymentModifyForm        $PaymentModifyForm
 * @var PaymentModifyFormAddCard $PaymentModifyFormAddCard
 */

$this->title = "Checkout Payment";

$errors = Yii::$app->session->getFlash('errors');
$success = Yii::$app->session->getFlash('success');
$warnings = Yii::$app->session->getFlash('warnings');
$messages = Yii::$app->session->getFlash('messages');
$post = Yii::$app->request->post();
?>

<?php if (!empty($errors)) {?>
	<div class="alert alert-danger"><?=$errors[0]?></div>
<?php }?>
<?php if (!empty($warnings)) {?>
	<div class="alert alert-warning"><?=$warnings[0]?></div>
<?php }?>
<?php if (!empty($messages)) {?>
	<div class="alert alert-success"><?=$messages[0]?></div>
<?php }?>
<?php if (!empty($success)) {?>
	<div class="alert alert-success"><?=$success?></div>
<?php } else {?>


<div class="order-card p-3">
<div class="menu-content menu-content-control mb-4">
	<ul class="nav" role="tablist">
		<?php if ($cards) {?>
        <li role="presentation"><a href="#usecard" aria-controls="usecard" role="tab" data-bs-toggle="tab" <?php if
            (empty($post["PaymentFormAddCard"])) {?>class="active"<?php }?>>Use existing card</a></li>
        <?php }?>
		<li role="presentation"><a href="#addcard" aria-controls="addcard" role="tab" data-bs-toggle="tab" <?php if
            (!empty($post["PaymentFormAddCard"])) {?>class="active"<?php }?>>Add new card</a></li>
	</ul>
</div>
	<div class="tab-content">
		
		<div role="tabpanel" class="tab-pane <?php if (empty($post["PaymentFormAddCard"])) {?>active<?php }?>" id="usecard">
			<?php $form = ActiveForm::begin(['id' => 'payment', 'method'=>'post','options'=>['autocomplete'=>'off']]); ?>
            <?= $form->field($PaymentModifyForm, 'modify_request')->hiddenInput()->label(false) ?>
            <?= $form->field($PaymentModifyForm, 'coupon_code')->hiddenInput()->label(false) ?>
			<div class="rows form-data form-payment mb-3">
				<div class="row">
					<div class="col-sm-6">
						<?= $form->field($PaymentModifyForm, 'axia_id')->dropDownList($cards, ['prompt'=>'- Select Card -'])->label("Card number") ?>
					</div>
					<div class="col-sm-3 col-lg-2">
						<?= $form->field($PaymentModifyForm, 'cvv_code')->passwordInput(['autocomplete'=>'new-password']) ?>
					</div>
				</div>
			</div>
			<?php ActiveForm::end(); ?>
		</div>

		
		<div role="tabpanel" class="tab-pane <?php if (!empty($post["PaymentFormAddCard"])) {?>active<?php }?>" id="addcard">
<?php }?>
			<?php $form = ActiveForm::begin(['id' => 'payment-add-card', 'method'=>'post','options'=>['autocomplete'=>'off']]); ?>
			<?= $form->field($PaymentModifyFormAddCard, 'modify_request')->hiddenInput()->label(false)?>
            <h5 class="mb-3"><strong>Card information</strong></h5>
            <div class="rows form-data form-payment">
                <div class="row">
                    <div class="col-sm-8 col-lg-6">
                        <?= $form->field($PaymentModifyFormAddCard, 'card_number')->textInput(
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
                        <?= $form->field($PaymentModifyFormAddCard, 'expiry_date')->textInput(
                            ["maxlength" => 7, 'autocomplete' => 'new-password', 'placeholder' => 'MM / YY']
                        )->label("Expiry date (MM/YY)") ?>
                    </div>
                    <div class="col-4 col-sm-3 col-lg-2">
                        <?= $form->field($PaymentModifyFormAddCard, 'cvv_code')->textInput(
                            ['autocomplete' => 'new-password', 'placeholder' => 'XYZ']
                        )->passwordInput() ?>
                    </div>
                    <div class="col-sm-8 col-lg-6">
                        <?= $form->field($PaymentModifyFormAddCard, 'name_card')->textInput(
                            ['autocomplete' => 'new-password']
                        ) ?>
                    </div>
                </div>

                <h5 class="mb-3"><strong>Address</strong></h5>
                <?php if (!Yii::$app->user->isGuest && $user["address"]) { ?>
                    <div class="mb-3">
                        <?= $form->field($PaymentModifyFormAddCard, 'same_as_billing')
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
                    <?= $form->field($PaymentModifyFormAddCard, 'street_address_1') ?>
                </div>
                <div class="is-billing">
                    <?= $form->field($PaymentModifyFormAddCard, 'street_address_2') ?>
                </div>
                <div class="row is-billing">
                    <div class="col-sm-2">
                        <?= $form->field($PaymentModifyFormAddCard, 'zip')->textInput(['type' => 'number']) ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($PaymentModifyFormAddCard, 'state') ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($PaymentModifyFormAddCard, 'city') ?>
                    </div>
                    <div class="col-12">
                        <?= $form->field($PaymentModifyFormAddCard, 'country')->dropDownList(
                            $PaymentModifyFormAddCard->getCountryList(),
                            ['prompt' => '- Select Country -']
                        ) ?>
                    </div>
                </div>
            </div>
			
			<?php ActiveForm::end(); ?>
			
<?php if ($cards) {?>
		</div>
	</div>
</div>
<?php }?>

<?php $this->registerJs("payment.init()");?>

<?php if ($PaymentModifyFormAddCard->same_as_billing) { $this->registerJs("payment.billingHide()");}?>
