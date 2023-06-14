<?php

namespace backend\controllers;

use backend\models\search\TrTheatersSearch;
use common\models\TrTheaters;
use Exception;
use Yii;

class LocationsController extends CrudController
{
    public $modelClass = TrTheaters::class;
    public $modelClassSearch = TrTheatersSearch::class;

    public function actionIndex()
    {
        if (Yii::$app->request->post()) {
            try {
                $counts = TrTheaters::setLocations(1, 30);
                Yii::$app->session->setFlash(
                    'success',
                    'Was updated ' . $counts . ' locations. Click update again to update next part of coordinate.'
                );
            } catch (Exception $e) {
                Yii::$app->session->setFlash('geocode', $e->getMessage());
            }
            return $this->redirect(['index']);
        }
        $searchModel = new $this->modelClassSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        $locationsEmpty = TrTheaters::find()->where(
            ['status' => TrTheaters::STATUS_ACTIVE, 'location_lat' => null]
        )->count();
        $locationsNotFinded = TrTheaters::find()->where(
            ['status' => TrTheaters::STATUS_ACTIVE, 'location_lat' => 0]
        )->count();
        $locations = TrTheaters::find()->where(['status' => TrTheaters::STATUS_ACTIVE])->count();
        return $this->renderIsAjax(
            '@backend/views/locations/index',
            compact(
                'searchModel',
                'dataProvider',
                'locationsEmpty',
                'locationsNotFinded',
                'locations'
            )
        );
    }
}
