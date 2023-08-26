<?php

use webvimark\modules\UserManagement\UserManagementModule;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>

<div class="shows-form">

    <?php
    $form = ActiveForm::begin(
        [
            'options' => ['enctype' => 'multipart/form-data'],
            'id' => 'shows',
            'layout' => 'horizontal',
            'validateOnBlur' => false,
        ]
    ); ?>

    <?= $form->field($model, 'show_in_footer')->dropDownList(['No', 'Yes']) ?>

    <?php
    if ($model->image_id) { ?>
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
                <?= Html::img($model->image->url, ['width' => '300px']) ?>
                <div class="checkbox">
                    <label><input type="checkbox" name="deleteImageId" value="1"/> Delete</label>
                </div>
            </div>
        </div>
    <?php
    } ?>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-9">
            <?php
            if ($model->isNewRecord): ?>
                <?= Html::submitButton(
                    '<span class="glyphicon glyphicon-plus-sign"></span> ' . UserManagementModule::t('back', 'Create'),
                    ['class' => 'btn btn-success']
                ) ?>
            <?php
            else: ?>
                <?= Html::submitButton(
                    '<span class="glyphicon glyphicon-ok"></span> ' . UserManagementModule::t('back', 'Save'),
                    ['class' => 'btn btn-primary']
                ) ?>
            <?php
            endif; ?>
        </div>
    </div>

    <?php
    ActiveForm::end(); ?>

</div>
