<?php

namespace common\widgets\styleAsset;

use Yii;
use yii\base\Widget;

use common\models\SiteSettings;

class StyleAssetWidget extends Widget
{
    public function run()
    {
        $this->assetRegister();
    }
    
    protected function assetRegister()
    {
        $view = $this->getView();
        StyleAsset::register($view);
    }
}
