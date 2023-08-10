<?php

use common\models\ContentFiles;
use common\models\UploadForm;
use yii\bootstrap\ActiveForm;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Inflector;

/**
 * @var ContentFiles $file
 * @var UploadForm   $uploadForm
 * @var ActiveForm   $form
 * @var string       $label
 * @var string       $field
 * @var bool         $canBeDeleted
 * @var ActiveRecord $model
 */
?>

<?php
$file = $model->{Inflector::variablize($field)};

$error = $model->getFirstError($field . '_id');
if (!empty($error)) {
    $uploadForm->addError('file', $error);
}

if ($file) {
    $fileInfo = pathinfo($file->getUrl());
    $extension = $fileInfo ? $fileInfo['extension'] : null;
    $viewFile = null;

    if (!in_array($extension, UploadForm::IMG_EXTENSIONS, true)) {
        $viewFile = Html::a($fileInfo['basename'], $file->getUrl(), ['target' => '_blank']);
    }

    ?>
    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-9">
            <?= Html::img($file->getUrl(), ['style' => 'max-width:300px']) ?>
            <?= $viewFile ?>
            <?php if ($canBeDeleted) { ?>
                <div class="checkbox">
                    <label><input type="checkbox" name="<?= 'delete' . Inflector::camelize($field) ?>" value="1"/>
                        Delete</label>
                </div>
            <?php } ?>
        </div>
    </div>
<?php } ?>
<?php $fileInput = $form->field($uploadForm, 'file')->fileInput()->label($label);
if ($file) {
    $fileInput->hint('Replace the existing file');
}
echo $fileInput;
?>
