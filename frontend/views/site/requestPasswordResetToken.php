<?php

use common\models\auth\PasswordResetRequestForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var PasswordResetRequestForm $model
 */

$this->title = 'Restore password';
$this->params['breadcrumbs'][] = $this->title;

$messages = Yii::$app->session->getFlash('messages');
$success = Yii::$app->session->getFlash('success');
$error = Yii::$app->session->getFlash('error');
?>
<div class="fixed">
    <div class="row">
        <div class="col-12 col-lg-8 offset-lg-2">
            <a href="#" onclick="history.go(-1);return false;" class="position-absolute back">
                <i class="fa fa-arrow-left"></i> <strong>Back</strong>
            </a>
            <h1 class="text-center fw-bold h2 mb-4"><?= Html::encode($this->title) ?></h1>
            <div class="white-block shadow-block site-signup border-block margin-block-small">
                <div class="row">
                    <div class="col-12 col-sm-5 mb-5 mb-sm-0">
                        <div class="form-data">
                            <?php if (!empty($messages)) { ?>
                                <div class="alert alert-success"><?= $messages[0] ?></div>
                            <?php } ?>
                            <?php $form = ActiveForm::begin(
                                ['id' => 'request-password-reset-form', 'class' => 'form-restore-pass']
                            ); ?>

                            <?= $form->field($model, 'email') ?>

                            <div class="form-group">
                                <?= Html::submitButton(
                                    'Send',
                                    ['class' => 'btn btn-sign d-inline-block ps-5 pe-5']
                                ) ?>
                            </div>

                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 offset-sm-1">
                        <?php if ($error) { ?>
                            <div class="alert alert-danger">
                                <?= $error ?>
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-info alert-info-icon">
                                Please fill out your email. A link to reset password will be sent there.
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="footer">
                    <div>Remember your password?</div>
                    <a href="<?= Url::to(['site/login']) ?>"><strong>Sign In</strong></a>
                </div>
            </div>
        </div>
    </div>
</div>

