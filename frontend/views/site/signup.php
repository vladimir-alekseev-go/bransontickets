<?php

use common\models\auth\SignupForm;
use yii\authclient\widgets\AuthChoice;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/**
 * @var SignupForm $model
 */

$this->title = 'Sign Up';
$this->params['breadcrumbs'][] = $this->title;

$messages = Yii::$app->session->getFlash('messages');

?>
<div class="fixed">
    <div class="row">
        <div class="col-12 col-lg-8 offset-lg-2 header-padding">
            <a href="#" onclick="history.go(-1);return false;" class="position-absolute mt-4 back">
                <i class="fa fa-arrow-left"></i> <strong>Back</strong>
            </a>
            <h1 class="text-center fw-bold h2 mb-4 pt-4"><?= Html::encode($this->title) ?></h1>
            <div class="white-block shadow-block site-signup margin-block-small">
                <div class="row">
                    <div class="col-12 col-sm-5 mb-5 mb-sm-0">
                        <div class="form-data">
                            <h5 class="mb-3"><strong>Sign Up with Email</strong></h5>
                            <?php if (!empty($messages)) { ?>
                                <div class="alert alert-success"><?= $messages[0] ?></div>
                            <?php } ?>

                            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>
                            <?= $form->field($model, 'email') ?>
                            <?= $form->field($model, 'password')->passwordInput() ?>
                            <?= $form->field($model, 'password_repeat')->passwordInput() ?>
                            <div class="form-group">
                                <?= Html::submitButton(
                                    'Signup',
                                    ['class' => 'btn btn-sign', 'name' => 'signup-button']
                                ) ?>
                            </div>
                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                    <div class="col-2 d-none d-sm-flex">
                        <div class="line"></div>
                    </div>
                    <div class="col-12 col-sm-5">
                        <h5 class="mb-sm-4 mb-3"><strong>Sign Up with Social Network</strong></h5>
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
                    <div>Already have an account?</div>
                    <a href="<?= Url::to(['site/login'])?>"><strong>Sign In</strong></a>
                </div>
            </div>
        </div>
    </div>
</div>
