<?php

namespace frontend\widgets\vacationPackagesList;

class VacationPackagesListWidget extends \common\widgets\vacationPackagesList\VacationPackagesListWidget
{
    /**
     * Register assets.
     */
    public function assetRegister(): void
    {
        $view = $this->getView();
        VacationPackagesListWidgetAsset::register($view);
    }
}
