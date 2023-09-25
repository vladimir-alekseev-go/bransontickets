<?php

namespace common\widgets\commonWlAssets;

use yii\web\AssetBundle;

class CommonWlAsset extends AssetBundle
{
    public $js = [
        'js/order.js',
    ];

    public function init()
    {
        $this->sourcePath = __DIR__.'/assets';
        parent::init();
    }
}