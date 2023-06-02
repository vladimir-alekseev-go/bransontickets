<?php

namespace backend\controllers;

use Throwable;
use webvimark\modules\UserManagement\components\GhostAccessControl;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

abstract class CrudController extends BaseController
{
    /**
     * Lists all models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = $this->modelSearchClass ? new $this->modelSearchClass : null;

        if ($this->modelSearchClass) {
            $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        } else {
            $modelClass = $this->modelClass;
            $dataProvider = new ActiveDataProvider(
                [
                    'query' => $modelClass::find(),
                    'sort'  => [
                        'defaultOrder' => [
                            'id' => SORT_DESC,
                        ],
                    ],
                ]
            );
        }

        return $this->renderIsAjax('index', compact('dataProvider', 'searchModel'));
    }

    /**
     * @param $id
     *
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->renderIsAjax(
            'view',
            [
                'model' => $this->findModel($id),
            ]
        );
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new $this->modelClass;

        $load = $model->load(Yii::$app->request->post());

        if ($load && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->renderIsAjax('create', compact('model'));
    }

    /**
     * @param $id
     *
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $post = (array)Yii::$app->request->post();

        $Sections = new $this->modelClass;

        $model = $Sections->findOne($id);

        if (empty($model)) {
            throw new NotFoundHttpException;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->renderIsAjax('update', compact('model'));
    }

    /**
     * @param $id
     *
     * @return Response
     * @throws Throwable
     * @throws StaleObjectException
     * @throws NotFoundHttpException
     */
    public function actionDelete($id): Response
    {
        $model = $this->findModel($id);
        $model->delete();

        return $this->redirect($this->getRedirectPage('delete', $model));
    }

    /**
     * Finds the model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param mixed $id
     *
     * @return ActiveRecord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id): ActiveRecord
    {
        $modelClass = $this->modelClass;
        $model = $modelClass::find()
            ->andWhere([$modelClass::tableName() . '.id' => $id])
            ->one();

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }
}
