<?php

use backend\models\forms\AttractionsForm;

/**
 * @var AttractionsForm $model
 */

$this->title = 'Editing attractions: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Attractions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Editing';
?>
<div class="attractions-update">
	<div class="panel panel-default">
		<div class="panel-body">
			<?= $this->render('_form', compact('model', 'uploadItemsBanner')) ?>
		</div>
	</div>
</div>