<?php

use common\models\StaticPage;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
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
    echo $form->field($model, 'text')->widget(CKEditor::class, [
        'editorOptions' => ElFinder::ckeditorOptions('elfinder', [
            'preset' => 'full',
            'inline' => false,
            'path' => 'editor',
            'filter' => 'image',
            'filebrowserUploadMethod' => 'form',
        ]),
    ]);
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
