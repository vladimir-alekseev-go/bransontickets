<?php

namespace frontend\assets;

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
        '/css/font-circe/circe.css',
        '/css/main.css',
        '/css/header.css',
        '/css/footer.css'
    ];
    public $js = [
        '/js/header.js',
        '/js/jquery.easing.1.3.js',
        '/js/general.js',
    ];
    public $depends = [
        YiiAsset::class,
        BootstrapAsset::class,
        JqueryAsset::class
    ];
}
