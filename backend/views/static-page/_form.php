<?php

use common\models\StaticPage;
use dosamigos\ckeditor\CKEditor;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var yii\web\View             $this
 * @var yii\bootstrap\ActiveForm $form
 * @var StaticPage               $model
 */
?>

<div class="text-page-form">

    <?php $form = ActiveForm::begin(
        [
            'options'        => ['enctype' => 'multipart/form-data'],
            'id'             => 'text-page',
            'layout'         => 'horizontal',
            'validateOnBlur' => false,
        ]
    ); ?>

    <?= $form->field($model, 'title')->textInput() ?>
    <?= $form->field($model, 'url')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList(StaticPage::getStatusList()) ?>

    <?php
    echo $form->field($model, 'text')->widget(
        CKEditor::class,
        [
            'options'       => ['rows' => 6],
            'kcfinder'      => true,
            'clientOptions' => [
                'filebrowserUploadMethod' => 'form',
                'language'                => 'en',
                'extraPlugins'            => 'lightbox',
            ],
            'kcfOptions'    => [
                'uploadURL' => '@web/upload/editor',
                'uploadDir' => '@root/upload/editor',
                'dirPerms'  => 0777,
                'filePerms' => 0664,
            ],
            'preset'        => 'full'
        ]
    )
    ?>

    <?php $this->registerJs("CKEDITOR.plugins.addExternal('lightbox', '/js/ckeditor-plugins/lightbox/');"); ?>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-9">
            <?php if ($model->isNewRecord): ?>
                <?= Html::submitButton(
                    '<span class="glyphicon glyphicon-plus-sign"></span> Create',
                    ['class' => 'btn btn-success']
                ) ?>
            <?php else: ?>
                <?= Html::submitButton(
                    '<span class="glyphicon glyphicon-ok"></span> Save',
                    ['class' => 'btn btn-primary']
                ) ?>
            <?php endif; ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
