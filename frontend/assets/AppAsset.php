<?php

namespace frontend\assets;

use yii\bootstrap\BootstrapAsset;
use yii\bootstrap\BootstrapPluginAsset;
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
        'https://fonts.googleapis.com/css?family=Poppins:400,500,600',
//        '/css/font-circe/circe.css',
        '/fonts/branson-tickets/styles.css',
        '/css/bootstrap-datepicker.min.css',
        '/css/jquery-ui.min.css',
        '/css/jquery-ui.structure.min.css',
        '/css/jquery.scrollbar.css',
        '/css/main.css',
        '/css/header.css',
        '/css/footer.css'
    ];
    public $js = [
        '/js/header.js',
        '/js/jquery-ui.min.js',
        '/js/jquery.easing.1.3.js',
        '/js/jquery.scrollbar.min.js',
        '/js/general.js',
    ];
    public $depends = [
        YiiAsset::class,
        BootstrapAsset::class,
        BootstrapPluginAsset::class,
        JqueryAsset::class
    ];
}
