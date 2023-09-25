<?php
namespace common\controllers;

use common\models\TrBasket;
use common\models\form\PackageForm;
use common\models\VacationPackage;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;

trait PackagesControllerTrait
{
    /**
     * @param $code
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionOverview($code)
    {
        $VacationPackage = VacationPackage::getActive()->where(['code'=>$code])->one();
        if (!$VacationPackage) {
            throw new NotFoundHttpException;
        }
        return $this->render('overview', compact('VacationPackage'));
    }

    /**
     * @param      $code
     * @param null $packageModifyId
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionBuy($code, $packageModifyId = null)
    {
        /**
         * @var VacationPackage $VacationPackage
         */
        $VacationPackage = VacationPackage::getActive()->where(['code'=>$code])->one();
        if (!$VacationPackage) {
            throw new NotFoundHttpException;
        }
        $Basket = TrBasket::build();
        
        $PackageForm = new PackageForm();
        $PackageForm->setVacationPackage($VacationPackage);
        $PackageForm->setPackage_modify_id($packageModifyId);
        
        return $this->render('buy', compact('VacationPackage', 'PackageForm', 'Basket'));
    }

    public function actionAddToCart()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $form = ActiveForm::begin();
        
        $PackageForm = new PackageForm;
        if ($PackageForm->load(Yii::$app->request->post()) && $PackageForm->addToCart()) {
            return ['redirectUrl'=>Url::to(['order/cart', 'changed'=>true])];
        }

        return [
            'errors' => $PackageForm->errors,
            'errorsHtml' => $form->errorSummary(
                $PackageForm,
                ['header'=>'<div class="alert alert-danger alert-dismissible has-error">', 'footer'=>'</div>']
            )
        ];
    }

    public function actionSelectedInfo()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $packageForm = new PackageForm();
        $packageForm->load(Yii::$app->request->post());
        if (!$packageForm->getPackage()) {
        	return '';
        }
        return $this->render('selected-info', compact('packageForm'));
    }
}
