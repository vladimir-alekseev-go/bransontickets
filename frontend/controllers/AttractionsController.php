<?php

namespace frontend\controllers;

use common\models\TrAttractions;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class AttractionsController extends Controller
{
    /**
     * @param $code
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionDetail($code)
    {
        $model = TrAttractions::getActive()->where(['code' => $code])->with(
            [
                'itemsPhoto' => static function (ActiveQuery $query) {
                    $query->with(['photo', 'preview']);
                }
            ]
        )->one();

        if (!$model) {
            throw new NotFoundHttpException;
        }

        $showsRecommended = TrAttractions::getActive()
            ->orderBy(new Expression('rand()'))
            ->limit(6)
            ->all();

        return $this->render('../shows/detail', compact('model', 'showsRecommended'));
    }
}
