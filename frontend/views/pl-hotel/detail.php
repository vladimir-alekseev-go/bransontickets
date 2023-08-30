<?php

use common\models\form\PlHotelReservationForm;
use common\models\TrPosPlHotels;

/**
 * @var TrPosPlHotels          $model
 * @var PlHotelReservationForm $ReservationForm
 */

$model = $ReservationForm->model;
$this->context->layout = 'item-detail';
Yii::$app->view->params['model'] = $model;

$this->title = $model->name;

?>

<?= $this->render('@app/views/components/item/menu-content', [
        'model' => $model,
        'HotelReservationForm' => $ReservationForm,
        'showsRecommended' => $showsRecommended
    ]
) ?>
