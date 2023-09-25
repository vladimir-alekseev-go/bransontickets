<?php

namespace common\widgets\scheduleSlider;

use yii\web\AssetBundle;

class ScheduleSliderWidgetAsset extends AssetBundle
{
    public $css = [
        'css/schedule-slider.css'
    ];

    public $js = [
        'js/schedule-slider.js'
    ];

    public function init()
    {
        $this->sourcePath = __DIR__.'/assets';
        parent::init();
    }
}