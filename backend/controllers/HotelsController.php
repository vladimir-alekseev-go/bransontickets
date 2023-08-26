<?php

namespace backend\controllers;

use backend\models\search\RedirectsSearch;
use backend\models\search\TrPosHotelsSearch;
use common\models\redirects\Redirects;
use common\models\TrPosHotels;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class HotelsController extends CrudController
{
    public $modelClass = TrPosHotels::class;
    public $modelSearchClass = TrPosHotelsSearch::class;

    public function actionView($id)
    {
        /**
         * @var TrPosHotels $model
         */
        $model = $this->findModel($id);

        $RedirectsSearch = new RedirectsSearch();
        $RedirectsSearch->setAttributes(
            [
                'item_id' => $model->id_external,
                'category' => Redirects::CATEGORY_HOTEL_POS,
            ]
        );
        $dataProviderRedirects = $RedirectsSearch->search([]);

        return $this->render(
            'view',
            [
                'model' => $model,
                'dataProviderRedirects' => $dataProviderRedirects,
            ]
        );
    }

    /**
     * @return string|void|Response
     * @throws NotFoundHttpException
     */
    public function actionCreate()
    {
        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }

    /**
     * @param $id
     *
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        if (!empty(Yii::$app->request->post())) {
            $cache = Yii::$app->cache;
            $cache->delete('popularLodging');
        }

        return parent::actionUpdate($id);
    }
}
