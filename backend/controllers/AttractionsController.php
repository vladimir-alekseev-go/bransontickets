<?php

namespace backend\controllers;

use backend\models\forms\AttractionsForm;
use backend\models\search\RedirectsSearch;
use common\controllers\UploadFileTrait;
use common\models\attractions\AttractionsSearch;
use common\models\redirects\Redirects;
use common\models\TrAttractions;
use common\models\upload\UploadItemsBanner;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class AttractionsController extends CrudController
{
    use UploadFileTrait;

    public $modelClass = TrAttractions::class;
    public $modelSearchClass = AttractionsSearch::class;

    public function actionView($id)
    {
        /**
         * @var TrAttractions $model
         */
        $model = $this->findModel($id);

        $RedirectsSearch = new RedirectsSearch();
        $RedirectsSearch->setAttributes(
            [
                'item_id' => $model->id_external,
                'category' => Redirects::CATEGORY_ATTRACTION,
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
     * @param $id
     *
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        /**
         * @var TrAttractions $model
         */
        $post = Yii::$app->request->post();

        $model = AttractionsForm::find()->where(['id' => $id])->one();
        if (!$model) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $uploadItemsBanner = new UploadItemsBanner();

        if ($model->load($post) && $model->save()) {
            $cache = Yii::$app->cache;
            $cache->delete('popularShow');

            $uploadItemsBanner->file = UploadedFile::getInstance($uploadItemsBanner, 'file');

            if ($uploadItemsBanner->validate() && $uploadItemsBanner->upload()) {
                if (!empty($model->image)) {
                    $model->image->delete();
                }
                $model->image_id = $uploadItemsBanner->id;
                $model->save();
            } elseif (!empty($post['deleteImageId'])) {
                if (!empty($model->image)) {
                    $model->image->delete();
                }
                $model->image_id = null;
                $model->save();
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->renderIsAjax('update', compact('model', 'uploadItemsBanner'));
    }

    /**
     * @return string|void|Response
     * @throws NotFoundHttpException
     */
    public function actionCreate()
    {
        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }
}
