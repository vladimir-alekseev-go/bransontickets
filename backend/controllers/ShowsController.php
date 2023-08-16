<?php

namespace backend\controllers;

use backend\models\forms\ShowsForm;
use backend\models\search\RedirectsSearch;
use common\models\redirects\Redirects;
use common\models\shows\ShowsSearch;
use common\models\TrShows;
use common\models\upload\UploadItemsBanner;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class ShowsController extends CrudController
{
    public $modelClass = TrShows::class;
    public $modelSearchClass = ShowsSearch::class;

    public function actionView($id)
    {
        /**
         * @var TrShows $model
         */
        $model = $this->findModel($id);

        $RedirectsSearch = new RedirectsSearch();
        $RedirectsSearch->setAttributes(
            [
                'item_id' => $model->id_external,
                'category' => Redirects::CATEGORY_SHOW,
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

    public function actionUpdate($id)
    {
        $post = Yii::$app->request->post();

        $model = ShowsForm::find()->where(['id' => $id])->one();
        if (!$model) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $uploadItemsBanner = new UploadItemsBanner();

        if ($model->load($post) && $model->save()) {
            $cache = Yii::$app->cache;
            $cache->delete('popularShow');

            var_dump(UploadedFile::getInstance($uploadItemsBanner, 'file'));
            
            $uploadItemsBanner->file = UploadedFile::getInstance($uploadItemsBanner, 'file');

            var_dump($uploadItemsBanner->file);

            if ($uploadItemsBanner->validate() && $uploadItemsBanner->upload()) {
                if (!empty($model->image)) {
                    $model->image->delete();
                }
                var_dump($uploadItemsBanner->id);
                exit();
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
