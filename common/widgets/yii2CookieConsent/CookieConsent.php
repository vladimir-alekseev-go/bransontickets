<?php

namespace common\widgets\yii2CookieConsent;

use yii\helpers\Html;
use yii\web\View;

class CookieConsent extends \dmstr\cookieconsent\widgets\CookieConsent
{
    public static function open($view): void
    {
        $view->registerJs(<<<JS
window.addEventListener('load', function () {
    $('.cookie-consent-popup').addClass('open');
});
JS
            , View::POS_END );
    }

    public static function openLink($text, $options = []): string
    {
        return Html::a(
            $text,
            '#',
            array_merge(['onclick' => "$('.cookie-consent-popup').addClass('open');return false;"], $options)
        );
    }
}
