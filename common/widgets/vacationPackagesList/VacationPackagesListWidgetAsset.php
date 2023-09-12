<?php

namespace common\widgets\vacationPackagesList;

use yii\web\AssetBundle;

class VacationPackagesListWidgetAsset extends AssetBundle
{
    public $js = [
        'js/vacation-packages-event.js',
        'js/vacation-packages.js',
    ];

    public function init()
    {
        $this->sourcePath = __DIR__.'/assets';
        parent::init();
    }
}