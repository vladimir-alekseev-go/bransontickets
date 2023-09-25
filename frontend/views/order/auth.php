<?php

use common\models\CustumerForm;
use common\models\TrBasket;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/**
 * @var TrBasket     $basket
 * @var CustumerForm $custumerForm
 */

$errors = Yii::$app->session->getFlash('errors');
$warnings = Yii::$app->session->getFlash('warnings');
?>
<div class="fixed">
    <h1 class="h2"><strong>Checkout</strong></h1>

    <?php if ($basket) { ?>

        <div class="row">
            <div class="col-md-8 order-2 order-md-1">
                <div class="white-block shadow-block margin-block-small">

                    <?php if (!empty($errors)) { ?>
                        <div class="alert alert-danger"><?= $errors[0] ?></div>
                    <?php } ?>
                    <?php if ($warnings) { ?>
                        <div class="alert alert-info alert-info-icon"><?= $warnings ?></div>
                    <?php } ?>

                    <div class="row">
                        <div class="col-sm-8 order-2 order-sm-1">
                            <div class="mb-2"><strong>Please tell us, to whom the order will issued</strong></div>
                            <div class="form-data">
                                <?php $form = ActiveForm::begin(['id' => 'auth', 'method' => 'post']); ?>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <?= $form->field($custumerForm, 'first_name') ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <?= $form->field($custumerForm, 'last_name') ?>
                                    </div>
                                    <div class="col-12">
                                        <?= $form->field($custumerForm, 'email') ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <?= $form->field($custumerForm, 'phone') ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <?= $form->field($custumerForm, 'zip_code') ?>
                                    </div>
                                </div>
                                <button class="btn btn-primary">Continue</button>
                                <?php ActiveForm::end(); ?>
                            </div>

                        </div>
                        <div class="col-sm-4 order-sm-2">
                            <div class="already-have-an-account">
                                <span class="icon ib-user"></span>
                                <div>
                                    <div>I already have an account</div>
                                    <small>
                                        <strong>
                                            <a href="<?= Url::to(
                                                ['site/login', 'returnUrl' => ['/order/checkout']]
                                            ) ?>">Sign in to Checkout faster</a>
                                        </strong>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($basket->getPackages()) { ?>
                <div class="col-md-4 order-1 order-md-2">
                    <?= $this->render('payment/short-payment-cart') ?>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <strong>Cart is empty!</strong>
    <?php } ?>

</div>