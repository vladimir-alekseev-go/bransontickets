<?php

namespace backend\controllers;

use common\models\FeedbackSettings;
use Yii;
use yii\web\Response;

class FeedbackSettingsController extends BaseController
{
    /**
     * @return string|Response
     */
    public function actionIndex()
    {
        /**
         * @var FeedbackSettings $model
         */
        $model = FeedbackSettings::find()->orderBy(['id' => SORT_DESC])->limit(1)->one();

        if (empty($model)) {
            $model = new FeedbackSettings();
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->renderIsAjax('update', compact('model'));
    }
}
