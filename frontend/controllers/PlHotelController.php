<?php
//
//namespace frontend\controllers;
//
//use common\models\form\PlHotelReservationForm;
//use common\models\TrPosPlHotels;
//use Yii;
//use yii\db\Expression;
//use yii\web\Controller;
//use yii\web\NotFoundHttpException;
//use yii\web\Response;
///** @deprecated  */
//class PlHotelController extends Controller
//{
//    /**
//     * @return Response
//     */
//    public function actionIndex(): Response
//    {
//        return $this->redirect(['lodging/index']);
//    }
//
//    /**
//     * @param $code
//     *
//     * @return string
//     * @throws NotFoundHttpException
//     */
//    public function actionDetail($code): string
//    {
//        if (Yii::$app->request->isAjax) {
//            return $this->actionRooms($code);
//        }
//
//        $model = TrPosPlHotels::getActive()->where(['code' => $code])->one();
//        if ($model === null) {
//            throw new NotFoundHttpException();
//        }
//
//        $ReservationForm = new PlHotelReservationForm(['model' => $model]);
//
//        $showsRecommended = TrPosPlHotels::getActive()
//            ->orderBy(new Expression('rand()'))
//            ->limit(6)
//            ->all();
//
//        return $this->render('detail', compact('ReservationForm', 'showsRecommended'));
//    }
//
//    /**
//     * @param $code
//     *
//     * @return string
//     * @throws NotFoundHttpException
//     */
//    public function actionRooms($code): string
//    {
//        $this->layout = false;
//
//        $model = TrPosPlHotels::getActive()->with('relatedPhotos')->where(['code' => $code])->one();
//
//        if (!$model) {
//            throw new NotFoundHttpException;
//        }
//
//        $ReservationForm = new PlHotelReservationForm(['model' => $model]);
//
//        return $this->render('@app/views/components/item/menu-content/hotel-pl-rooms', compact('ReservationForm'));
//    }
//
//    /**
//     * @param string $code
//     * @param int    $id
//     *
//     * @return mixed
//     * @throws NotFoundHttpException|\Throwable
//     */
//    public function actionReservation($code, $id)
//    {
//        $this->layout = false;
//
//        /**
//         * @var $model TrPosPlHotels
//         */
//        $model = TrPosPlHotels::getActive()->where(['code' => $code])->one();
//
//        if (!$model) {
//            throw new NotFoundHttpException;
//        }
//
//        $HotelReservationForm = new PlHotelReservationForm(
//            [
//                'roomId' => $id,
//                'model' => $model,
//                'packageId' => Yii::$app->getRequest()->get('packageId'),
//                'ppnBundle' => Yii::$app->getRequest()->get('ppnBundle'),
//            ]
//        );
//
//        if (!$HotelReservationForm->getRoomType()) {
//            throw new NotFoundHttpException;
//        }
//
//        if ($HotelReservationForm->load(Yii::$app->getRequest()->post()) && $HotelReservationForm->addToCart()) {
//            return $this->redirect(['order/cart'], 302);
//        }
//
//        return $this->render('reservation', compact('HotelReservationForm'));
//    }
//}
