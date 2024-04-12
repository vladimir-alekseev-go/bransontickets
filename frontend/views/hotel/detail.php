<?php

use common\models\form\HotelReservationForm;
use common\models\TrPosHotels;
use frontend\controllers\BaseController;

/**
 * @var TrPosHotels          $model
 * @var HotelReservationForm $HotelReservationForm
 */

$model = $HotelReservationForm->model;
$this->context->layout = BaseController::LAYOUT_ITEM_DETAIL;
Yii::$app->view->params['model'] = $model;

$this->title = $model->name;

?>

<?= $this->render('@app/views/components/item/menu-content', compact('model', 'HotelReservationForm', 'showsRecommended')) ?>
