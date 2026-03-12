<?php

namespace backend\assets;

use dmstr\web\AdminLteAsset;
use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
    ];
    public $js = [
        'js/site.js'
    ];
    public $depends = [
        AdminLteAsset::class,
    ];
}
