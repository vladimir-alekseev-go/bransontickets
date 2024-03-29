<?php

/**
 * @var $uploadItemsBanner
 */

/*use common\models\LocationItem;*/
use common\models\TrShows;
use webvimark\modules\UserManagement\UserManagementModule;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

?>

<div class="shows-form">

    <?php $form = ActiveForm::begin(
        [
            'options' => ['enctype' => 'multipart/form-data'],
            'id' => 'shows',
            'layout' => 'horizontal',
            'validateOnBlur' => false,
        ]
    ); ?>

    <?= $form->field($model, 'show_in_footer')->dropDownList(['No', 'Yes']) ?>
    <!-- $form->field($model, 'location_item_id')->label('Items group order')->dropDownList(
        ArrayHelper::map(LocationItem::find()->all(), 'id', 'location_name'),
        ['prompt' => '']
    ) -->

    <?= $this->render(
        '@backend/views/components/upload-file',
        [
            'model'        => $model,
            'uploadForm'   => $uploadItemsBanner,
            'form'         => $form,
            'label'        => 'Banner',
            'field'        => 'image',
            'canBeDeleted' => true,
        ]
    ) ?>

    <?= $form->field($model, 'display_image')->dropDownList(['No', 'Yes'])->label('Display Banner') ?>

    <?= $form->field($model, 'similarIds')->dropDownList(
        ArrayHelper::map(
            TrShows::getActive()->orderBy('name')->where(['not', ['id' => $model->id]])->all(),
            'id_external',
            'name'
        ),
        ['multiple' => true, 'size' => 18, 'prompt' => '']
    )->label('See also') ?>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-9">
            <?php if ($model->isNewRecord) { ?>
                <?= Html::submitButton(
                    '<span class="glyphicon glyphicon-plus-sign"></span> ' . UserManagementModule::t('back', 'Create'),
                    ['class' => 'btn btn-success']
                ) ?>
            <?php } else { ?>
                <?= Html::submitButton(
                    '<span class="glyphicon glyphicon-ok"></span> ' . UserManagementModule::t('back', 'Save'),
                    ['class' => 'btn btn-primary']
                ) ?>
            <?php } ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

