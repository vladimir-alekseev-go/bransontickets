<?php

namespace frontend\controllers;

use common\analytics\Analytics;
use common\models\OrderForm;
use DateTime;
use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

trait TicketsControllerTrait
{
    public function actionTicketsRedirect($code, $date)
    {
        $model = $this->generalData($code);
        $this->redirect($model->detailURL($code, $date), 301);
    }

    /**
     * @param $code
     * @param $date
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionTickets($code, $date)
    {
        $date = str_replace('_', ' ', $date);
        try {
            $d = new DateTime($date);
        } catch (Exception $e) {
            return $this->redirect(Yii::$app->urlManager->createUrl([$this->id . '/detail', 'code' => $code]), 301);
        }

        if ((new DateTime($d->format('Y-m-d')))->format('U') < (new DateTime(date('Y-m-d')))->format('U')) {
            return $this->redirect(Yii::$app->urlManager->createUrl([$this->id . '/detail', 'code' => $code]), 301);
        }

        $model = $this->generalData($code);

        $this->layout = false;

        if (!Yii::$app->request->isAjax) {
            return $this->redirect(
                $model->getUrl(
                    array_merge(
                        Yii::$app->request->get(),
                        [
                            'tickets-on-date' => $d->format('Y-m-d H:i:s')
                        ]
                    )
                ),
                301
            );
        }

        $OrderForm = new OrderForm();
        $OrderForm->setAttributes(['date' => $d, 'model' => $model, 'allotmentId' => Yii::$app->request->get('allotmentId')]);
        $OrderForm->initData();

        if (Yii::$app->request->get()) {
            $OrderForm->load(Yii::$app->request->get());
        }

        $OrderForm->initPrice();
        if (!Yii::$app->request->post()) {
            $OrderForm->initPackageModify();
        }

        if ($OrderForm->load(Yii::$app->request->post()) && $OrderForm->validate() && $res = $OrderForm->run()) {
            Yii::$app->response->statusCode = 302;
            return Url::to(['order/cart', 'changed' => true]);
        }

        Analytics::addEvent(
            Analytics::EVENT_DETAIL,
            [['itemId' => $model->id, 'itemType' => $model->type]],
            ['list' => 'all']
        );

        return $this->render(
            'order',
            compact(
                'model',
                'OrderForm'
            )
        );
    }

    /**
     * @param $code
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function generalData($code)
    {
        $mainClass = $this::mainClass;

        $model = $mainClass::find()->where(["code" => $code, "status" => 1])->one();
        if (!$model) {
            throw new NotFoundHttpException;
        }

        return $model;
    }
}
