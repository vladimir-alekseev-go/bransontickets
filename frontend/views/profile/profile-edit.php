<?php

use common\models\Users;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var Users $model
 */

$this->title = 'Modify profile';

$this->registerMetaTag(['name' => 'robots', 'content' => 'noindex']);

$changePasswordSuccess = Yii::$app->session->getFlash('changePasswordSuccess');
$userSaveSuccess = Yii::$app->session->getFlash('userSaveSuccess');
$message = Yii::$app->session->getFlash('message');
$warnings = Yii::$app->session->getFlash('warnings');
$emailConfirmation = Yii::$app->session->getFlash('emailConfirmation');

$warnings = $emailConfirmation . (strlen($emailConfirmation) > 0 && strlen($warnings) > 0 ? '<br>' : '') . $warnings;
?>
    <div class="fixed">
        <div class="header-padding">
            <h1 class="text-center fw-bold pt-4">Modify information</h1>
            <div class="text-center mb-3">
                <a href="<?= Url::to(['profile/index']) ?>" class="back">
                    <i class="fa fa-arrow-left"></i> <strong>Back to profile</strong>
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-8 offset-md-2">
                <div class="white-block shadow-block margin-block-small ms-n15 me-n15">

                    <?php if ($message) { ?>
                        <div class="alert alert-warning"><?= $message ?></div>
                    <?php } ?>
                    <?php if ($warnings) { ?>
                        <div class="alert alert-warning"><?= $warnings ?></div>
                    <?php } ?>

                    <?php $form = ActiveForm::begin(['id' => 'profile-edit']); ?>


                    <div class="form-data">

                        <?php if ($userSaveSuccess) { ?>
                            <div class="alert alert-success"><?= $userSaveSuccess ?></div>
                        <?php } ?>

                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <?= $form->field($model, 'first_name') ?>
                            </div>
                            <div class="col-12 col-sm-6">
                                <?= $form->field($model, 'last_name') ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <?= $form->field($model, 'email')->textInput(
                                //['disabled' => $model->email && ($model->fb_id || $model->tw_id || $model->gp_id)]
                                ) ?>
                            </div>
                            <div class="col-12 col-sm-6">
                                <?= $form->field($model, 'phone') ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <?= $form->field($model, 'address') ?>
                            </div>
                            <div class="col-12 col-sm-6">
                                <?= $form->field($model, 'zip_code') ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <?= $form->field($model, 'city') ?>
                            </div>
                            <div class="col-12 col-sm-6">
                                <?= $form->field($model, 'state')
                                    ->dropDownList(
                                        Users::getStateList(),
                                        ['prompt' => '- Select Country -']
                                    )->label(
                                        'Country'
                                    ) ?>
                            </div>
                        </div>
                    </div>

                    <?= Html::submitButton(
                        'Update information',
                        ['class' => 'btn btn-sign']
                    ) ?>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-12 col-md-8 offset-md-2">
                <div class="white-block shadow-block margin-block-small ms-n15 me-n15 text-center">

                    <?php $form = ActiveForm::begin(
                        ['id' => 'profile-delete', 'action' => ['profile/delete'], 'method' => 'post']
                    ); ?>

                    <?= Html::submitButton(
                        'Delete My Account',
                        [
                            'class'   => 'btn btn-delete',
                            'onclick' => 'return confirm("Are you sure you want to delete your account?");'
                        ]
                    ) ?>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>


    </div>

<?php $this->registerJs(
    '
	profileEdit.init()
'
); ?>
