<?php

use yii\authclient\widgets\AuthChoice;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var $model LoginForm
 */

$this->title = 'Sign In';
$this->params['breadcrumbs'][] = $this->title;

$messages = Yii::$app->session->getFlash('messages');

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
                            <h5 class="mb-3"><strong>Sign In with Email</strong></h5>
                            <?php if (!empty($messages)) { ?>
                                <div class="alert alert-success"><?= $messages[0] ?></div>
                            <?php } ?>
                            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
                            <?= $form->errorSummary(
                                $model,
                                [
                                    'header' => '<div class="alert alert-danger alert-dismissible">',
                                    'footer' => '</div>'
                                ]
                            ) ?>
                            <?= $form->field($model, 'username') ?>
                            <?= $form->field($model, 'password')->passwordInput() ?>
                            <?= $form->field($model, 'rememberMe')->checkbox(
                                ['template' => '{input}{beginLabel}{labelTitle}{endLabel}{hint}',]
                            ) ?>
                            <div class="row mt-3">
                                <div class="col-5">
                                    <?= Html::submitButton(
                                        'Sign In',
                                        ['class' => 'btn btn-primary', 'name' => 'sign-in-button']
                                    ) ?>
                                </div>
                                <div class="col-7 text-end forgot">
                                    <a href="<?= Url::to(['site/requestpasswordreset']) ?>">
                                        <strong>Forgot Password?</strong>
                                    </a>
                                </div>
                            </div>
                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                    <div class="col-2 d-none d-sm-flex">
                        <div class="line"></div>
                    </div>
                    <div class="col-12 col-sm-5">
                        <h5 class="mb-sm-4 mb-3"><strong>Sign In with Social Network</strong></h5>
                        <div class="pt-0 pt-sm-3">
                            <?= AuthChoice::widget(
                                [
                                    'baseAuthUrl' => ['site/auth']
                                ]
                            ) ?>
                        </div>
                    </div>
                </div>
                <div class="footer">
                    <div>Don't have an account?</div>
                    <a href="<?= Url::to(['site/signup']) ?>"><strong>Sign Up</strong></a>
                </div>
            </div>
        </div>
    </div>
</div>

