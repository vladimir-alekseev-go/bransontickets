<?php

namespace common\widgets\yii2CookieConsent;

class CookieConsentAsset extends \dmstr\cookieconsent\assets\CookieConsentAsset
{
    public $css = [
        'cookie-consent.css',
    ];

    public function init()
    {
        $this->sourcePath = __DIR__.'/assets';
        parent::init();
    }
}
