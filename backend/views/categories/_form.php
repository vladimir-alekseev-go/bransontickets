<?php

use common\models\TrCategories;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var TrCategories $model
 */

?>
<div class="attractions-form">
    <?php $form = ActiveForm::begin(
        [
            'options' => ['enctype' => 'multipart/form-data'],
            'id' => 'categories',
            'layout' => 'horizontal',
            'validateOnBlur' => false,
        ]
    ); ?>
    <?= $form->field($model, 'sort_shows') ?>
    <?= $form->field($model, 'sort_attractions') ?>
    <!-- $form->field($model, 'sort_hotels') -->
    <!-- $form->field($model, 'sort_dining') --> 

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-9">
            <?= Html::submitButton(
                '<span class="glyphicon glyphicon-ok"></span> Save',
                ['class' => 'btn btn-primary']
            ) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
