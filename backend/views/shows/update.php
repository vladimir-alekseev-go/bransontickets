<?php

use webvimark\modules\UserManagement\UserManagementModule;

$this->title = UserManagementModule::t('back', 'Editing shows: ') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => UserManagementModule::t('back', 'Shows'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = UserManagementModule::t('back', 'Editing');
?>

<div class="shows-update">
	<div class="panel panel-default">
		<div class="panel-body">
			<?= $this->render('_form', compact('model', 'uploadItemsBanner', 'uploadShowsSeatMap')) ?>
		</div>
	</div>
</div>
