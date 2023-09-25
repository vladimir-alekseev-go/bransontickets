<?php

namespace frontend\widgets\scheduleSlider;

class ScheduleSliderWidget extends \common\widgets\scheduleSlider\ScheduleSliderWidget
{
    protected function assetRegister()
    {
        $view = $this->getView();
        ScheduleSliderWidgetAsset::register($view);
    }
}
