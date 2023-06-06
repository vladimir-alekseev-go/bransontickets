<?php

namespace frontend\assets;

use yidas\yii\fontawesome\FontawesomeAsset;
use yii\bootstrap\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\web\YiiAsset;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://fonts.cdnfonts.com/css/circe',
        '/css/main.css',
        '/css/header.css',
        '/css/footer.css'
    ];
    public $js = [
        '/js/header.js'
    ];
    public $depends = [
        YiiAsset::class,
        BootstrapAsset::class,
        JqueryAsset::class,
        FontawesomeAsset::class
    ];
}
