<?php

use frontend\controllers\BaseController;
use common\helpers\General;
use common\models\TrAttractions;
use common\models\TrShows;

/**
 * @var TrShows|TrAttractions $model
 */

$this->context->layout = BaseController::LAYOUT_ITEM_DETAIL;
Yii::$app->view->params['model'] = $model;

$this->title = $model->name;

?>
<?= $this->render('@app/views/components/item/menu-content', compact('model', 'ScheduleSlider', 'VPLWidget')) ?>
<?= $this->render('@app/views/components/item/description', compact('model')) ?>
<?= $this->render('@app/views/components/item/gallery', compact('model')) ?>
<?= $this->render('@app/views/components/item/also', compact('showsRecommended')) ?>

