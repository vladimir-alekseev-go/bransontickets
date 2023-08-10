<?php

namespace backend\controllers;

use backend\models\search\TrCategoriesSearch;
use common\models\TrAttractions;
use common\models\TrCategories;
/*use common\models\TrLunchs;
use common\models\TrPosHotels;
use common\models\TrPosPlHotels;*/
use common\models\TrShows;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class CategoriesController extends CrudController
{
    public $modelClass = TrCategories::class;
    public $modelSearchClass = TrCategoriesSearch::class;

    /**
     * @return string|void|Response
     * @throws NotFoundHttpException
     */
    public function actionCreate()
    {
        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }

    public function actionUpdate($id)
    {
        if (Yii::$app->request->post()) {
            $cache = Yii::$app->cache;
            $cache->delete(TrShows::TYPE . '.Categories');
            $cache->delete(TrAttractions::TYPE . '.Categories');
            /*$cache->delete(TrLunchs::TYPE . '.Categories');
            $cache->delete(TrPosHotels::TYPE . '.Categories');
            $cache->delete(TrPosPlHotels::TYPE . '.Categories');*/
        }
        return parent::actionUpdate($id);
    }
}
