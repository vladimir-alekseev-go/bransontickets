<?php

namespace backend\controllers;

use Yii;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends BaseController
{
    public $freeAccessActions = ['login', 'logout', 'confirm-registration-email', 'index'];

    /**
     * @return string|Response
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('/user-management/auth/login');
        }

        return $this->render('index');
    }
}
