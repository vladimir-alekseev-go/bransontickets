<?php

namespace console\controllers;

use common\helpers\AssetHelper;
use Yii;
use yii\console\Controller;

/**
 * Clear data
 */
class ClearController extends Controller
{
    /**
     * Delete all assets
     */
    public function actionAssets()
    {
        AssetHelper::clear(Yii::getAlias('@frontend') . '/web/assets');
    }

    /**
     * Delete all minify
     */
    public function actionMinify()
    {
        AssetHelper::clearMinify(Yii::getAlias('@frontend') . '/web/minify');
    }
}
