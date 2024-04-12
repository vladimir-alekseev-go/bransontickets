<?php

namespace frontend\components;

use common\models\StaticPage;
use frontend\controllers\BaseController;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\ErrorAction;

class CustomerErrorAction extends ErrorAction
{
    public $layout;

    /**
     * @return string
     * @throws InvalidConfigException
     */
    public function run()
    {
        $relativeUrl = '/' . Yii::$app->request->getPathInfo();

        if (strlen($relativeUrl) <= 1) {
            return parent::run();
        }

        $staticPage = StaticPage::find()->where(['url' => $relativeUrl, 'status' => StaticPage::ACTIVE])->one();

        if ($staticPage) {
            Yii::$app->response->statusCode = 200;

            Yii::$app->errorHandler->exception->statusCode = 200;
            $this->controller->layout = BaseController::LAYOUT_STATIC_PAGE;

            return $this->controller->render('static-page', compact('staticPage'));
        }

        return parent::run();
    }
}
