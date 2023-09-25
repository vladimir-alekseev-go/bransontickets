<?php

namespace frontend\controllers;

use common\models\OrderModifyForm;
use common\models\TrBasket;
use common\models\TrOrders;
use common\models\User;
use frontend\widgets\scheduleSlider\ScheduleSliderWidget;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class OrderController extends Controller
{
    use \common\controllers\OrderController;

    /**
     * @param $id
     *
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionCancellationPolicyVacationPackage($id)
    {
        $this->layout = false;
        $Basket = TrBasket::build();
        if ($VacationPackage = $Basket->getVacationPackage($id)) {
            return $this->render('cart/cancel-policy', ['packages' => [$VacationPackage]]);
        }

        if (!Yii::$app->request->isAjax) {
            return $this->redirect(['order/cart']);
        }

        throw new NotFoundHttpException;
    }

    /**
     * @param null $packageId
     *
     * @return string|Response
     */
    public function actionCancellationPolicy($packageId = null)
    {
        $this->layout = false;
        $packages = [];
        $Basket = TrBasket::build();

        if ($packageId) {
            $package = $Basket->getPackage($packageId);
            return $this->render('cart/cancel-policy', ['packages' => [$package]]);
        }

        foreach ($Basket->getUniqueVacationPackages() as $VacationPackage) {
            $packages[] = $VacationPackage;
        }
        foreach ($Basket->getPackages() as $package) {
            $packages[] = $package;
        }

        if (empty($packages) && !Yii::$app->request->isAjax) {
            return $this->redirect(['order/cart']);
        }

        return $this->render('cart/cancel-policy', compact('packages'));
    }


    /**
     * @param $orderNumber
     * @param $packageNumber
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionModification($orderNumber, $packageNumber)
    {
        if (Yii::$app->user->isGuest) {
            return '<script>document.location.reload()</script>';
        }

        $tripium_id = User::getCustomerTripiumID();

        $Order = TrOrders::find()->where(["tripium_user_id"=>$tripium_id, "order_number"=>$orderNumber])->one();

        if (!$Order) {
            throw new NotFoundHttpException;
        }

        $package = $Order->getPackage($packageNumber);

        if (!$package) {
            throw new NotFoundHttpException;
        }
        $ScheduleSlider = new ScheduleSliderWidget(
            [
                'model' => $package->item,
                'date' => $package->startDataTime,
                'package' => $package,
                'scheduleIsShow' => false
            ]
        );

        $OrderForm = new OrderModifyForm();
        $OrderForm->coupon_code = $Order->getCoupon() ? $Order->getCoupon()->code : null;

        return $this->renderAjax('modification', compact('ScheduleSlider', 'package', 'Order', 'OrderForm'));
    }
}
