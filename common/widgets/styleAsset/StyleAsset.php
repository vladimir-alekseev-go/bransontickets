<?php
namespace common\widgets\styleAsset;

use yii\web\AssetBundle;

use common\models\SiteSettings;

class StyleAsset extends AssetBundle
{
    public const CSS_FILE_NAME = 'color-style-template';

    public function init()
    {
        $SiteSettings = SiteSettings::getData();
        if (!empty($SiteSettings->data)) {
            $data = SiteSettings::getFontsData($SiteSettings->data->font_style);
            if (!empty($data) && $data['css']) {
                $this->css = $data['css'];
            }
        }
        $file = 'css/'.static::CSS_FILE_NAME.'.css';
        $path = __DIR__.'/assets/';

        if (file_exists($path.$file)) {
            $this->css[] = $file.'?time='.filemtime($path.$file);
        }
        
        $this->sourcePath = __DIR__.'/assets';
        parent::init();
        
    }
}

