<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Reset password';
$this->params['breadcrumbs'][] = $this->title;

$success = Yii::$app->session->getFlash('success');
?>
<div class="fixed">
<?php if ($success) {?>
    <div class="alert-success success-block">
        <?= $success?>
    </div>
<?php } else {?>
<div class="site-reset-password">
    <div class="white-block shadow-block">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>Please choose your new password:</p>

        <div class="row">
            <div class="col-lg-5 form-restore-pass">
                <?php $form = ActiveForm::begin(['id' => 'reset-password-form', 'class'=>'form-restore-pass']); ?>

                    <?= $form->field($model, 'password')->passwordInput() ?>

                    <div class="form-group">
                        <?= Html::submitButton('Save', ['class' => 'btn btn-primary d-inline-block ps-5 pe-5']) ?>
                    </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<?php }?>
</div>
