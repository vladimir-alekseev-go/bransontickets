<?php

namespace backend\controllers;

use common\models\Feedback;
use backend\models\search\FeedbackSearch;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class FeedbackController extends CrudController
{
    public $modelClass = Feedback::class;
    public $modelSearchClass = FeedbackSearch::class;

    /**
     * @param $id
     *
     * @return string|void|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id): Response
    {
        throw new NotFoundHttpException;
    }

    /**
     * @return string|void|Response
     * @throws NotFoundHttpException
     */
    public function actionCreate(): Response
    {
        throw new NotFoundHttpException;
    }

    /**
     * @param $id
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id): Response
    {
        throw new NotFoundHttpException;
    }
}
