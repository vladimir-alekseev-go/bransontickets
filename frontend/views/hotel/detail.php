<?php

use common\models\TrPosHotels;

/**
 * @var TrPosHotels $model
 */

$model = $HotelReservationForm->model;
$this->context->layout = 'item-detail';
Yii::$app->view->params['model'] = $model;

$this->title = $model->name;

?>

<?= $this->render('@app/views/components/item/menu-content', compact('model', 'HotelReservationForm', 'showsRecommended')) ?>
