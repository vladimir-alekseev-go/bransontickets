<?php

namespace frontend\controllers;

use common\models\form\HotelReservationForm;
use common\models\TrPosHotels;
use Yii;
use yii\db\Expression;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class HotelController extends Controller
{
    /**
     * @param $code
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionDetail($code): string
    {
        /**
         * @var TrPosHotels $model
         */
        $model = TrPosHotels::getActive()->where(['code' => $code])->one();
        
        if (!$model) {
            throw new NotFoundHttpException;
        }

        $HotelReservationForm = new HotelReservationForm(
            [
                'model' => $model,
                'packageId' => Yii::$app->getRequest()->get('HotelReservationForm')['packageId'] ?? null,
                'isChange' => Yii::$app->getRequest()->get('isChange')
            ]
        );

        $HotelReservationForm->load(Yii::$app->getRequest()->get());

        if (Yii::$app->request->isAjax) {
            $this->layout = false;
            return $this->render('@app/views/components/item/menu-content/hotel-rooms', compact('HotelReservationForm'));
        }
        
        $showsRecommended = TrPosHotels::getActive()
            ->orderBy(new Expression('rand()'))
            ->limit(6)
            ->all();

        return $this->render('detail', compact('HotelReservationForm', 'showsRecommended'));
    }
}
