<?php

use common\models\form\Search;
use yii\widgets\ActiveForm;

/**
 * @var Search $Search
 * @var int                  $itemCount
 */

$Search = $this->params['view']['search'];

?>
<div class="panel-sorting list-<?= $Search->display ?>" id="panel-sorting">
    <div class="items-count"><b id="items-count"><?= $itemCount ?> item<?= $itemCount === 1 ? '' : 's' ?></b> found
    </div>
    <?php
    $form = ActiveForm::begin(
        [
            'options' => ['class' => 'list-filter-sorting'],
            'id' => 'list-filter-sorting',
            'validateOnSubmit' => false,
        ]
    ); ?>
    <?= $form->field($Search, "fieldSort")->dropDownList($Search::sortList(), ['id' => 'panel-sorting-field'])
        ->label(false)
    ?>
    Sort: <span class="js-selected selected"><?= $Search::sortList()[$Search->fieldSort] ?> <i class="fa fa-angle-down"></i></span>
    <?php ActiveForm::end(); ?>
</div>
