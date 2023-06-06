<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>

<div class="user-form">

	<?php $form = ActiveForm::begin([
		'id'=>'Vacancies',
		'layout'=>'horizontal',
		'validateOnBlur' => false
	]);?>
	
	<?= $form->field($model, 'address')?>
	<?= $form->field($model, 'phone')?>
	<?= $form->field($model, 'email')?>
	
	<div class="form-group">
		<div class="col-sm-offset-3 col-sm-9">
			<?php if ( $model->isNewRecord ): ?>
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
