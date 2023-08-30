<?php

use common\models\form\HotelReservationForm;
use common\models\TrPosHotels;

/**
 * @var TrPosHotels          $model
 * @var HotelReservationForm $HotelReservationForm
 */

$model = $HotelReservationForm->model;
$this->context->layout = 'item-detail';
Yii::$app->view->params['model'] = $model;

$this->title = $model->name;

?>

<?= $this->render('@app/views/components/item/menu-content', compact('model', 'HotelReservationForm', 'showsRecommended')) ?>
