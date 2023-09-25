<?php

use common\models\TrAttractions;
use common\models\TrShows;

/**
 * @var TrShows|TrAttractions $model
 */

$this->context->layout = 'item-detail';
Yii::$app->view->params['model'] = $model;

$this->title = $model->name;
?>

<?= $this->render('@app/views/components/item/menu-content', compact('model', 'showsRecommended', 'ScheduleSlider', 'VPLWidget')) ?>

