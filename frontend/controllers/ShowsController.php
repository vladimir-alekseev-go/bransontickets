<?php

namespace frontend\controllers;

use common\models\TrShows;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ShowsController extends Controller
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
         * @var TrShows $model
         */
        $model = TrShows::getActive()->where(['code' => $code])->with(
            [
                'itemsPhoto' => static function (ActiveQuery $query) {
                    $query->with(['photo', 'preview']);
                }
            ]
        )->one();

        if (!$model) {
            throw new NotFoundHttpException;
        }

        $showsRecommended = TrShows::getActive()
            ->orderBy(new Expression('rand()'))
            ->limit(6)
            ->all();

        return $this->render('detail', compact('model', 'showsRecommended'));
    }
}
