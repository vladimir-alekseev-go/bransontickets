<?php

use common\models\auth\ChangePasswordForm;
use common\models\Users;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var Users              $model
 * @var ChangePasswordForm $changePasswordForm
 */

$this->title = 'Change Password';

$this->registerMetaTag(['name' => 'robots', 'content' => 'noindex']);

$changePasswordSuccess = Yii::$app->session->getFlash('changePasswordSuccess');
$userSaveSuccess = Yii::$app->session->getFlash('userSaveSuccess');
$message = Yii::$app->session->getFlash('message');
$warnings = Yii::$app->session->getFlash('warnings');
$emailConfirmation = Yii::$app->session->getFlash('emailConfirmation');

$warnings = $emailConfirmation . (strlen($emailConfirmation) > 0 && strlen($warnings) > 0 ? '<br>' : '') . $warnings;
?>
<div class="fixed">
    <h1 class="text-center fw-bold"><?= $this->title ?></h1>
    <div class="text-center mb-3">
        <a href="<?= Url::to(['profile/index']) ?>" class="back">
            <i class="fa fa-arrow-left"></i> <strong>Back to profile</strong>
        </a>
    </div>

    <div class="row">
        <div class="col-12 col-md-6 offset-md-3">
            <div class="white-block shadow-block margin-block-small ms-n15 me-n15">

                <?php if ($message) { ?>
                    <div class="alert alert-success"><?= $message ?></div>
                <?php } ?>
                <?php if ($warnings) { ?>
                    <div class="alert alert-warning"><?= $warnings ?></div>
                <?php } ?>

                <?php $form = ActiveForm::begin(['id' => 'profile-change-password']); ?>
                <div class="rows">
                    <div class="change-pass form-data">
                        <?php if ($changePasswordSuccess) { ?>
                            <div class="alert alert-success"><?= $changePasswordSuccess ?></div>
                        <?php } else { ?>
                            <div class="rows">
                                <?php if ($model->password_hash) { ?>
                                    <div class="row">
                                        <div class="col-12">
                                            <?= $form->field($changePasswordForm, 'password')->passwordInput() ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="row">
                                    <div class="col-12">
                                        <?= $form->field($changePasswordForm, 'password_new')->passwordInput() ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <?= $form->field($changePasswordForm, 'password_repeat')->passwordInput() ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="text-center">
                    <?= Html::submitButton(
                        'Change password',
                        ['class' => 'btn btn-sign d-inline-block']
                    ) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
