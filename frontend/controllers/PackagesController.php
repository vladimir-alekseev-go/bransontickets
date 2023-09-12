<?php

namespace frontend\controllers;

use common\controllers\PackagesControllerTrait;
use common\models\form\PackageForm;
use common\models\TrBasket;
use common\models\VacationPackage;
use frontend\widgets\vacationPackagesList\VacationPackagesListWidget;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class PackagesController extends Controller
{
    use PackagesControllerTrait;

    protected $VPLWidget = VacationPackagesListWidget::class;

    public function actionIndex()
    {
        $VPLWidget = new $this->VPLWidget();

        Yii::$app->view->params['view']['search'] = $VPLWidget->search;

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return $VPLWidget->run();
        }

        return $this->render('index', compact('VPLWidget'));
    }

    /**
     * @param      $code
     * @param null $packageModifyId
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDetail($code, $packageModifyId = null)
    {
        /**
         * @var VacationPackage $VacationPackage
         */
        $VacationPackage = VacationPackage::getActive()->where(['code' => $code])->one();
        if (!$VacationPackage) {
            throw new NotFoundHttpException;
        }
        //$Basket = TrBasket::build();

        $PackageForm = new PackageForm();
        $PackageForm->setVacationPackage($VacationPackage);
        $PackageForm->setPackage_modify_id($packageModifyId);

        return $this->render('detail', compact('VacationPackage', 'PackageForm'));
    }
}
