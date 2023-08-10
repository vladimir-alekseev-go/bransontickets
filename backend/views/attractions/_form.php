<?php

/**
 * @var $uploadItemsBanner
 */

use backend\models\forms\AttractionsForm;
/*use common\models\LocationItem;*/
use common\models\TrAttractions;
use webvimark\modules\UserManagement\UserManagementModule;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * @var AttractionsForm $model
 */

?>
<div class="attractions-form">
    <?php $form = ActiveForm::begin(
        [
            'options'        => ['enctype' => 'multipart/form-data'],
            'id'             => 'attractions',
            'layout'         => 'horizontal',
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
            TrAttractions::getActive()->orderBy('name')->where(['not', ['id' => $model->id]])->all(),
            'id_external',
            'name'
        ),
        ['multiple' => true, 'size' => 18, 'prompt' => '']
    )->label('See also') ?>

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-9">
            <?= Html::submitButton(
                '<span class="glyphicon glyphicon-ok"></span> ' . UserManagementModule::t('back', 'Save'),
                ['class' => 'btn btn-primary']
            ) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
